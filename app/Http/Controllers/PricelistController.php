<?php

namespace App\Http\Controllers;

use App\Models\Pricelist;
use App\Models\Service;
use Illuminate\Http\Request;

class PricelistController extends Controller
{
    public function index()
    {
        $pricelists = Pricelist::ordered()->get();
        return view('admin.pricelists.index', compact('pricelists'));
    }

    public function create()
    {
        $services = Service::all();
        return view('admin.pricelists.create', compact('services'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'service_id'   => 'nullable|exists:services,id',
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'price'        => 'required|numeric|min:0',
            'category'     => 'nullable|string|max:100',
            'features'     => 'nullable|array',
            'button_text'  => 'required|string|max:50',
            'button_link'  => 'nullable|string',
            'is_active'    => 'boolean',
            'sort_order'   => 'integer',
        ]);

        Pricelist::create($data);

        return redirect()->route('admin.pricelists.index')
            ->with('success', 'Pricelist berhasil ditambahkan');
    }

    public function edit(Pricelist $pricelist)
    {
        $services = Service::all();
        return view('admin.pricelists.edit', compact('pricelist', 'services'));
    }

    public function update(Request $request, Pricelist $pricelist)
    {
        $data = $request->validate([
            'service_id'   => 'nullable|exists:services,id',
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'price'        => 'required|numeric|min:0',
            'category'     => 'nullable|string|max:100',
            'features'     => 'nullable|array',
            'button_text'  => 'required|string|max:50',
            'button_link'  => 'nullable|string',
            'is_active'    => 'boolean',
            'sort_order'   => 'integer',
        ]);

        $pricelist->update($data);

        return redirect()->route('admin.pricelists.index')
            ->with('success', 'Pricelist berhasil diupdate');
    }

    public function destroy(Pricelist $pricelist)
    {
        $pricelist->delete();

        return back()->with('success', 'Pricelist berhasil dihapus');
    }
}
