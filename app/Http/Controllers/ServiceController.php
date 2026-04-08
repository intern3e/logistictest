<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
        {
         
            return view('driver.service'); // Pass data to the view
        }
}
