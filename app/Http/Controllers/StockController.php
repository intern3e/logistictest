<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory;
class StockController extends Controller
{
    public function dashboard(Request $request)
    {

        return view('stock.dashboardstock');
    }
    public function dashboardInventory(Request $request)
        {
            $items = Inventory::query()
                ->when($request->filled('q'), function ($q) use ($request) {
                    $s = trim($request->q);
                    $q->where(function ($w) use ($s) {
                        $w->where('iditem', 'like', "%{$s}%")
                        ->orWhere('model', 'like', "%{$s}%")
                        ->orWhere('brand', 'like', "%{$s}%")
                        ->orWhere('inven_location', 'like', "%{$s}%");
                    });
                })
                ->orderBy('iditem')
                ->paginate(25)
                ->withQueryString();

            return view('stock.dashboardinventory', compact('items'));
        }
}
