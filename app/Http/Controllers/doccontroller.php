<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Docbills;
use App\Models\docbillsdetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class DocController extends Controller
{
    public function dashboard(Request $request)
    {
        return view('document.dashboarddoc');
    }

    public function dashboarddoc()
    {
        $docbill = Docbills::all();
        return view('document.dashboarddoc', compact('docbill'));
    }

    public function insertdoc()
    {
        return view('document.insertdoc');
    }
    public function insertDocu(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'so_id' => 'nullable',
            'doctype' => 'required',
            'emp_name' => 'required',
            'customer_id' => 'required',
            'customer_name' => 'required',
            'customer_tel' => 'nullable|string',
            'customer_address' => 'nullable|string',
            'customer_la_long' => 'nullable|string',
            'revdate' => 'required',
            'additional_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        try {
            DB::table('docbills')->insert([
                'so_id' => $request->so_id,
                'doctype' => $request->doctype,
                'emp_name' => $request->emp_name,
                'status' => 0,
                'customer_id' => $request->customer_id,
                'customer_name' => $request->customer_name,
                'customer_tel' => $request->customer_tel,
                'customer_address' => $request->customer_address,
                'customer_la_long' => $request->customer_la_long,   
                'revdate' => $request->revdate,
                'notes' => $request->notes,
            ]);

            return response()->json(['success' => 'บันทึกข้อมูลสำเร็จ']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $e->getMessage()], 500);
        }
    }
    public function getdocBillDetail($doc_id)
    {
        try {
            $docbillDetails = docbills::where('doc_id', $doc_id)->get();
            
            if ($docbillDetails->isEmpty()) {
                return response()->json([]);
            }

            return response()->json($docbillDetails);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 500);
        }
    }
}
