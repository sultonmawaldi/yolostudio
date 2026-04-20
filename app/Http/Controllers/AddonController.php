<?php

namespace App\Http\Controllers;

use App\Models\Addon;
use Illuminate\Http\Request;
use App\Models\Service;

class AddonController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:addons.view')->only('index');
        $this->middleware('permission:addons.create')->only(['create', 'store']);
        $this->middleware('permission:addons.edit')->only(['edit', 'update']);
        $this->middleware('permission:addons.delete')->only('destroy');
    }

    public function index()
    {
        $addons = Addon::latest()->get();
        return view('backend.addons.index', compact('addons'));
    }

    public function create()
    {
        $services = Service::orderBy('title')->get();

        return view('backend.addons.create', compact('services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code'     => 'required|unique:addons,code|max:255',
            'name'     => 'required|string|max:255',
            'price'    => 'required|integer|min:0',
            'unit'     => 'required|in:person,minute,item',
            'max_qty'  => 'nullable|integer|min:1',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $addon = Addon::create([
            'code'       => $request->code,
            'name'       => $request->name,
            'price'      => $request->price,
            'unit'       => $request->unit,
            'max_qty'    => $request->max_qty,
            'is_active'  => $request->boolean('is_active'),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        // SIMPAN RELASI PIVOT
        if ($request->services) {
            $addon->services()->sync($request->services);
        }

        return redirect()
            ->route('addons.index')
            ->with('success', 'Layanan tambahan berhasil ditambahkan');
    }

    public function edit(Addon $addon)
    {
        $services = Service::orderBy('title')->get();

        return view('backend.addons.edit', compact('addon', 'services'));
    }

    public function update(Request $request, Addon $addon)
    {
        $request->validate([
            'code'     => 'required|unique:addons,code,' . $addon->id . '|max:255',
            'name'     => 'required|string|max:255',
            'price'    => 'required|integer|min:0',
            'unit'     => 'required|in:person,minute,item',
            'max_qty'  => 'nullable|integer|min:1',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $addon->update([
            'code'       => $request->code,
            'name'       => $request->name,
            'price'      => $request->price,
            'unit'       => $request->unit,
            'max_qty'    => $request->max_qty,
            'is_active'  => $request->boolean('is_active'),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        // UPDATE RELASI PIVOT
        if ($request->services) {
            $addon->services()->sync($request->services);
        } else {
            $addon->services()->detach();
        }

        return redirect()
            ->route('addons.index')
            ->with('success', 'Layanan tambahan berhasil diperbarui');
    }

    public function destroy(Addon $addon)
    {
        $addon->delete();

        return redirect()
            ->route('addons.index')
            ->with('success', 'Layanan tambahan berhasil dihapus');
    }
}
