<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pobills;
use Illuminate\Support\Facades\DB;

class AdminpoController extends Controller
{
    public function dashboard(Request $request)
    {
        $pobill = Pobills::all();  // Fetch the data
        return view('po.adminpo', compact('pobill'));  // Pass data to the view
    }

    public function dashboardpo()
    {
        $pobill = Pobills::all();  // Fetch the data
        return view('po.adminpo', compact('pobill'));  // Pass data to the view
    }
    public function historypo()
    {
        $pobill = poBills::orderBy('po_detail_id', 'desc')->get();
        return view('po.historypo', compact('pobill'));
    }
   
    public function updateStatus(Request $request)
    {
        $poDetailIds  = $request->input('poDetailIds');
    
        // Update the status of the selected SO details to 1
        DB::table('pobills')
            ->whereIn('po_detail_id', $poDetailIds )
            ->update(['status' => 1]);
    
        return response()->json(['success' => true]);
    }
    
}