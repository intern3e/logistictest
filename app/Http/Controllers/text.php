<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class text extends Controller
{
    public function txt1(Request $request)
    {
        $this->requireLogin($request);
        return view('SOlist');
    }
    public function txt2()
    {
        $this->requireLogin();
        return view('insertSO');
    }
    public function txt3()
    {
        $this->requireLogin();
        return view('adminSO');
    }
}
