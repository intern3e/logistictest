<?php

// TechnicianController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TechnicianController extends Controller
{
    public function technician()
    {
        return view('technician.technician');
    }
}
