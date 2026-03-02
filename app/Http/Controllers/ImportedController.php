<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ImportedController extends Controller
{
    public function Imported()
    {
        return view('Imported.Imported');
    }

    public function getSODetail(Request $request)
    {
        $soNum = $request->query('SONum');
        
        $response = Http::get("http://server_update:8000/api/getSODetail", [
            'SONum' => $soNum
        ]);

        return response()->json($response->json());
    }

    public function getPODetail(Request $request)
    {
        $poNum = $request->query('PONum');
        
        $response = Http::get("http://server_update:8000/api/getPODetail", [
            'PONum' => $poNum
        ]);

        return response()->json($response->json());
    }
}