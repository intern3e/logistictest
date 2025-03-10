<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class text extends Controller
{
    public function txt1(Request $request)
    {
        return view('txt');
    }
    public function txt2()
    {
        return view('txt2');
    }
}
