<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Models\Pooutsidereturn;
use App\Models\DetailPooutsidereturn;
use Carbon\Carbon;

class PooutsidereturnController extends Controller
{
    public function __construct()
    {
        if (!Schema::hasColumn('Pooutsidereturn', 'images')) {
            Schema::table('Pooutsidereturn', function (Blueprint $table) {
                $table->text('images')->nullable()->after('note');
            });
        }
        if (!Schema::hasColumn('Pooutsidereturn', 'images_evidence')) {
            Schema::table('Pooutsidereturn', function (Blueprint $table) {
                $table->text('images_evidence')->nullable()->after('images');
            });
        }
        if (!Schema::hasColumn('Pooutsidereturn', 'images_pack')) {
            Schema::table('Pooutsidereturn', function (Blueprint $table) {
                $table->text('images_pack')->nullable()->after('images_evidence');
            });
        }

        // ⭐ เพิ่ม column เวลา step ถ้ายังไม่มี (fail-safe ถ้ายังไม่ได้รัน migration)
        $stepColumns = [
            'step1_at', 'step2_at', 'step3_at',
            'step4_at', 'step5_at', 'cancelled_at',
        ];
        foreach ($stepColumns as $col) {
            if (!Schema::hasColumn('Pooutsidereturn', $col)) {
                Schema::table('Pooutsidereturn', function (Blueprint $table) use ($col) {
                    $table->timestamp($col)->nullable();
                });
            }
        }
    }

    public function dashboardreturn()
    {
        return view('pooutside.dashboardreturn');
    }

    public function getPODetail(Request $request)
    {
        $poNum    = $request->query('PONum');
        $response = Http::get('http://server_update:8000/api/getPODetail', ['PONum' => $poNum]);
        return response()->json($response->json());
    }

    public function listReturns()
    {
        $headers = DB::table('Pooutsidereturn')
            ->orderBy('return_date', 'desc')
            ->get();

        return response()->json(
            $headers->map(function ($h) {
                $products = DB::table('DetailPooutsidereturn')
                    ->where('return_id', $h->return_id)
                    ->get();

                $productList = $products->map(fn($d) => [
                    'product_name' => $d->product_name ?? '',
                    'quantity'     => $d->quantity ?? 0,
                    'invoice'      => $d->inovice ?? '',
                ]);

                $images = [];
                if (!empty($h->images)) {
                    $decoded = json_decode($h->images, true);
                    if (is_array($decoded)) $images = $decoded;
                }

                $images_evidence = [];
                if (!empty($h->images_evidence)) {
                    $decoded = json_decode($h->images_evidence, true);
                    if (is_array($decoded)) $images_evidence = $decoded;
                }

                $images_pack = [];
                if (!empty($h->images_pack)) {
                    $decoded = json_decode($h->images_pack, true);
                    if (is_array($decoded)) $images_pack = $decoded;
                }

                // ⭐ สร้าง stepDates array จาก column เวลาใน DB
                // ถ้า column ยังไม่มี (เคสเก่า) ใช้ return_date เป็น fallback
                $stepDates = [
                    $this->fmtDt($h->step1_at ?? $h->return_date),
                    $this->fmtDt($h->step2_at ?? $h->return_date),
                    $this->fmtDt($h->step3_at ?? null),
                    $this->fmtDt($h->step4_at ?? null),
                    $this->fmtDt($h->step5_at ?? null),
                ];

                return [
                    'id'              => $h->return_id,
                    'customer'        => $h->vendor,
                    'date'            => substr($h->return_date, 0, 10),
                    'po'              => $h->po,
                    'status'          => $h->status,
                    'reason'          => $h->reason,
                    'note'            => $h->note ?? '-',
                    'product'         => $productList->map(fn($d) =>
                        ($d['product_name'] ?: '-') . ' (จำนวน: ' . $d['quantity'] . ')'
                    )->implode('|'),
                    'products'        => $productList->values(),
                    'images'          => $images,
                    'images_evidence' => $images_evidence,
                    'images_pack'     => $images_pack,
                    // ⭐ ส่ง stepDates เป็น "Y-m-d H:i:s" ให้ frontend แสดงใน timeline
                    'stepDates'       => $stepDates,
                    'cancelled_at'    => $this->fmtDt($h->cancelled_at ?? null),
                ];
            })
        );
    }

