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
        $poData = Pooutside::orderBy('date_invice', 'desc')->get();
        
        return view('pooutside.dashboard', compact('poData'));
    }

    private function calculateExpectedDate($dateInvice)
        {
            if (!$dateInvice) {
                return null;
            }
            
            try {
                $dateArray = explode('/', $dateInvice);
                
                if (count($dateArray) == 3) {
                    $carbonDate = \Carbon\Carbon::createFromDate(
                        $dateArray[2] - 543,  // year (แปลง พ.ศ. เป็น ค.ศ.)
                        $dateArray[1],        // month
                        $dateArray[0]         // day
                    );
                    
                    // บวก 15 วัน
                    $expectedDate = $carbonDate->addDays(15);
                    
                    // คืนค่าเป็น format d/m/Y (พ.ศ.)
                    return $expectedDate->format('d/m') . '/' . ($expectedDate->year + 543);
                }
            } catch (\Exception $e) {
                Log::error('Error calculating expected date: ' . $e->getMessage());
            }
            
            return null;
        }
    public function detailpooutside($ponum)
    {
        return view('pooutside.detailpooutside', ['ponum' => $ponum]);
    }
private function cleanProductName($name)
{
    $cleaned = $name;
    
    // 1. ลบ metadata และ prefix ก่อน
    $cleaned = preg_replace('/^.*?Model\s*:\s*/i', '', $cleaned); // ลบทุกอย่างก่อน "Model :"
    $cleaned = preg_replace('/^(Cooling\s+Fan|Fan|Motor|Pump)\s+/i', '', $cleaned); // ลบคำทั่วไป
    
    // 2. ลบ code ท้ายชื่อ
    $codePatterns = [
        '/\s+[A-Z]\.\d+[^\s]*\s+[A-Z]\.\d+.*$/i',  // C.12174 S.021620
        '/\*{2,}.*$/i',
        '/\/\/[a-z]\.\d+.*$/i',
    ];
    
    foreach ($codePatterns as $pattern) {
        $cleaned = preg_replace($pattern, '', $cleaned);
    }
    
    // 3. ลบตัวเลขโดดเดี่ยวท้ายสุด (แก้ปัญหา "3", "2" ใน DB)
    $cleaned = preg_replace('/\s+\d+$/', '', $cleaned);
    
    // 4. ⭐ ลบชื่อแบรนด์ที่ต่อท้าย (มักมี comma นำหน้า)
    $cleaned = preg_replace('/,\s*(SCHNEIDER|ABB|SIEMENS|MITSUBISHI|OMRON|FUJI|YASKAWA|PANASONIC|EATON|LEGRAND|HAGER|MOELLER|ALLEN\s*BRADLEY|ROCKWELL|GE|SQUARE\s*D|CUTLER\s*HAMMER|PHOENIX\s*CONTACT|WEIDMULLER|PILZ|SICK|TURCK|PEPPERL\s*FUCHS|IFM|BALLUFF|FESTO|SMC)\s*$/i', '', $cleaned);
    
    // ... ขั้นตอนอื่นๆ เหมือนเดิม
    
    return trim($cleaned);
}

/**
 * ฟังก์ชันเปรียบเทียบชื่อแบบ exact
 */
private function isExactMatch($apiName, $dbName)
{
    $cleanApi = $this->cleanProductName($apiName);
    $cleanDb = $this->cleanProductName($dbName);
    
    // Normalize: ลบทุกอย่างที่ไม่ใช่ตัวอักษรและตัวเลข แล้วแปลงเป็นตัวใหญ่
    $normalizedApi = strtoupper(preg_replace('/[^A-Z0-9]/i', '', $cleanApi));
    $normalizedDb = strtoupper(preg_replace('/[^A-Z0-9]/i', '', $cleanDb));
    
    $isMatch = $normalizedApi === $normalizedDb;
    
    // Log เพื่อ debug
    Log::info('🔍 Exact Match Check:', [
        'api_original' => $apiName,
        'db_original' => $dbName,
        'api_cleaned' => $cleanApi,
        'db_cleaned' => $cleanDb,
        'api_normalized' => $normalizedApi,
        'db_normalized' => $normalizedDb,
        'IS_MATCH' => $isMatch ? '✅ YES' : '❌ NO'
    ]);
    
    return $isMatch;
}

