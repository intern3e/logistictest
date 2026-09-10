<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CheckdriverController extends Controller
{
    public function dashboard()
    {
        $this->requireLogin();
        return view('driver.check');
    }
}
