<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class text extends Controller
{
    public function txt1(Request $request)
    {
        return view('SOlist');
    }
    public function txt2()
    {
        return view('insertSO');
    }
    public function txt3()
    {
        return view('adminSO');
    }
}
