<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OtRequestController extends Controller
{
    public function index()
    {
        // ส่งข้อมูลไปแสดงผลที่หน้า resources/views/ot/dashboardot.blade.php
        return view('ot.adminOT');
    }
}
