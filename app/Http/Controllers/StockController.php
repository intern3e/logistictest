<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StockController extends Controller
{
    public function dashboard(Request $request)
    {

        return view('stock.dashboardstock');
    }
}
