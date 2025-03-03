<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pobills;

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
}