<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SotestController extends Controller
{
    public function dashboard(Request $request)
        {
            return view('sale.Sotest');  // Pass data to the view
        }
 //
}
