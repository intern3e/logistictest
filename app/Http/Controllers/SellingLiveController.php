<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SellingLiveController extends Controller
{
    public function insertsellinglive()
    {
        return view('sellinglive.insertsellinglive');
    }
    public function dashboardsellinglive()
    {
        return view('sellinglive.dashboardsellinglive');
    }
}