    /**
     * Helper: format datetime เป็น "Y-m-d H:i:s" หรือ null
     */
    private function fmtDt($datetime): ?string
    {
        if (empty($datetime)) return null;
        try {
            return Carbon::parse($datetime)->format('Y-m-d H:i:s');
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function submitReturn(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'poNum'                    => 'required|string',
            'vendor'                   => 'required|string',
            'reason'                   => 'required|string',
            'selectedItems'            => 'required|array|min:1',
            'selectedItems.*.goodName' => 'required|string',
            'selectedItems.*.qty'      => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        try {
            $poNum  = trim($request->input('poNum'));
            $vendor = trim($request->input('vendor'));
            $reason = $request->input('reason');
            $note   = $request->input('note', '');
            $images = $request->input('images', []);
            $now    = Carbon::now('Asia/Bangkok'); // ตั้งเวลาไทยให้ถูกต้อง

            $returnId = $this->generateReturnId($now, $poNum);

            DB::table('Pooutsidereturn')->insert([
                'return_id'   => $returnId,
                'return_date' => $now->toDateTimeString(),
                'po'          => $poNum,
                'vendor'      => $vendor,
                'status'      => 'processing',
                'reason'      => $reason,
                'note'        => $note ?: null,
                'images'      => !empty($images) ? json_encode($images) : null,
                // ⭐ บันทึกเวลา step1 และ step2 ตอนสร้างเคส (เก็บถาวร)
                'step1_at'    => $now->toDateTimeString(),
                'step2_at'    => $now->toDateTimeString(),
            ]);

            $selectedItems = $request->input('selectedItems');
            foreach ($selectedItems as $item) {
                DetailPooutsidereturn::create([
                    'return_id'    => $returnId,
                    'inovice'      => $item['invoice'] ?? '',
                    'product_name' => trim($item['goodName']),
                    'quantity'     => $item['qty'],
                ]);
            }

            // ส่งการแจ้งเตือน Line ทันทีที่สร้างเคส (เพื่อให้ส่งครั้งเดียวจบที่นี่)
            // เช็คว่า notify_line ไม่ได้ถูกสั่งปิด
            if ($request->input('notify_line', true) !== false) {
                $this->sendLineNotification(
                    $returnId, $poNum, $vendor,
                    $reason, $note, $selectedItems, $now,
                    $images
                );
            }

            return response()->json([
                'success'   => true,
                'return_id' => $returnId,
                'vendor'    => $vendor,
                'message'   => "สร้างเคส {$returnId} เรียบร้อยแล้ว",
                // ⭐ ส่ง stepDates กลับให้ frontend ทันที
                'stepDates' => [
                    $now->format('Y-m-d H:i:s'),
                    $now->format('Y-m-d H:i:s'),
                    null, null, null,
                ],
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => basename($e->getFile()),
            ], 500);
        }
    }

    public function driveImage(Request $request)
    {
        $fileId = $request->query('id');
        $sz     = $request->query('sz', 'w400');

        if (!$fileId) {
            return response('Missing id', 400);
        }

        try {
            $url      = "https://drive.google.com/thumbnail?id={$fileId}&sz={$sz}";
            $response = Http::withoutVerifying()->timeout(15)->get($url);

            if (!$response->successful()) {
                $url      = "https://drive.google.com/uc?export=view&id={$fileId}";
                $response = Http::withoutVerifying()->timeout(15)->get($url);
            }

            $contentType = $response->header('Content-Type') ?: 'image/jpeg';

            return response($response->body(), 200)
                ->header('Content-Type', $contentType)
                ->header('Cache-Control', 'public, max-age=86400');

        } catch (\Exception $e) {
            return response('Image load failed: ' . $e->getMessage(), 500);
        }
    }

    public function uploadToGAS(Request $request)
    {
        $gasUrl   = $request->input('gasUrl');
        $image    = $request->input('image');
        $filename = $request->input('filename', 'image_' . time() . '.jpg');
        $mimeType = $request->input('mimeType', 'image/jpeg');

        if (!$gasUrl || !$image) {
            return response()->json(['success' => false, 'error' => 'Missing gasUrl or image']);
        }

        try {
            $response = Http::withoutVerifying()
                ->timeout(60)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($gasUrl, [
                    'image'    => $image,
                    'filename' => $filename,
                    'mimeType' => $mimeType,
                ]);

            $body = $response->json();

            if (!$body) {
                return response()->json([
                    'success' => false,
                    'error'   => 'GAS returned non-JSON: ' . substr($response->body(), 0, 300),
                ]);
            }

            return response()->json($body);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function updateImages(Request $request, string $id)
    {
        try {
            $images          = $request->input('images', []);
            $images_evidence = $request->input('images_evidence', null);
            $images_pack     = $request->input('images_pack', null);
            $isFinal         = $request->boolean('final', false);
            
            // ยกเลิกการส่งแจ้งเตือนซ้ำที่นี่ (เพราะส่งไปแล้วตอน submitReturn)
            // เพื่อแก้ปัญหาการแจ้งเตือนซ้ำ 2 ครั้ง

            $updateData = ['images' => json_encode($images)];

            if ($images_evidence !== null) {
                $updateData['images_evidence'] = json_encode($images_evidence);
            }
            if ($images_pack !== null) {
                $updateData['images_pack'] = json_encode($images_pack);
            }

            DB::table('Pooutsidereturn')
                ->where('return_id', $id)
                ->update($updateData);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function updateStatus(Request $request, string $id)
    {
        try {
            $newStatus = $request->input('status');
            $updatedBy = $request->input('updated_by', 'admin');
            
            // เช็ค parameter notify_line อย่างเคร่งครัด
            $shouldNotify = $request->input('notify_line', true);

            $allowed = ['processing', 'accept', 'finish', 'cancel'];
            if (!in_array($newStatus, $allowed)) {
                return response()->json(['success' => false, 'message' => 'สถานะไม่ถูกต้อง'], 422);
            }

            // ⭐ ดึงข้อมูลเคสปัจจุบันมาก่อน เพื่อเช็คว่าเวลา step ไหนถูกเก็บไปแล้วบ้าง
            $case = DB::table('Pooutsidereturn')->where('return_id', $id)->first();
            if (!$case) {
                return response()->json(['success' => false, 'message' => 'ไม่พบเคส ' . $id], 404);
            }

            $now = Carbon::now('Asia/Bangkok');
            $nowStr = $now->toDateTimeString();
            $updatePayload = ['status' => $newStatus];

            // ⭐ บันทึกเวลาตาม step ที่เปลี่ยน → เก็บครั้งเดียวถาวร
            // ถ้าเคยเก็บแล้ว (ไม่ว่างเปล่า) จะไม่เขียนทับ
            if ($newStatus === 'accept') {
                // กด ✓ อนุมัติ → เก็บเวลา step3
                if (empty($case->step3_at)) {
                    $updatePayload['step3_at'] = $nowStr;
                }
            } elseif ($newStatus === 'finish') {
                // กด 📦 จัดของพร้อมปิดเคส → เก็บเวลา step4 + step5 (จัดของ + ปิดเคส พร้อมกัน)
                if (empty($case->step4_at)) {
                    $updatePayload['step4_at'] = $nowStr;
                }
                if (empty($case->step5_at)) {
                    $updatePayload['step5_at'] = $nowStr;
                }
            } elseif ($newStatus === 'cancel') {
                // กด ✕ ยกเลิก → เก็บเวลา cancel
                if (empty($case->cancelled_at)) {
                    $updatePayload['cancelled_at'] = $nowStr;
                }
            }

            $updated = DB::table('Pooutsidereturn')
                ->where('return_id', $id)
                ->update($updatePayload);

            if (!$updated) {
                return response()->json(['success' => false, 'message' => 'ไม่พบเคส ' . $id], 404);
            }

            // ถ้าหน้าบ้านสั่งมาว่า notify_line: false (เช่นจากปุ่มสถานะ) จะไม่ทำงานในส่วนแจ้งเตือน
            if ($shouldNotify !== false) {
                // ถ้าในอนาคตต้องการให้ปุ่มสถานะแจ้งเตือน ก็สามารถใส่โค้ดตรงนี้ได้
            }

            // ⭐ ส่ง stepDates ชุดใหม่กลับให้ frontend อัปเดต UI ทันที
            $fresh = DB::table('Pooutsidereturn')->where('return_id', $id)->first();
            $stepDates = [
                $this->fmtDt($fresh->step1_at ?? $fresh->return_date),
                $this->fmtDt($fresh->step2_at ?? $fresh->return_date),
                $this->fmtDt($fresh->step3_at ?? null),
                $this->fmtDt($fresh->step4_at ?? null),
                $this->fmtDt($fresh->step5_at ?? null),
            ];

            return response()->json([
                'success'      => true,
                'return_id'    => $id,
                'status'       => $newStatus,
                'updated_by'   => $updatedBy,
                'stepDates'    => $stepDates,
                'cancelled_at' => $this->fmtDt($fresh->cancelled_at ?? null),
            ]);

        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function approveReturn(Request $request, string $id)
    {
        try {
            $row  = Pooutsidereturn::where('return_id', $id)->firstOrFail();
            $next = match ($row->status) {
                'processing' => 'accept',
                'accept'     => 'finish',
                default      => null,
            };

            if (!$next) {
                return response()->json(['success' => false, 'message' => 'ไม่สามารถอนุมัติเคสนี้ได้'], 422);
            }

            $row->status = $next;

            // ⭐ บันทึกเวลาเมื่อ approve ผ่าน method นี้ด้วย
            $now = Carbon::now('Asia/Bangkok');
            if ($next === 'accept' && empty($row->step3_at)) {
                $row->step3_at = $now;
            } elseif ($next === 'finish') {
                if (empty($row->step4_at)) $row->step4_at = $now;
                if (empty($row->step5_at)) $row->step5_at = $now;
            }

            $row->save();

            return response()->json(['success' => true, 'return_id' => $id, 'status' => $next]);

        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function rejectReturn(Request $request, string $id)
    {
        try {
            $row = Pooutsidereturn::where('return_id', $id)->firstOrFail();

            if (!in_array($row->status, ['processing', 'accept'])) {
                return response()->json(['success' => false, 'message' => 'ไม่สามารถยกเลิกเคสนี้ได้'], 422);
            }

            $row->status = 'cancel';

            // ⭐ บันทึกเวลา cancel
            if (empty($row->cancelled_at)) {
                $row->cancelled_at = Carbon::now('Asia/Bangkok');
            }

            $row->save();

            return response()->json(['success' => true, 'return_id' => $id, 'status' => 'cancel']);

        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function generateReturnId(Carbon $now, string $poNum): string
    {
        $cleanPo = preg_replace('/^PO/i', '', $poNum);

        $lastId = DB::table('Pooutsidereturn')
            ->where('return_id', 'like', "{$now->format('dmy')}%")
            ->orderBy('return_id', 'desc')
            ->value('return_id');

        $nextSeq = 1;
        if ($lastId) {
            $parts   = explode('-', $lastId);
            $nextSeq = (int) end($parts) + 1;
        }

        return $now->format('dmy')
            . '-' . $now->format('His')
            . '-' . $cleanPo
            . '-' . str_pad($nextSeq, 5, '0', STR_PAD_LEFT);
    }

    private function sendLineNotification(
        string $returnId, string $poNum, string $vendor,
        string $reason, ?string $note, array $items, Carbon $now,
        array $images = []
    ): void {
        $token  = config('services.line.channel_access_token');
        $userId = config('services.line.user_id');
        if (!$token || !$userId) return;

        $note     = $note ?? '';
        $itemRows = collect($items)->map(function ($item, $i) {
            $name = trim($item['goodName'] ?? '-');
            $qty  = $item['qty'] ?? 0;
            $inv  = $item['invoice'] ?? '-';
            return [
                'type' => 'box', 'layout' => 'vertical', 'margin' => 'sm',
                'contents' => [
                    ['type'=>'text','text'=>($i+1).". {$name}",'size'=>'sm','color'=>'#222222','wrap'=>true,'weight'=>'bold'],
                    ['type'=>'box','layout'=>'horizontal','contents'=>[
                        ['type'=>'text','text'=>"จำนวน: {$qty}",'size'=>'xs','color'=>'#888888','flex'=>1],
                        ['type'=>'text','text'=>"Invoice: {$inv}",'size'=>'xs','color'=>'#888888','flex'=>2,'align'=>'start'],
                    ]],
                ],
            ];
        })->values()->toArray();

        $infoRows = array_values(array_filter([
            $this->flexInfoRow('🏢 บริษัท', $vendor),
            $this->flexInfoRow('📦 บิล', $poNum),
            $this->flexInfoRow('❗เหตุผล', $reason),
            $note ? $this->flexInfoRow('📝 หมายเหตุ', $note) : null,
        ]));

        $bodyContents = [
            ['type'=>'box','layout'=>'horizontal','contents'=>[
                ['type'=>'text','text'=>'🔔 เคส Return ใหม่','weight'=>'bold','size'=>'lg','color'=>'#1428A0','flex'=>1,'wrap'=>true],
                ['type'=>'text','text'=>$now->format('d/m/Y H:i'),'size'=>'xs','color'=>'#aaaaaa','align'=>'end','gravity'=>'top','flex'=>0],
            ]],
            ['type'=>'separator','margin'=>'md'],
            ['type'=>'box','layout'=>'vertical','margin'=>'md','spacing'=>'sm','contents'=>$infoRows],
            ['type'=>'separator','margin'=>'md'],
            ['type'=>'text','text'=>'🛒 รายการสินค้า','weight'=>'bold','size'=>'sm','color'=>'#1428A0','margin'=>'md'],
            ['type'=>'box','layout'=>'vertical','margin'=>'sm','spacing'=>'sm','contents'=>$itemRows],
        ];

        if (!empty($images)) {
            $photos = collect($images)
                ->filter(fn($img) => !empty($img['viewUrl']))
                ->take(9)
                ->values();

            if ($photos->count() > 0) {
                $bodyContents[] = ['type'=>'separator','margin'=>'md'];
                $bodyContents[] = [
                    'type'   => 'text',
                    'text'   => '📷 รูปภาพประกอบ ('.$photos->count().' รูป)',
                    'weight' => 'bold',
                    'size'   => 'sm',
                    'color'  => '#1428A0',
                    'margin' => 'md',
                ];

                $rows = $photos->chunk(3);
                foreach ($rows as $row) {
                    $rowItems = $row->map(function($img) {
                        $viewUrl = $img['viewUrl'] ?? '';
                        if (preg_match('/\/d\/([^\/]+)\//', $viewUrl, $m)) {
                            $thumbUrl = 'https://drive.google.com/thumbnail?id='.$m[1].'&sz=w400';
                        } else {
                            $thumbUrl = $img['thumbUrl'] ?? $viewUrl;
                        }
                        return [
                            'type'        => 'image',
                            'url'         => $thumbUrl,
                            'flex'        => 1,
                            'aspectRatio' => '1:1',
                            'aspectMode'  => 'cover',
                            'size'        => 'full',
                            'action'      => ['type'=>'uri','uri'=>$viewUrl,'label'=>'ดูรูป'],
                        ];
                    })->values()->toArray();

                    while (count($rowItems) < 3) {
                        $rowItems[] = ['type'=>'filler'];
                    }

                    $bodyContents[] = [
                        'type'     => 'box',
                        'layout'   => 'horizontal',
                        'margin'   => 'xs',
                        'spacing'  => 'xs',
                        'contents' => $rowItems,
                    ];
                }
            }
        }

        $bubble = [
            'type' => 'bubble', 'size' => 'giga',
            'body' => [
                'type' => 'box', 'layout' => 'vertical',
                'paddingAll' => '20px', 'backgroundColor' => '#ffffff',
                'contents' => $bodyContents,
            ],
        ];

        try {
            Http::withHeaders([
                'Authorization' => "Bearer {$token}",
                'Content-Type'  => 'application/json',
            ])->post('https://api.line.me/v2/bot/message/push', [
                'to'       => $userId,
                'messages' => [['type'=>'flex','altText'=>"🔔 เคส Return ใหม่ | บิล {$poNum} | {$vendor}",'contents'=>$bubble]],
            ]);
        } catch (\Throwable $e) {
            \Log::error('LINE notification failed: ' . $e->getMessage());
        }
    }

    private function flexInfoRow(string $label, string $value): array
    {
        return [
            'type' => 'box', 'layout' => 'vertical', 'margin' => 'sm',
            'contents' => [
                ['type'=>'text','text'=>$label,'size'=>'xs','color'=>'#888888','wrap'=>false],
                ['type'=>'text','text'=>$value,'size'=>'sm','color'=>'#222222','wrap'=>true,'margin'=>'xs'],
            ],
        ];
    }

}