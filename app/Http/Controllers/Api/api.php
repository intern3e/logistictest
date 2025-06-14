<?php

namespace App\Http\Controllers\Api;
use App\Models\Bill; 
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class api extends Controller
{
    public function apibilldeli()
    {
        $bills = Bill::pluck('billid')->toArray(); 

        $result = ['idbilldelivery' => []];

        foreach ($bills as $billid) {
            $result['idbilldelivery'][$billid] = true;
        }

        return response()->json($result);
    }
}