<?php

namespace App\Http\Controllers;

use App\Models\Studio;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StudioController extends Controller
{
    // ===========================
    // FRONTEND
    // ===========================

    public function index()
    {
        $studios = Studio::where('status', 1)
            ->orderBy('id')
            ->get();

        return view('frontend.studio', compact('studios'));
    }

    // ===========================
    // BACKEND
    // ===========================

    public function adminIndex()
    {
        $studios = Studio::latest()->paginate(10);
        return view('backend.studios.index', compact('studios'));
    }

    public function create()
    {
        return view('backend.studios.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'address'     => 'required|string',
            'city'        => 'required|string|max:255',
            'phone'       => 'nullable|string|max:255',
            'google_maps' => 'nullable|url',
            'status'      => 'required|boolean',
            'image'       => 'nullable|image|max:2048',
        ]);

        $data = [
            'name'        => $request->name,
            'slug'        => Str::slug($request->name),
            'address'     => $request->address,
            'city'        => $request->city,
            'phone'       => $request->phone,
            'google_maps' => $request->google_maps,
            'status'      => $request->status,
        ];

        // Upload gambar ke public/uploads/studios
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/studios'), $imageName);
            $data['image'] = $imageName;
        }

        Studio::create($data);

        return redirect()
            ->route('studio.index')
            ->with('success', 'Studio berhasil ditambahkan');
    }

    public function edit(Studio $studio)
    {
        return view('backend.studios.edit', compact('studio'));
    }

    public function update(Request $request, Studio $studio)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'address'     => 'required|string',
            'city'        => 'required|string|max:255',
            'phone'       => 'nullable|string|max:255',
            'google_maps' => 'nullable|url',
            'status'      => 'required|boolean',
            'image'       => 'nullable|image|max:2048',
        ]);

        $data = [
            'name'        => $request->name,
            'slug'        => Str::slug($request->name),
            'address'     => $request->address,
            'city'        => $request->city,
            'phone'       => $request->phone,
            'google_maps' => $request->google_maps,
            'status'      => $request->status,
        ];

        if ($request->hasFile('image')) {
            // Hapus gambar lama
            if ($studio->image && file_exists(public_path('uploads/studios/' . $studio->image))) {
                unlink(public_path('uploads/studios/' . $studio->image));
            }

            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/studios'), $imageName);
            $data['image'] = $imageName;
        }

        $studio->update($data);

        return redirect()
            ->route('studio.index')
            ->with('success', 'Studio berhasil diperbarui');
    }

    public function destroy(Studio $studio)
    {
        if ($studio->image && file_exists(public_path('uploads/studios/' . $studio->image))) {
            unlink(public_path('uploads/studios/' . $studio->image));
        }

        $studio->delete();

        return redirect()
            ->route('studio.index')
            ->with('success', 'Studio berhasil dihapus');
    }
}
