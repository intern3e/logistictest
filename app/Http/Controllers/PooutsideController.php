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

        $data = Pooutside::where('ponum', $ponum)->get();

        return response()->json([
            'success' => true,
            'exists' => $data->count() > 0,
            'data' => $data
        ]);
    }

    // public function detailpooutside($ponum)
    // {
    //     return view('pooutside.detailpooutside', ['ponum' => $ponum]);
    // }
    // public function batchMatch(Request $request)
    // {
    //     try {
    //         $poNumber = $request->input('po_number');
    //         $apiItems = $request->input('api_items'); // Array ของ items จาก API

    //         $cleanPoNumber = preg_replace('/^PO/i', '', $poNumber);

    //         Log::info('=== BATCH MATCH START ===', [
    //             'po_number' => $cleanPoNumber,
    //             'api_items_count' => count($apiItems)
    //         ]);

    //         if (!$cleanPoNumber || empty($apiItems)) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Missing parameters',
    //                 'matches' => []
    //             ]);
    //         }

    //         // 1. ดึง DB items ทั้งหมดของ PO นี้
    //         $dbRecords = Pooutside::where('ponum', $cleanPoNumber)->get();
            
    //         // Group by unique name
    //         $dbItems = $dbRecords->groupBy('name')->map(function($records, $name) {
    //             return [
    //                 'name' => $name,
    //                 'total_qty' => $records->sum(function($r) { return floatval($r->quantity); }),
    //                 'latest_invoice' => $records->sortByDesc('date_invice')->first()->invice ?? '',
    //                 'latest_date' => $records->sortByDesc('date_invice')->first()->date_invice ?? '',
    //                 'records' => $records->map(function($r) {
    //                     return [
    //                         'invoice' => $r->invice,
    //                         'date' => $r->date_invice,
    //                         'quantity' => $r->quantity
    //                     ];
    //                 })->values()->toArray()
    //             ];
    //         })->values()->toArray();

    //         Log::info('DB Items:', ['count' => count($dbItems)]);

    //         // 2. สร้างผลลัพธ์สำหรับ API items ทั้งหมด (default: ไม่มี match)
    //         $results = [];
    //         foreach ($apiItems as $index => $apiItem) {
    //             $results[$index] = [
    //                 'api_name' => $apiItem['name'],
    //                 'api_quantity' => $apiItem['quantity'],
    //                 'complete_flag' => $apiItem['complete_flag'] ?? 'N',
    //                 'matched' => false,
    //                 'db_name' => null,
    //                 'total_received' => 0,
    //                 'invoice' => '',
    //                 'date_invice' => '',
    //                 'records' => []
    //             ];
    //         }

    //         // 3. ⭐ วน DB ทีละตัว → หา API ที่ใกล้เคียงที่สุด → จับคู่
    //         $usedApiIndexes = []; // เก็บ index ของ API ที่ถูกจับคู่แล้ว

    //         foreach ($dbItems as $dbItem) {
    //             $dbName = $dbItem['name'];
                
    //             $bestApiIndex = null;
    //             $highestScore = 0;

    //             // หา API item ที่ใกล้เคียงที่สุด (ที่ยังไม่ถูกจับคู่)
    //             foreach ($apiItems as $apiIndex => $apiItem) {
    //                 // ข้าม API ที่ถูกจับคู่แล้ว
    //                 if (in_array($apiIndex, $usedApiIndexes)) {
    //                     continue;
    //                 }
                    
    //                 $apiName = $apiItem['name'];
    //                 $score = $this->calculateMatchScore($apiName, $dbName);

    //                 if ($score > $highestScore) {
    //                     $highestScore = $score;
    //                     $bestApiIndex = $apiIndex;
    //                 }
    //             }

    //             // ⭐ ถ้า score >= 50 (DB keywords อยู่ใน API มากพอ) → จับคู่
    //             if ($bestApiIndex !== null && $highestScore >= 50) {
    //                 $usedApiIndexes[] = $bestApiIndex;
                    
    //                 $results[$bestApiIndex] = [
    //                     'api_name' => $apiItems[$bestApiIndex]['name'],
    //                     'api_quantity' => $apiItems[$bestApiIndex]['quantity'],
    //                     'complete_flag' => $apiItems[$bestApiIndex]['complete_flag'] ?? 'N',
    //                     'matched' => true,
    //                     'match_score' => $highestScore,
    //                     'db_name' => $dbName,
    //                     'total_received' => $dbItem['total_qty'],
    //                     'invoice' => $dbItem['latest_invoice'],
    //                     'date_invice' => $dbItem['latest_date'],
    //                     'records' => $dbItem['records']
    //                 ];

    //                 Log::info("✅ MATCHED", [
    //                     'db' => substr($dbName, 0, 40),
    //                     'api' => substr($apiItems[$bestApiIndex]['name'], 0, 40),
    //                     'score' => $highestScore
    //                 ]);
    //             }
    //         }

    //         Log::info('=== BATCH MATCH COMPLETE ===', [
    //             'matched' => count($usedApiIndexes),
    //             'unmatched' => count($apiItems) - count($usedApiIndexes)
    //         ]);

    //         return response()->json([
    //             'success' => true,
    //             'matches' => $results
    //         ]);

    //     } catch (\Exception $e) {
    //         Log::error('Batch Match Error:', ['message' => $e->getMessage()]);

    //         return response()->json([
    //             'success' => false,
    //             'error' => $e->getMessage(),
    //             'matches' => []
    //         ]);
    //     }
    // }

    // /**
    //  * ⭐ คำนวณ Match Score ระหว่าง API name และ DB name
    //  * หลักการ: ดูว่า keywords ใน DB มีอยู่ใน API ครบไหม
    //  */
    // private function calculateMatchScore($apiName, $dbName)
    // {
    //     $apiNorm = $this->normalize($apiName);
    //     $dbNorm = $this->normalize($dbName);

    //     // 1. ถ้า normalized เหมือนกันเลย → 100
    //     if ($apiNorm === $dbNorm) {
    //         return 100;
    //     }

    //     // 2. ⭐⭐⭐ เช็คว่า keywords ใน DB มีอยู่ใน API ครบไหม ⭐⭐⭐
    //     $dbWords = $this->extractWords($dbName);
    //     $apiUpper = strtoupper($apiName);
        
    //     if (count($dbWords) > 0) {
    //         $matchedCount = 0;
    //         foreach ($dbWords as $dbWord) {
    //             // เช็คว่า DB word อยู่ใน API string ไหม
    //             if (strpos($apiUpper, $dbWord) !== false) {
    //                 $matchedCount++;
    //             }
    //         }
            
    //         $matchPercent = ($matchedCount / count($dbWords)) * 100;
            
    //         Log::info("Keyword match check", [
    //             'db_words' => $dbWords,
    //             'matched' => $matchedCount . '/' . count($dbWords),
    //             'percent' => $matchPercent
    //         ]);
            
    //         // ถ้า DB keywords อยู่ใน API ครบ 100% → score 95
    //         if ($matchPercent == 100) {
    //             return 95;
    //         }
            
    //         // ถ้า >= 80% → score 80
    //         if ($matchPercent >= 80) {
    //             return 80;
    //         }
            
    //         // ถ้า >= 60% → score 60
    //         if ($matchPercent >= 60) {
    //             return 60;
    //         }
    //     }

    //     // 3. ดึง Model Number มาเทียบ
    //     $apiModel = $this->extractModelNumber($apiName);
    //     $dbModel = $this->extractModelNumber($dbName);

    //     if ($apiModel && $dbModel && $apiModel === $dbModel) {
    //         return 85;
    //     }

    //     // 4. ใช้ similar_text เป็น fallback
    //     $similarity = 0;
    //     similar_text($apiNorm, $dbNorm, $similarity);

    //     return round($similarity, 2);
    // }

    // /**
    //  * Normalize string สำหรับเปรียบเทียบ
    //  */
    // private function normalize($str)
    // {
    //     // ลบ code ท้าย
    //     $str = preg_replace('/\+\+.*?\+\+/', '', $str);
    //     $str = preg_replace('/\-\/\-.*?\-\/\-/', '', $str);
    //     $str = preg_replace('/[^A-Z0-9]/i', '', $str);
    //     return strtoupper($str);
    // }

    // /**
    //  * ดึง Model Number
    //  */
    // private function extractModelNumber($name)
    // {
    //     $patterns = [
    //         '/\b([A-Z]\d+-\d+-\d+)/i',               // A26-30-10
    //         '/\b(\d+-\d+-\d+[A-Z]*)/i',              // 3-9900-1P
    //         '/\b(\d+-\d+[A-Z]*-\d+[A-Z]*)/i',        // 3-9900-1P variant
    //         '/\b(NM8[N]?-\d+[A-Z]?)/i',              // NM8-125S
    //         '/\b([A-Z]\d{3}[A-Z]?-[A-Z]{2,}\d+)/i',  // C200H-OC225
    //         '/\b([A-Z]{2,}\d+-[A-Z0-9-]+)/i',        // General
    //     ];

    //     foreach ($patterns as $pattern) {
    //         if (preg_match($pattern, $name, $matches)) {
    //             return strtoupper($matches[1]);
    //         }
    //     }

    //     return null;
    // }

    // /**
    //  * ดึงคำสำคัญจากชื่อ
    //  */
    // private function extractWords($name)
    // {
    //     $clean = preg_replace('/\+\+.*?\+\+/', '', $name);
    //     $clean = preg_replace('/\-\/\-.*?\-\/\-/', '', $clean);
    //     $clean = preg_replace('/["\'\(\)\[\]<>]/', ' ', $clean);
        
    //     preg_match_all('/[A-Z0-9][A-Z0-9\-]+/i', strtoupper($clean), $matches);
        
    //     $excludeWords = ['THE', 'NEW', 'MODEL', 'IS', 'FOR', 'WITH', 'AND', 'OR'];
        
    //     $words = array_filter($matches[0], function($w) use ($excludeWords) {
    //         return strlen($w) >= 2 && !in_array($w, $excludeWords);
    //     });

    //     return array_values(array_unique($words));
    // }

    // private function calculateExpectedDate($dateInvice)
    // {
    //     if (!$dateInvice) return null;
        
    //     try {
    //         $dateArray = explode('/', $dateInvice);
    //         if (count($dateArray) == 3) {
    //             $carbonDate = \Carbon\Carbon::createFromDate(
    //                 $dateArray[2] - 543, $dateArray[1], $dateArray[0]
    //             );
    //             $expectedDate = $carbonDate->addDays(15);
    //             return $expectedDate->format('d/m') . '/' . ($expectedDate->year + 543);
    //         }
    //     } catch (\Exception $e) {
    //         Log::error('Error calculating expected date: ' . $e->getMessage());
    //     }
    //     return null;
    // }

    public function pull()
    {
        ini_set('max_execution_time', 600);
        set_time_limit(600);
        
        $today = Carbon::now()->format('Y/m/d');
        $url = "https://docs.google.com/spreadsheets/d/10C7TH4CUsE8AZmngq4G0PYti_IcEzjRHB2EQiCDwsh0/export?format=csv&gid=0";
        
        $csv = array_map('str_getcsv', file($url));
        unset($csv[0]);

        $inserted = 0;
        $updated = 0;
        $errors = [];

        foreach ($csv as $row) {
            if ($row[0] !== $today) continue;

            DB::table('pooutside')->insert([
                'date_invice' => Carbon::createFromFormat('Y/m/d', $row[0])->format('Y-m-d'),
                'invice'      => $row[1],
                'name'        => $row[2],
                'quantity'    => $row[3],
                'ponum'       => $row[4],
                'idvendor'    => null,
                'name_vendor' => null
            ]);
            $inserted++;
        }

        $poNumbers = collect($csv)
            ->filter(fn($row) => $row[0] === $today)
            ->pluck(4)->unique()->values();

        foreach ($poNumbers as $poNumber) {
            try {
                usleep(200000);
                $apiUrl = "http://server_update:8000/api/getPODetail?PONum={$poNumber}";
                $response = Http::timeout(10)->get($apiUrl);

                if ($response->successful()) {
                    $poDetail = $response->json();
                    if (isset($poDetail['VendorName']) && isset($poDetail['VendorCode'])) {
                        $affectedRows = DB::table('pooutside')
                            ->where('ponum', $poNumber)
                            ->where('date_invice', $today)
                            ->whereNull('idvendor')
                            ->update([
                                'name_vendor' => $poDetail['VendorName'],
                                'idvendor'    => $poDetail['VendorCode']
                            ]);
                        if ($affectedRows > 0) $updated += $affectedRows;
                    }
                }
            } catch (\Exception $e) {
                $errors[] = "PO {$poNumber}: " . $e->getMessage();
            }
        }

        return response()->json([
            'status' => true,
            'message' => "ดึงข้อมูลสำเร็จ {$inserted} รายการ, อัพเดท Vendor {$updated} รายการ",
            'errors' => count($errors) > 0 ? $errors : null
        ]);
    }
}