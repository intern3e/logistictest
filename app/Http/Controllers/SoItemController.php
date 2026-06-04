<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SoItemController extends Controller
{
    public function index()
    {
        return view('sale.SoItem');
    }

    public function store(Request $request)
    {
        // รับข้อมูล POST
        return response()->json([
            'status' => 'success'
        ]);
    }
}