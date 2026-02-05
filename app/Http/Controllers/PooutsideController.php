<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pooutside;
use Illuminate\Support\Facades\Log;

class PooutsideController extends Controller
{
    public function dashboard(Request $request)
    {
        $poData = Pooutside::orderBy('date_invice', 'desc')
                          ->paginate(100);
        
        return view('pooutside.dashboard', compact('poData'));
    }

    private function calculateExpectedDate($dateInvice)
        {
            if (!$dateInvice) {
                return null;
            }
            
            try {
                // แยกวันที่ออกจาก format d/m/Y (20/02/2569)
                $dateArray = explode('/', $dateInvice);
                
                if (count($dateArray) == 3) {
                    // สร้าง Carbon object (แปลง พ.ศ. เป็น ค.ศ. ก่อน)
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

            // ทำความสะอาดชื่อสินค้า
            $cleanedName = preg_replace('/\s*\*{2,}.*$/', '', $goodName);
            $cleanedName = preg_replace('/\s*\|\s*PR:.*$/i', '', $cleanedName);
            $cleanedName = preg_replace('/\s*Brand:.*$/i', '', $cleanedName);
            $cleanedName = preg_replace('/\s*Model:.*$/i', '', $cleanedName);
            $cleanedName = trim($cleanedName);

            // ดึง keywords สำคัญจากชื่อสินค้า (รวมขนาด/ตัวเลข)
            $apiKeywords = $this->extractKeywords($cleanedName);

            Log::info('Extracted keywords from API:', [
                'original' => $goodName,
                'cleaned' => $cleanedName,
                'keywords' => $apiKeywords
            ]);

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

            // === จับคู่แบบ 1:1 โดยตรวจสอบ keywords ครบถ้วน ===
            $bestMatch = null;
            $highestScore = 0;
            $bestDbName = '';

            foreach ($allRecords as $record) {
                $dbName = $record->name;
                $dbKeywords = $this->extractKeywords($dbName);
                
                // ตรวจสอบว่า keywords สำคัญจาก API มีใน DB ครบหรือไม่
                $matchedKeywords = 0;
                $totalKeywords = count($apiKeywords);
                
                foreach ($apiKeywords as $apiKeyword) {
                    foreach ($dbKeywords as $dbKeyword) {
                        // ตรวจสอบความเหมือน
                        similar_text(strtolower($apiKeyword), strtolower($dbKeyword), $percent);
                        
                        // ถือว่าตรงถ้า similarity > 85%
                        if ($percent > 85) {
                            $matchedKeywords++;
                            break; // หา keyword ถัดไป
                        }
                    }
                }
                
                // คำนวณ keyword match percentage
                $keywordMatchPercent = ($totalKeywords > 0) ? ($matchedKeywords / $totalKeywords) * 100 : 0;
                
                // คำนวณ overall similarity
                similar_text(strtolower($cleanedName), strtolower($dbName), $overallSimilarity);
                
                // คะแนนรวม: 70% จาก keyword match + 30% จาก overall similarity
                $finalScore = ($keywordMatchPercent * 0.7) + ($overallSimilarity * 0.3);
                
                Log::info('Comparing:', [
                    'api_name' => $cleanedName,
                    'api_keywords' => $apiKeywords,
                    'db_name' => $dbName,
                    'db_keywords' => $dbKeywords,
                    'matched_keywords' => $matchedKeywords . '/' . $totalKeywords,
                    'keyword_match_percent' => round($keywordMatchPercent, 2),
                    'overall_similarity' => round($overallSimilarity, 2),
                    'final_score' => round($finalScore, 2)
                ]);

                // เก็บตัวที่คะแนนสูงสุด
                if ($finalScore > $highestScore) {
                    $highestScore = $finalScore;
                    $bestMatch = $record;
                    $bestDbName = $dbName;
                }
            }

            // ต้องมี keyword match อย่างน้อย 80% จึงถือว่าเจอ
            if ($highestScore < 70) {
                Log::warning('No good match found', [
                    'best_score' => round($highestScore, 2),
                    'best_match' => $bestDbName
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

            // เอาเฉพาะรายการที่มีชื่อตรงกับ bestMatch
            $exactName = $bestMatch->name;
            $matchedRecords = $allRecords->filter(function($record) use ($exactName) {
                return $record->name === $exactName;
            });

            // คำนวณยอดรวม
            $totalReceived = $matchedRecords->sum(function($item) {
                return floatval($item->quantity);
            });

            $latestRecord = $matchedRecords->sortByDesc('date_invice')->first();
            $apiQty = floatval($apiQuantity);
            $isComplete = $totalReceived >= $apiQty;

            Log::info('=== FINAL MATCH (1:1) ===', [
                'api_name' => $cleanedName,
                'matched_db_name' => $exactName,
                'best_score' => round($highestScore, 2),
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
                'match_score' => round($highestScore, 2),
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
    }

    /**
     * แยก keywords สำคัญจากชื่อสินค้า
     */
    private function extractKeywords($name)
    {
        // แยกคำและตัวเลข/ขนาด
        preg_match_all('/[\w\/]+/', $name, $matches);
        
        $keywords = [];
        foreach ($matches[0] as $word) {
            $word = strtolower(trim($word));
            
            // เก็บคำที่มีความหมาย (ยาวกว่า 1 ตัว หรือเป็นตัวเลข/ขนาด)
            if (strlen($word) > 1 || preg_match('/\d/', $word)) {
                // ไม่เอาคำทั่วไปที่ไม่มีความหมาย
                if (!in_array($word, ['pr', 'brand', 'model', 'smc', 'wago'])) {
                    $keywords[] = $word;
                }
            }
        }
        
        return array_unique($keywords);
    }
}