private function extractKeywords($name)
{
    // ลบ code ท้ายชื่อทุกรูปแบบ
    $cleaned = preg_replace('/[<\^\+]{1,2}[A-Z]\.\d+.*?[>\^\+]{1,2}/', '', $name);
    $cleaned = preg_replace('/\s+[A-Z]\.\d+[\.\d]*\s+[A-Z]\.[\d\/]+.*$/', '', $cleaned);
    $cleaned = preg_replace('/\*{2,}.*$/', '', $cleaned);
    
    // ลบ code หลัง model number
    $cleaned = preg_replace('/\s+\d{4}-\d+.*$/', '', $cleaned);
    
    // แปลง full-width space เป็น normal space
    $cleaned = str_replace(['　', '  ', "\t", '"', "'"], ' ', $cleaned);
    
    // ลบ Brand:, Model:, PR: ออก
    $cleaned = preg_replace('/\s*\|\s*PR:.*$/i', '', $cleaned);
    $cleaned = preg_replace('/\s*Brand:.*$/i', '', $cleaned);
    $cleaned = preg_replace('/\s*Model:.*$/i', '', $cleaned);
    
    // ลบ + ที่หน้าและหลังชื่อ
    $cleaned = preg_replace('/^\++/', '', $cleaned);
    $cleaned = preg_replace('/\++$/', '', $cleaned);
    
    $cleaned = trim($cleaned);
    
    // แยกคำและตัวเลข/model number
    preg_match_all('/[A-Z]+[\+]?|[A-Z]*\d+[A-Z]*[\-]?[A-Z]*/', strtoupper($cleaned), $matches);
    
    $keywords = [];
    $excludeWords = ['WITH', 'MANUAL', 'ENGLISH', 'FOR', 'THE', 'AND', 'OR', 
                     'PR', 'BRAND', 'MODEL', 'NO', 'TX', 'OHC', 'PART', 'TWT'];
    
    foreach ($matches[0] as $word) {
        $word = trim($word);
        
        // เก็บคำที่มีความหมาย
        if (strlen($word) >= 2 && !in_array($word, $excludeWords)) {
            $keywords[] = $word;
        }
    }
    
    return array_unique($keywords);
}

