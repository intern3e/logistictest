<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Docbills;

class admindoccontroller extends Controller
{
    public function dashboard(Request $request)
    {
        $docbill = docbills::all();  // Fetch the data
        return view('document.dashboarddoc', compact('docbill'));  // Pass data to the view
    }

    public function dashboarddoc()
    {
        $docbill = docbills::all();  // Fetch the data
        return view('document.admindoc', compact('docbill'));  // Pass data to the view
    }
    public function historydoc()
    {
        $docbill = docbills::orderBy('doc_id', 'desc')->get();
        return view('document.historydoc', compact('docbill'));
    }
   
    public function updateStatus(Request $request)
    {
        $docDetailIds  = $request->input('docDetailIds');
    
        // Update the status of the selected SO details to 1
        DB::table('docbills')
            ->whereIn('doc_id', $docDetailIds )
            ->update(['status' => 1]);
    
        return response()->json(['success' => true]);
    }
    
}
