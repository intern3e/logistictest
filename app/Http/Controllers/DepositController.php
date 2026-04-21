<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DepositController extends Controller
{
    public function insertsellinglive()
    {
        return view('sellinglive.insertsellinglive');
    }
    public function dashboardsellinglive()
    {
        return view('sellinglive.dashboardsellinglive');
    } 
        public function Botsellinglive()
    {
        return view('sellinglive.Botsellinglive');
    }
}
