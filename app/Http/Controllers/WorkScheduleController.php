<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WorkScheduleController extends Controller
{
    public function index()
    {
        // เรียกใช้ View ชื่อ WorkScheduleController.blade.php
         return view('sale.WorkSchededule');
    }
}
