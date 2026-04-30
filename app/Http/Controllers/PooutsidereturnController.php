<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Symfony\Component\HttpFoundation\StreamedResponse;
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

        // ⭐ เพิ่ม column สำหรับข้อมูลที่ admin กรอกตอนอนุมัติ
        if (!Schema::hasColumn('Pooutsidereturn', 'shipping_address')) {
            Schema::table('Pooutsidereturn', function (Blueprint $table) {
                $table->text('shipping_address')->nullable();
            });
        }
        if (!Schema::hasColumn('Pooutsidereturn', 'claim_type')) {
            Schema::table('Pooutsidereturn', function (Blueprint $table) {
                $table->string('claim_type', 50)->nullable();
            });
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
                    'shipping_address' => $h->shipping_address ?? '',
                    'claim_type'      => $h->claim_type ?? '',
                    'product'         => $productList->map(fn($d) =>
                        ($d['product_name'] ?: '-') . ' (จำนวน: ' . $d['quantity'] . ')'
                    )->implode('|'),
                    'products'        => $productList->values(),
                    'images'          => $images,
                    'images_evidence' => $images_evidence,
                    'images_pack'     => $images_pack,
                    'stepDates'       => $stepDates,
                    'cancelled_at'    => $this->fmtDt($h->cancelled_at ?? null),
                ];
            })
        );
    }

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
            $now    = Carbon::now('Asia/Bangkok');

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

    /**
     * รูปภาพจาก Google Drive (เดิม) - ใช้สำหรับ thumbnail/รูป
     */
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

    /**
     * ⭐ Stream วิดีโอจาก Google Drive (ปรับปรุงใหม่)
     *
     * ปัญหาที่เคยเจอ:
     * 1. Google Drive `uc?export=download` ไฟล์ใหญ่ >25MB จะคืนหน้า virus scan warning HTML
     * 2. PHP output_buffering ทำให้ headers ส่งผิดลำดับ
     * 3. StreamedResponse + cURL บางครั้งดึง Content-Length ไม่ได้ทำให้ browser ไม่ยอม seek
     *
     * วิธีแก้:
     * - ใช้ thumbnail URL (ใช้ได้กับวิดีโอด้วย) สำหรับ preview
     * - ส่งต่อ Range header แบบ raw + ปิด output_buffering
     * - ใช้ readfile() แทน cURL streaming (เร็วกว่า + เสถียรกว่า)
     * - เพิ่ม fallback: ถ้าได้ HTML กลับมา (virus scan) → parse confirm token แล้วลองอีกรอบ
     */
    public function driveVideo(Request $request)
    {
        $fileId = $request->query('id');
        if (!$fileId) {
            return response('Missing id', 400);
        }

        // ⭐ ปิด output buffering เพื่อให้ stream แบบ real-time
        while (ob_get_level() > 0) { ob_end_clean(); }

        // ส่ง Range header ที่ผู้ใช้ส่งมา
        $rangeHeader = $request->header('Range');

        // ใช้ URL ที่เสถียรสำหรับการ stream วิดีโอ
        // googleusercontent.com มักจะคืน raw bytes ตรงๆ ไม่ติด virus scan
        // แต่ Drive จะ redirect จาก uc?export=download → googleusercontent
        $driveUrl = "https://drive.google.com/uc?export=download&id={$fileId}";

        // ขั้นตอนที่ 1: ทำ HEAD-like request เพื่อหา URL จริง + เช็ค virus scan
        $finalUrl = $this->resolveDriveDownloadUrl($driveUrl);

        if (!$finalUrl) {
            return response('Cannot resolve drive video URL', 502);
        }

        // ขั้นตอนที่ 2: Stream ผ่าน cURL ไปยัง output
        return new StreamedResponse(function () use ($finalUrl, $rangeHeader) {
            $ch = curl_init($finalUrl);

            $reqHeaders = [
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'Accept: */*',
            ];
            if ($rangeHeader) {
                $reqHeaders[] = 'Range: ' . $rangeHeader;
            }

            curl_setopt_array($ch, [
                CURLOPT_HTTPHEADER     => $reqHeaders,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS      => 10,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_TIMEOUT        => 0,
                CURLOPT_CONNECTTIMEOUT => 30,
                CURLOPT_BUFFERSIZE     => 8192, // 8KB chunks
                CURLOPT_WRITEFUNCTION  => function ($ch, $data) {
                    echo $data;
                    if (function_exists('fastcgi_finish_request')) {
                        // ถ้าใช้ FPM ให้ flush ผ่าน FPM
                        @ob_flush();
                    }
                    @flush();
                    return strlen($data);
                },
            ]);

            curl_exec($ch);
            curl_close($ch);
        }, 200, [
            'Content-Type'           => 'video/mp4',
            'Accept-Ranges'          => 'bytes',
            'Cache-Control'          => 'public, max-age=3600',
            'X-Content-Type-Options' => 'nosniff',
            'X-Accel-Buffering'      => 'no', // ⭐ บอก Nginx ห้าม buffer
        ]);
    }

    /**
     * Helper: หา URL จริงสำหรับดาวน์โหลดวิดีโอจาก Drive
     * - ทำ HEAD request ก่อน
     * - ถ้าได้ HTML กลับมา (virus scan) → parse confirm token
     * - คืน URL สุดท้ายที่จะใช้ stream
     */
    private function resolveDriveDownloadUrl(string $url): ?string
    {
        // ลอง request แบบ HEAD ดู Content-Type ก่อน
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_NOBODY         => true,
            CURLOPT_HEADER         => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_HTTPHEADER     => ['User-Agent: Mozilla/5.0'],
        ]);
        curl_exec($ch);
        $effectiveUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        $contentType  = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close($ch);

        // ถ้า Content-Type เป็น HTML → เป็นหน้า virus scan ของ Drive
        // ต้อง parse confirm token แล้วเพิ่มใน URL
        if ($contentType && stripos($contentType, 'text/html') !== false) {
            // ดึง HTML body มา parse
            $ch2 = curl_init($url);
            curl_setopt_array($ch2, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS      => 10,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_TIMEOUT        => 15,
                CURLOPT_HTTPHEADER     => ['User-Agent: Mozilla/5.0'],
                CURLOPT_COOKIEJAR      => '/tmp/drive_cookie_' . md5($url),
                CURLOPT_COOKIEFILE     => '/tmp/drive_cookie_' . md5($url),
            ]);
            $html = curl_exec($ch2);
            curl_close($ch2);

            // ดึง confirm token ออกจาก HTML (รูปแบบใหม่ของ Drive ใช้ form action+input)
            // รูปแบบ 1: ?confirm=XXX
            if (preg_match('/confirm=([a-zA-Z0-9_-]+)/', $html, $m)) {
                return $url . '&confirm=' . $m[1];
            }
            // รูปแบบ 2: <input name="confirm" value="XXX">
            if (preg_match('/name=["\']confirm["\']\s+value=["\']([^"\']+)["\']/i', $html, $m)) {
                return $url . '&confirm=' . $m[1];
            }
            // รูปแบบ 3: action="/uc?..." มี hidden inputs ทั้งหมด
            if (preg_match('/<form[^>]+id=["\']download-form["\'][^>]+action=["\']([^"\']+)["\']/i', $html, $am)) {
                $action = html_entity_decode($am[1]);
                $params = [];
                if (preg_match_all('/<input[^>]+name=["\']([^"\']+)["\'][^>]+value=["\']([^"\']*)["\']/i', $html, $im, PREG_SET_ORDER)) {
                    foreach ($im as $kv) {
                        $params[$kv[1]] = html_entity_decode($kv[2]);
                    }
                }
                if (!empty($params)) {
                    $sep = strpos($action, '?') === false ? '?' : '&';
                    return $action . $sep . http_build_query($params);
                }
            }
            // หา confirm token ไม่เจอ → คืน null
            return null;
        }

        // ถ้าไม่ใช่ HTML → ใช้ URL สุดท้าย (effective_url) ที่ redirect ไปแล้ว
        return $effectiveUrl ?: $url;
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
                ->timeout(180) // ⭐ เพิ่ม timeout เป็น 180s รองรับวิดีโอใหญ่
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
            $shouldNotify = $request->input('notify_line', true);

            // ⭐ รับข้อมูลเพิ่มที่ admin กรอกตอนอนุมัติ
            $shippingAddress = $request->input('shipping_address');
            $claimType       = $request->input('claim_type');

            $allowed = ['processing', 'accept', 'finish', 'cancel'];
            if (!in_array($newStatus, $allowed)) {
                return response()->json(['success' => false, 'message' => 'สถานะไม่ถูกต้อง'], 422);
            }

            $case = DB::table('Pooutsidereturn')->where('return_id', $id)->first();
            if (!$case) {
                return response()->json(['success' => false, 'message' => 'ไม่พบเคส ' . $id], 404);
            }

            $now = Carbon::now('Asia/Bangkok');
            $nowStr = $now->toDateTimeString();
            $updatePayload = ['status' => $newStatus];

            if ($newStatus === 'accept') {
                if (empty($case->step3_at)) {
                    $updatePayload['step3_at'] = $nowStr;
                }
                // ⭐ บันทึกที่อยู่จัดส่ง + ประเภทเคลม ตอน admin อนุมัติ
                if ($shippingAddress !== null) {
                    $updatePayload['shipping_address'] = trim($shippingAddress);
                }
                if ($claimType !== null) {
                    $updatePayload['claim_type'] = trim($claimType);
                }
            } elseif ($newStatus === 'finish') {
                if (empty($case->step4_at)) {
                    $updatePayload['step4_at'] = $nowStr;
                }
                if (empty($case->step5_at)) {
                    $updatePayload['step5_at'] = $nowStr;
                }
            } elseif ($newStatus === 'cancel') {
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

            $fresh = DB::table('Pooutsidereturn')->where('return_id', $id)->first();
            $stepDates = [
                $this->fmtDt($fresh->step1_at ?? $fresh->return_date),
                $this->fmtDt($fresh->step2_at ?? $fresh->return_date),
                $this->fmtDt($fresh->step3_at ?? null),
                $this->fmtDt($fresh->step4_at ?? null),
                $this->fmtDt($fresh->step5_at ?? null),
            ];

            return response()->json([
                'success'          => true,
                'return_id'        => $id,
                'status'           => $newStatus,
                'updated_by'       => $updatedBy,
                'stepDates'        => $stepDates,
                'cancelled_at'     => $this->fmtDt($fresh->cancelled_at ?? null),
                'shipping_address' => $fresh->shipping_address ?? '',
                'claim_type'       => $fresh->claim_type ?? '',
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