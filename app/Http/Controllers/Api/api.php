<?php

namespace App\Http\Controllers\Api;
use App\Models\Bill; 
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class api extends Controller
{
    public function apibilldeli(Request $request)
    {
        // รับค่าจาก query string และแปลงเป็น array
        $idbillParam = $request->query('idbill');
        $requestedIds = $idbillParam ? explode(',', $idbillParam) : [];

        // ดึงรายการ billid ที่มีอยู่ในฐานข้อมูล
        $existingIds = Bill::whereIn('billid', $requestedIds)->pluck('billid')->toArray();

        // สร้าง response โดยตรวจสอบแต่ละ ID ว่ามีหรือไม่
        $result = ['idbilldelivery' => []];

        foreach ($requestedIds as $id) {
            $result['idbilldelivery'][$id] = in_array($id, $existingIds);
        }

        return response()->json($result);
    }
}
