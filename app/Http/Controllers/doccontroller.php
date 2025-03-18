<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Docbills;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        // Validate incoming request data
        $request->validate([
            'so_id' => 'required|string|max:255',
            'doc_id' => 'required|string|max:255',
            'so_number' => 'required|string|max:255',
            'doctype' => 'required|integer',
            'customer_id' => 'nullable|string|max:255',
            'customer_name' => 'nullable|string|max:255',
            'additional_notes' => 'nullable|string',
        ]);
    
        try {
            DB::beginTransaction();
    
            // Insert the data into the database
            $bill = Docbills::create([
                'so_id' => $request->so_id,
                'doc_id' => $request->doc_id,
                'so_number' => $request->so_number,
                'doctype' => $request->doctype,
                'customer_id' => $request->customer_id,
                'customer_name' => $request->customer_name,
                'additional_notes' => $request->additional_notes,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
    
            DB::commit();
            return response()->json(['success' => 'Bill opened successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error occurred while opening the bill: ' . $e->getMessage());
            return response()->json(['error' => 'Error occurred while opening the bill. Please try again later.'], 500);
        }
    }
}