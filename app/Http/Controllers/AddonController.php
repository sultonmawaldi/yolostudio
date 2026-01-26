<?php

namespace App\Http\Controllers;

use App\Models\Addon;
use Illuminate\Http\Request;

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
        return view('backend.addons.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code'     => 'required|unique:addons,code',
            'name'     => 'required|string|max:255',
            'price'    => 'required|numeric|min:0',
            'unit'     => 'required|string|max:50',
            'max_qty'  => 'nullable|integer|min:1',
        ]);

        Addon::create([
            'code'      => strtoupper($request->code),
            'name'      => $request->name,
            'price'     => $request->price,
            'unit'      => $request->unit,
            'max_qty'   => $request->max_qty,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('addons.index')
            ->with('success', 'Addon berhasil ditambahkan');
    }

    public function edit(Addon $addon)
    {
        return view('backend.addons.edit', compact('addon'));
    }

    public function update(Request $request, Addon $addon)
    {
        $request->validate([
            'code'     => 'required|unique:addons,code,' . $addon->id,
            'name'     => 'required|string|max:255',
            'price'    => 'required|numeric|min:0',
            'unit'     => 'required|string|max:50',
            'max_qty'  => 'nullable|integer|min:1',
        ]);

        $addon->update([
            'code'      => strtoupper($request->code),
            'name'      => $request->name,
            'price'     => $request->price,
            'unit'      => $request->unit,
            'max_qty'   => $request->max_qty,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('addons.index')
            ->with('success', 'Addon berhasil diupdate');
    }

    public function destroy(Addon $addon)
    {
        $addon->delete();

        return redirect()
            ->route('addons.index')
            ->with('success', 'Addon berhasil dihapus');
    }
}
