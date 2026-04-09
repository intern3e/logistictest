<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CheckdriverController extends Controller
{
    public function dashboard()
    {
        return view('driver.check');
    }
}
