<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Pooutsidereturn;
use App\Models\DetailPooutsidereturn;
use Carbon\Carbon;

class PooutsidereturnController extends Controller
{
    // ─── Dashboard View ───────────────────────────────────────────────────────
    public function dashboardreturn()
    {
        return view('pooutside.dashboardreturn');
    }

    // ─── Admin View ───────────────────────────────────────────────────────────
    public function adminpooutside()
    {
        return view('pooutside.adminpooutside');
    }

    // ─── Proxy: PO Detail ─────────────────────────────────────────────────────
    public function getPODetail(Request $request)
    {
        $poNum    = $request->query('PONum');
        $response = Http::get('http://server_update:8000/api/getPODetail', ['PONum' => $poNum]);
        return response()->json($response->json());
    }

    // ─── List All Returns ─────────────────────────────────────────────────────
    public function listReturns()
    {
        $headers = Pooutsidereturn::orderBy('return_date', 'desc')->get();

        return response()->json(
            $headers->map(function ($h) {
                $products = DetailPooutsidereturn::where('return_id', $h->return_id)->get();
                return [
                    'id'       => $h->return_id,
                    'customer' => $h->vendor,
                    'date'     => substr($h->return_date, 0, 10),
                    'po'       => $h->po,
                    'status'   => $h->status,
                    'reason'   => $h->reason,
                    'note'     => $h->note ?? '-',
                    'product'  => $products->map(fn($d) =>
                        $d->product_name . ' (จำนวน: ' . $d->quantity . ')'
                    )->implode("\n"),
                    'products' => $products->map(fn($d) => [
                        'product_name' => $d->product_name,
                        'quantity'     => $d->quantity,
                        'invoice'      => $d->inovice,
                    ]),
                ];
            })
        );
    }

    // ─── Submit New Return Case ───────────────────────────────────────────────
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
            $now    = Carbon::now();

            $returnId = $this->generateReturnId($now, $poNum);

            Pooutsidereturn::create([
                'return_id'   => $returnId,
                'return_date' => $now->toDateTimeString(),
                'po'          => $poNum,
                'vendor'      => $vendor,
                'status'      => 'processing',
                'reason'      => $reason,
                'note'        => $note ?: null,
            ]);

            $items = $request->input('selectedItems');

            foreach ($items as $item) {
                DetailPooutsidereturn::create([
                    'return_id'    => $returnId,
                    'inovice'      => $item['invoice']  ?? null,
                    'product_name' => trim($item['goodName']),
                    'quantity'     => $item['qty'],
                ]);
            }

            // ─── Send LINE Notification ───────────────────────────────────────
            $this->sendLineNotification($returnId, $poNum, $vendor, $reason, $note, $items, $now);

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

    // ─── Approve (processing → accept → finish) ───────────────────────────────
    public function approveReturn(Request $request, string $id)
    {
        try {
            $row = Pooutsidereturn::where('return_id', $id)->firstOrFail();

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

    // ─── Reject → cancel ──────────────────────────────────────────────────────
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

    // ─── Send LINE Notification ───────────────────────────────────────────────
    private function sendLineNotification(
        string $returnId,
        string $poNum,
        string $vendor,
        string $reason,
        string $note,
        array  $items,
        Carbon $now
    ): void {
        $token  = config('services.line.channel_access_token');
        $userId = config('services.line.user_id');

        if (!$token || !$userId) {
            return; // ถ้าไม่มี config ให้ข้ามไป
        }

        // ─── สร้างรายการสินค้า ─────────────────────────────────────────────
        $itemLines = collect($items)->map(function ($item, $i) {
            $name = trim($item['goodName'] ?? '-');
            $qty  = $item['qty'] ?? 0;
            $inv  = $item['invoice'] ?? '-';
            return ($i + 1) . ". {$name}\n   จำนวน: {$qty}  |  Invoice: {$inv}";
        })->implode("\n");

        // ─── สร้างข้อความแจ้งเตือน ────────────────────────────────────────
        $message = implode("\n", [
            "🔔 แจ้งเตือน: เคส Return ใหม่",
            "📅 " . $now->format('d/m/Y H:i'),
            "━━━━━━━━━━━━━━━━━━━━",
            "🏢 บริทัษ : {$vendor}",
            "📦 บิล   : {$poNum}",
            "❗เหตุผล : {$reason}",
            $note ? "📝 หมายเหตุ: {$note}" : "",
            "━━━━━━━━━━━━━━━━━━━━",
            "🛒 รายการสินค้า:",
            $itemLines,
        ]);

        // กรองบรรทัดว่าง (กรณี $note ว่าง)
        $message = preg_replace("/\n{2,}/", "\n", trim($message));

        try {
            Http::withHeaders([
                'Authorization' => "Bearer {$token}",
                'Content-Type'  => 'application/json',
            ])->post('https://api.line.me/v2/bot/message/push', [
                'to'       => $userId,
                'messages' => [
                    ['type' => 'text', 'text' => $message],
                ],
            ]);
        } catch (\Throwable $e) {
            // ไม่ให้ LINE error กระทบ response หลัก — log ไว้เท่านั้น
            \Log::error('LINE notification failed: ' . $e->getMessage());
        }
    }

    // ─── Generate return_id ───────────────────────────────────────────────────
    private function generateReturnId(Carbon $now, string $poNum): string
    {
        $cleanPo = preg_replace('/^PO/i', '', $poNum);

        $monthPrefix = '01' . $now->format('my');

        $lastId = DB::table('Pooutsidereturn')
            ->where('return_id', 'like', "{$monthPrefix}%")
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
}