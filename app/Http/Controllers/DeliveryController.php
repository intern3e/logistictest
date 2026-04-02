<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class deliveryController extends Controller
{
    public function Delivery()
    {
        return view('wrongitem.delivery');
    }
        public function oil()
    {
        return view('wrongitem.oil');
    }
            public function service()
    {
        return view('wrongitem.service');
    }
}