public function searchInvoice(Request $request)
{
    try {
        $poNumber = $request->input('po_number');
        $goodName = $request->input('good_name');
        $apiQuantity = $request->input('quantity');
        $apiCompleteFlag = $request->input('complete_flag', 'N');

        $cleanPoNumber = preg_replace('/^PO/i', '', $poNumber);

        Log::info('=== Search Invoice Request ===', [
            'po_number' => $cleanPoNumber,
            'good_name' => $goodName,
            'api_quantity' => $apiQuantity
        ]);

        if (!$cleanPoNumber || !$goodName) {
            return response()->json([
                'success' => false,
                'message' => 'Missing parameters',
                'date_invice' => '',
                'invice' => '',
                'total_received' => 0,
                'is_complete' => false,
                'has_data' => false
            ]);
        }

        // ดึงข้อมูลทั้งหมดของ PO นี้
        $allRecords = Pooutside::where('ponum', $cleanPoNumber)->get();

        if ($allRecords->isEmpty()) {
            Log::warning('No records found for PO');
            return response()->json([
                'success' => false,
                'message' => 'No data found',
                'date_invice' => '',
                'invice' => '',
                'total_received' => 0,
                'is_complete' => false,
                'has_data' => false
            ]);
        }

        // นับจำนวนสินค้าที่ไม่ซ้ำกันใน DB (unique names)
        $uniqueDbNames = $allRecords->pluck('name')->unique();
        $dbItemCount = $uniqueDbNames->count();

        Log::info('PO Items Count:', [
            'db_unique_items' => $dbItemCount,
            'db_items' => $uniqueDbNames->toArray()
        ]);

        // === ขั้นตอนที่ 1: ลอง exact match ก่อน ===
        $exactMatch = null;
        foreach ($allRecords as $record) {
            if ($this->isExactMatch($goodName, $record->name)) {
                $exactMatch = $record;
                Log::info('✓ EXACT MATCH FOUND', [
                    'api_name' => $goodName,
                    'db_name' => $record->name
                ]);
                break;
            }
        }

        // === ขั้นตอนที่ 2: ถ้าไม่เจอ exact match แต่มีข้อมูลอย่างละ 1 รายการ ให้จับคู่โดยอัตโนมัติ ===
        if (!$exactMatch && $dbItemCount === 1) {
            $exactMatch = $allRecords->first();
            Log::info('✓ AUTO-MATCH (Single Item in PO)', [
                'reason' => 'Only 1 unique item in DB for this PO',
                'api_name' => $goodName,
                'db_name' => $exactMatch->name,
                'auto_matched' => true
            ]);
        }

        // === ขั้นตอนที่ 3: ถ้ายังไม่เจอ ให้ใช้ keyword matching ===
        if (!$exactMatch) {
            $cleanedName = $this->cleanProductName($goodName);
            $apiKeywords = $this->extractKeywords($cleanedName);

            Log::info('No exact match and multiple items, trying keyword matching:', [
                'cleaned_name' => $cleanedName,
                'keywords' => $apiKeywords
            ]);

            $bestMatch = null;
            $highestScore = 0;

            foreach ($allRecords as $record) {
                $dbKeywords = $this->extractKeywords($record->name);
                
                $matchedKeywords = 0;
                $totalKeywords = count($apiKeywords);
                
                if ($totalKeywords === 0) continue;
                
                foreach ($apiKeywords as $apiKeyword) {
                    foreach ($dbKeywords as $dbKeyword) {
                        $normalizedApi = str_replace(' ', '', strtoupper($apiKeyword));
                        $normalizedDb = str_replace(' ', '', strtoupper($dbKeyword));
                        
                        if ($normalizedApi === $normalizedDb) {
                            $matchedKeywords++;
                            break;
                        }
                    }
                }
                
                $score = ($totalKeywords > 0) ? ($matchedKeywords / $totalKeywords) * 100 : 0;
                
                Log::info('Keyword comparison:', [
                    'api_keywords' => $apiKeywords,
                    'db_keywords' => $dbKeywords,
                    'db_name' => $record->name,
                    'matched' => $matchedKeywords . '/' . $totalKeywords,
                    'score' => $score
                ]);

                if ($score === 100.0 && $score > $highestScore) {
                    $highestScore = $score;
                    $bestMatch = $record;
                }
            }

            // ถ้าไม่มีที่ตรง 100% ถือว่าไม่เจอ
            if ($highestScore < 100) {
                Log::warning('No perfect keyword match found', [
                    'best_score' => $highestScore
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'No matching item found',
                    'date_invice' => '',
                    'invice' => '',
                    'total_received' => 0,
                    'is_complete' => false,
                    'has_data' => false
                ]);
            }

            $exactMatch = $bestMatch;
        }

        // === มี match แล้ว ===
        $exactName = $exactMatch->name;
        $matchedRecords = $allRecords->filter(function($record) use ($exactName) {
            return $record->name === $exactName;
        });

        $totalReceived = $matchedRecords->sum(function($item) {
            return floatval($item->quantity);
        });

        $latestRecord = $matchedRecords->sortByDesc('date_invice')->first();
        $apiQty = floatval($apiQuantity);
        $isComplete = $totalReceived >= $apiQty;

        Log::info('=== FINAL MATCH ===', [
            'api_name' => $goodName,
            'matched_db_name' => $exactName,
            'records_count' => $matchedRecords->count(),
            'total_received' => $totalReceived,
            'api_quantity' => $apiQty
        ]);

        return response()->json([
            'success' => true,
            'date_invice' => $latestRecord->date_invice ?? '',
            'invice' => $latestRecord->invice ?? '',
            'total_received' => $totalReceived,
            'is_complete' => $isComplete,
            'has_data' => true,
            'api_complete_flag' => $apiCompleteFlag,
            'matched_name' => $exactName,
            'records' => $matchedRecords->map(function($item) {
                return [
                    'invoice' => $item->invice,
                    'date' => $item->date_invice,
                    'quantity' => $item->quantity,
                    'name' => $item->name
                ];
            })->values()
        ]);

    } catch (\Exception $e) {
        Log::error('Search Invoice Error:', [
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ]);

        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'date_invice' => '',
            'invice' => '',
            'total_received' => 0,
            'is_complete' => false,
            'has_data' => false
        ]);
    }
}  public function pull()
    {
        // บังคับ set time limit
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
            if ($row[0] !== $today) {
                continue;
            }

            // Insert ข้อมูลก่อน
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

        // แยก loop การ update vendor ออกมา และใช้ unique PO numbers
        $poNumbers = collect($csv)
            ->filter(fn($row) => $row[0] === $today)
            ->pluck(4)
            ->unique()
            ->values();

        foreach ($poNumbers as $poNumber) {
            try {
                // ลด delay เหลือ 0.2 วินาที
                usleep(200000);
                
                $apiUrl = "http://server_update:8000/api/getPODetail?PONum={$poNumber}";
                $response = Http::timeout(10)->get($apiUrl);

                if ($response->successful()) {
                    $poDetail = $response->json();
                    
                    if (isset($poDetail['VendorName']) && isset($poDetail['VendorCode'])) {
                        
                        // Update ทุกแถวที่มี ponum นี้
                        $affectedRows = DB::table('pooutside')
                            ->where('ponum', $poNumber)
                            ->where('date_invice', $today)
                            ->whereNull('idvendor')
                            ->update([
                                'name_vendor' => $poDetail['VendorName'],
                                'idvendor'    => $poDetail['VendorCode']
                            ]);
                        
                        if ($affectedRows > 0) {
                            $updated += $affectedRows;
                        }
                    }
                } else if ($response->status() === 429) {
                    // ถ้าโดน rate limit
                    sleep(1);
                    
                    $retryResponse = Http::timeout(10)->get($apiUrl);
                    if ($retryResponse->successful()) {
                        $poDetail = $retryResponse->json();
                        
                        if (isset($poDetail['VendorName']) && isset($poDetail['VendorCode'])) {
                            $affectedRows = DB::table('pooutside')
                                ->where('ponum', $poNumber)
                                ->where('date_invice', $today)
                                ->whereNull('idvendor')
                                ->update([
                                    'name_vendor' => $poDetail['VendorName'],
                                    'idvendor'    => $poDetail['VendorCode']
                                ]);
                            
                            if ($affectedRows > 0) {
                                $updated += $affectedRows;
                            }
                        }
                    } else {
                        $errors[] = "PO {$poNumber}: ถูก rate limit";
                    }
                }
                
            } catch (\Exception $e) {
                $errors[] = "PO {$poNumber}: " . $e->getMessage();
                Log::error("Error for {$poNumber}: " . $e->getMessage());
            }
        }

        return response()->json([
            'status' => true,
            'message' => "ดึงข้อมูลสำเร็จ {$inserted} รายการ, อัพเดท Vendor {$updated} รายการ",
            'errors' => count($errors) > 0 ? $errors : null
        ]);
    }
}