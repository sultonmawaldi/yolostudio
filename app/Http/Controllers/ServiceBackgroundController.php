<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceBackground;
use Illuminate\Http\Request;

class ServiceBackgroundController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:service-backgrounds.view')->only('index');
        $this->middleware('permission:service-backgrounds.create')->only(['create', 'store']);
        $this->middleware('permission:service-backgrounds.edit')->only(['edit', 'update']);
        $this->middleware('permission:service-backgrounds.delete')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $backgrounds = ServiceBackground::orderBy('service_id')
            ->orderBy('sort_order')
            ->get();

        return view('backend.service-backgrounds.index', compact('backgrounds'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // 🔥 FIX: jangan orderBy('name')
        $services = Service::orderBy('id')->get();

        return view('backend.service-backgrounds.create', compact('services'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'name'       => 'required|string|max:255',
            'value'      => 'required|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        ServiceBackground::create([
            'service_id' => $validated['service_id'],
            'name'       => $validated['name'],
            'value'      => $validated['value'],
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => (int) $request->input('is_active', 1),
        ]);

        return redirect()
            ->route('service-backgrounds.index')
            ->with('success', 'Latar layanan berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ServiceBackground $background)
    {
        // 🔥 FIX: jangan orderBy('name')
        $services = Service::orderBy('id')->get();

        return view(
            'backend.service-backgrounds.edit',
            compact('background', 'services')
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ServiceBackground $background)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'name'       => 'required|string|max:255',
            'value'      => 'required|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $background->update([
            'service_id' => $validated['service_id'],
            'name'       => $validated['name'],
            'value'      => $validated['value'],
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => (int) $request->input('is_active', 1),
        ]);

        return redirect()
            ->route('service-backgrounds.index')
            ->with('success', 'Latar layanan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ServiceBackground $background)
    {
        $background->delete();

        return redirect()
            ->route('service-backgrounds.index')
            ->with('success', 'Latar layanan berhasil dihapus.');
    }

    public function getByService(Service $service)
    {
        return response()->json([
            'backgrounds' => $service->backgrounds()->get([
                'id',
                'name',
                'type',
                'value',
            ]),
        ]);
    }
}
