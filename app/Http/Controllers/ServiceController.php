<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\service_car;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        return view('driver.service');
    }
    public function list(Request $request)
    {
        $q      = $request->query('q', '');
        $type   = $request->query('type', '');
        $status = $request->query('status', '');

        $query = service_car::query()->orderBy('date', 'desc')->orderBy('id', 'desc');

        if ($q) {
            $query->where(function ($qb) use ($q) {
                $qb->where('driver', 'like', "%{$q}%")
                   ->orWhere('plate',  'like', "%{$q}%")
                   ->orWhere('detail', 'like', "%{$q}%");
            });
        }
        if ($type)   $query->where('type',   $type);
        if ($status) $query->where('status', $status);

        $records = $query->get()->map(function ($r) {
            $r->image_urls = collect($r->images ?? [])->map(
                fn($p) => asset('storage/' . $p)
            )->values();
            return $r;
        });

        // metrics (always from full table)
        $all       = service_car::all();
        $totalCost = $all->sum('cost');
        $total     = $all->count();
        $cars      = $all->pluck('plate')->unique()->count();

        return response()->json([
            'records' => $records,
            'metrics' => [
                'total'    => $total,
                'totalCost'=> $totalCost,
                'avg'      => $total ? round($totalCost / $total) : 0,
                'cars'     => $cars,
            ],
        ]);
    }

    /* ── STORE ── */
    public function store(Request $request)
    {
        $request->validate([
            'date'   => 'required|date',
            'driver' => 'required|string|max:100',
            'plate'  => 'required|string|max:50',
            'type'   => 'required|string|max:100',
            'cost'   => 'nullable|numeric|min:0',
            'status' => 'required|string|max:50',
            'detail' => 'nullable|string',
            'images.*'=> 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $paths = $this->uploadImages($request);

        $record = service_car::create([
            'date'   => $request->date,
            'driver' => $request->driver,
            'plate'  => $request->plate,
            'type'   => $request->type,
            'cost'   => $request->cost ?? 0,
            'status' => $request->status,
            'detail' => $request->detail,
            'images' => $paths,
        ]);

        $record->image_urls = collect($paths)->map(
            fn($p) => asset('storage/' . $p)
        )->values();

        return response()->json(['success' => true, 'record' => $record], 201);
    }

    /* ── UPDATE ── */
    public function update(Request $request, $id)
    {
        $record = service_car::findOrFail($id);

        $request->validate([
            'date'    => 'required|date',
            'driver'  => 'required|string|max:100',
            'plate'   => 'required|string|max:50',
            'type'    => 'required|string|max:100',
            'cost'    => 'nullable|numeric|min:0',
            'status'  => 'required|string|max:50',
            'detail'  => 'nullable|string',
            'images.*'=> 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'keep_images' => 'nullable|string', // JSON array of paths to keep
        ]);

        // paths to keep (images not replaced)
        $keep = json_decode($request->input('keep_images', '[]'), true) ?? [];

        // delete removed images from disk
        $old = $record->images ?? [];
        foreach ($old as $p) {
            if (!in_array($p, $keep)) {
                Storage::disk('public')->delete($p);
            }
        }

        // upload new images
        $newPaths = $this->uploadImages($request);
        $allPaths = array_merge($keep, $newPaths);

        $record->update([
            'date'   => $request->date,
            'driver' => $request->driver,
            'plate'  => $request->plate,
            'type'   => $request->type,
            'cost'   => $request->cost ?? 0,
            'status' => $request->status,
            'detail' => $request->detail,
            'images' => $allPaths,
        ]);

        $record->image_urls = collect($allPaths)->map(
            fn($p) => asset('storage/' . $p)
        )->values();

        return response()->json(['success' => true, 'record' => $record]);
    }

    /* ── DESTROY ── */
    public function destroy($id)
    {
        $record = service_car::findOrFail($id);

        foreach ($record->images ?? [] as $p) {
            Storage::disk('public')->delete($p);
        }

        $record->delete();

        return response()->json(['success' => true]);
    }

    /* ── HELPER ── */
    private function uploadImages(Request $request): array
    {
        $paths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('service_car', 'public');
                $paths[] = $path;
            }
        }
        return $paths;
    }
}