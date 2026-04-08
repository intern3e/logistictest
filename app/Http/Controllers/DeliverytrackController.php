<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DeliverytrackController extends Controller
{
      public function index(Request $request)
        {
         
            return view('driver.deliverytrack'); // Pass data to the view
        }
}
