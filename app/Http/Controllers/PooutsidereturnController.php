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
    // ════════════════════════════════════════════════════════════════════
    //  เพิ่มคอลัมน์ images อัตโนมัติถ้ายังไม่มี (ไม่ต้อง migrate แยก)
    // ════════════════════════════════════════════════════════════════════
    public function __construct()
    {
        if (!Schema::hasColumn('Pooutsidereturn', 'images')) {
            Schema::table('Pooutsidereturn', function (Blueprint $table) {
                $table->text('images')->nullable()->after('note');
            });
        }
    }

    public function dashboardreturn()
    {
        return view('pooutside.dashboardreturn');
    }

    public function adminpooutside()
    {
        return view('pooutside.adminpooutside');
    }

    public function getPODetail(Request $request)
    {
        $poNum    = $request->query('PONum');
        $response = Http::get('http://server_update:8000/api/getPODetail', ['PONum' => $poNum]);
        return response()->json($response->json());
    }

    public function listReturns()
    {
        // ใช้ DB::table แทน Eloquent เพื่อดึงทุก column รวม images
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

                return [
                    'id'       => $h->return_id,
                    'customer' => $h->vendor,
                    'date'     => substr($h->return_date, 0, 10),
                    'po'       => $h->po,
                    'status'   => $h->status,
                    'reason'   => $h->reason,
                    'note'     => $h->note ?? '-',
                    'product'  => $productList->map(fn($d) =>
                        ($d['product_name'] ?: '-') . ' (จำนวน: ' . $d['quantity'] . ')'
                    )->implode('|'),
                    'products' => $productList->values(),
                    'images'   => $images,
                ];
            })
        );
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
            $now    = Carbon::now();

            $returnId = $this->generateReturnId($now, $poNum);

            // ใช้ DB::table เพื่อ insert images โดยไม่ติดปัญหา fillable
            DB::table('Pooutsidereturn')->insert([
                'return_id'   => $returnId,
                'return_date' => $now->toDateTimeString(),
                'po'          => $poNum,
                'vendor'      => $vendor,
                'status'      => 'processing',
                'reason'      => $reason,
                'note'        => $note ?: null,
                'images'      => !empty($images) ? json_encode($images) : null,
            ]);

            foreach ($request->input('selectedItems') as $item) {
                DetailPooutsidereturn::create([
                    'return_id'    => $returnId,
                    'inovice'      => $item['invoice'] ?? '',
                    'product_name' => trim($item['goodName']),
                    'quantity'     => $item['qty'],
                ]);
            }

            // ไม่ส่ง LINE ที่นี่เลย — ให้ frontend เรียก /update-images?final=true
            // ถ้าไม่มีรูป frontend จะส่ง final=true ทันที
            // ถ้ามีรูป frontend จะส่ง final=true หลังรูปครบ

            return response()->json([
                'success'   => true,
                'return_id' => $returnId,
                'vendor'    => $vendor,
                'message'   => "สร้างเคส {$returnId} เรียบร้อยแล้ว",
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

    // ════════════════════════════════════════════════════════════════════
    //  Proxy รูปจาก Google Drive (แก้ CORS)
    //  GET /return/drive-image?id=FILE_ID&sz=w400
    // ════════════════════════════════════════════════════════════════════
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
                // ลอง uc export แทน
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

    // ════════════════════════════════════════════════════════════════════
    //  Proxy: Browser → Laravel → GAS → Google Drive
    //  POST /return/upload-image
    // ════════════════════════════════════════════════════════════════════
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

    // ════════════════════════════════════════════════════════════════════
    //  อัปเดต images หลัง background upload เสร็จ
    //  POST /return/{id}/update-images
    // ════════════════════════════════════════════════════════════════════
    public function updateImages(Request $request, string $id)
    {
        try {
            $images   = $request->input('images', []);
            $isFinal  = $request->boolean('final', false); // true = รูปครบแล้ว ส่ง LINE

            DB::table('Pooutsidereturn')
                ->where('return_id', $id)
                ->update(['images' => json_encode($images)]);

            // ส่ง LINE notification พร้อมรูปเมื่อรูปครบทุกรูป
            if ($isFinal && !empty($images)) {
                $row = DB::table('Pooutsidereturn')->where('return_id', $id)->first();
                if ($row) {
                    $items = DB::table('DetailPooutsidereturn')
                        ->where('return_id', $id)->get()
                        ->map(fn($d) => [
                            'goodName' => $d->product_name ?? '',
                            'qty'      => $d->quantity ?? 0,
                            'invoice'  => $d->inovice ?? '',
                        ])->toArray();

                    $this->sendLineNotification(
                        $id, $row->po, $row->vendor,
                        $row->reason, $row->note ?? '',
                        $items, Carbon::parse($row->return_date),
                        $images
                    );
                }
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
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
            $row->save();

            return response()->json(['success' => true, 'return_id' => $id, 'status' => 'cancel']);

        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ─── Generate return_id ───────────────────────────────────────────────────
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

    // ─── Send LINE Notification ───────────────────────────────────────────────
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

        // เพิ่ม section รูปภาพแบบ grid 3x3 (สูงสุด 9 รูป)
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

                // แบ่งรูปเป็นแถวๆ ละ 3 รูป
                $rows = $photos->chunk(3);
                foreach ($rows as $row) {
                    $rowItems = $row->map(function($img) {
                        $viewUrl = $img['viewUrl'] ?? '';
                        // แปลงเป็น thumbnail URL
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
                            'action'      => [
                                'type'  => 'uri',
                                'uri'   => $viewUrl,
                                'label' => 'ดูรูป',
                            ],
                        ];
                    })->values()->toArray();

                    // เติมช่องว่างถ้าแถวไม่ครบ 3
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