<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Fluke;

class FlukeApiController extends Controller
{
    public function index()
    {
        return response()->json(Fluke::all());
    }

    public function show($iditem)
    {
        $fluke = Fluke::where('iditem', $iditem)->first();

        if (!$fluke) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        return response()->json($fluke);
    }
}
