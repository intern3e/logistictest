<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SheetController extends Controller
{
    public function send(Request $request)
    {
        $googleScriptURL = 'https://script.google.com/macros/s/AKfycbwGDVIq0oS7CQmjfQLkfcKN7y5VK6drABxNH4iq2IwBCyygcBIuo3v-9f_fXVPOEPnM/exec';

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($googleScriptURL, $request->all());

            return response()->json([
                'status' => 'success',
                'response' => $response->body()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
