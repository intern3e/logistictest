<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Pooutside;
use Illuminate\Support\Facades\Log;

class PooutsideController extends Controller
{
    public function dashboard(Request $request)
    {
        return view('pooutside.dashboard');
    }
public function checkLocalPO(Request $request)
    {
        $ponum = $request->ponum;

        // ดึงข้อมูลทั้งหมดที่ตรงกับ PO number
        $data = Pooutside::where('ponum', $ponum)
            ->orderBy('date_invoice', 'desc') 
            ->get();

        return response()->json([
            'success' => true,
            'exists' => $data->count() > 0,
            'data' => $data,
            'count' => $data->count()
        ]);
    }

    public function getPODetailFromERP($poNum)
    {
        try {
            $response = Http::get("http://server_update:8000/api/getPODetail", [
                'PONum' => $poNum
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error fetching PO detail from ERP: ' . $e->getMessage());
            return null;
        }
    }
   public function pull()
{
    ini_set('max_execution_time', 600);
    set_time_limit(600);

    // ⭐ วันที่ปัจจุบัน (วันที่กดปุ่ม)
    $today = Carbon::now()->format('Y/m/d'); // 2026/02/13

    $url = "https://docs.google.com/spreadsheets/d/1wRmbT3ZkN1Td-EoLfwRBCm5LxxUvkNwPkEo5UZxVysE/export?format=csv&gid=0";

    $rows = array_map('str_getcsv', file($url));
    unset($rows[0]); // ลบ header

    $inserted = 0;
    $errors = [];

    foreach ($rows as $index => $row) {
        try {
            // เช็คว่ามี column ครบ
            if (!isset($row[0], $row[1], $row[2], $row[3], $row[4])) {
                continue;
            }

            $date_invoice = trim($row[0]);
            $invoice      = trim($row[1]);
            $name         = trim($row[2]);
            $quantity     = trim($row[3]);
            $ponum        = trim($row[4]);

            // ⭐ ข้ามถ้า ponum เป็น null หรือว่าง
            if (empty($ponum)) {
                continue;
            }

            // ⭐ ดึงเฉพาะวันที่วันนี้เท่านั้น
            if ($date_invoice !== $today) {
                continue;
            }

            $date = Carbon::createFromFormat('Y/m/d', $date_invoice)
                ->format('Y-m-d');

            Pooutside::create([
                'date_invoice' => $date,
                'invoice'      => $invoice,
                'name'        => $name,
                'quantity'    => $quantity,
                'ponum'       => $ponum
            ]);

            $inserted++;

        } catch (\Exception $e) {
            $errors[] = "Row {$index}: " . $e->getMessage();
        }
    }

    return response()->json([
        'status' => true,
        'message' => "ดึง PO วันที่ " . Carbon::now()->format('d/m/Y') . " สำเร็จ {$inserted} รายการ",
        'inserted' => $inserted,
        'errors' => $errors
    ]);
}
}