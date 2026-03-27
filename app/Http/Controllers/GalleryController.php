<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use App\Models\Service;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    // ===========================
    // FRONTEND
    // ===========================
    public function index()
    {
        // ✅ Ambil hanya gallery yang aktif + eager loading service
        $galleries = Gallery::with('service')
            ->where('status', 1)
            ->latest()
            ->get();

        // ✅ Ambil hanya service yang aktif
        $services = Service::where('status', 1)
            ->orderBy('title', 'asc')
            ->get();

        return view('frontend.gallery', compact('galleries', 'services'));
    }

    // ===========================
    // BACKEND
    // ===========================
    public function adminIndex()
    {
        $galleries = Gallery::with('service')->latest()->get();

        return view('backend.gallery.index', compact('galleries'));
    }

    public function create()
    {
        // ambil service aktif
        $services = Service::where('status', 1)->get();

        return view('backend.gallery.create', compact('services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'      => 'required|string|max:255',
            'service_id' => 'required|exists:services,id',
            'image'      => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'description' => 'nullable|string',
        ]);

        // upload image
        $imageName = time() . '.' . $request->image->extension();
        $request->image->move(public_path('uploads/gallery'), $imageName);

        Gallery::create([
            'title'       => $request->title,
            'service_id'  => $request->service_id,
            'description' => $request->description,
            'image'       => $imageName,
            'status'      => $request->status ?? 1,
        ]);

        return redirect()
            ->route('gallery.index')
            ->with('success', 'Gallery berhasil ditambahkan.');
    }

    public function edit(Gallery $gallery)
    {
        $services = Service::where('status', 1)->get();

        return view('backend.gallery.edit', compact('gallery', 'services'));
    }

    public function update(Request $request, Gallery $gallery)
    {
        $request->validate([
            'title'      => 'required|string|max:255',
            'service_id' => 'required|exists:services,id',
            'image'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'description' => 'nullable|string',
        ]);

        $data = [
            'title'       => $request->title,
            'service_id'  => $request->service_id,
            'description' => $request->description,
            'status'      => $request->status,
        ];

        // jika upload gambar baru
        if ($request->hasFile('image')) {

            // hapus gambar lama
            if ($gallery->image && file_exists(public_path('uploads/gallery/' . $gallery->image))) {
                unlink(public_path('uploads/gallery/' . $gallery->image));
            }

            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/gallery'), $imageName);

            $data['image'] = $imageName;
        }

        $gallery->update($data);

        return redirect()
            ->route('gallery.index')
            ->with('success', 'Gallery berhasil diperbarui.');
    }

    public function destroy(Gallery $gallery)
    {
        // hapus file gambar
        if ($gallery->image && file_exists(public_path('uploads/gallery/' . $gallery->image))) {
            unlink(public_path('uploads/gallery/' . $gallery->image));
        }

        $gallery->delete();

        return redirect()
            ->route('gallery.index')
            ->with('success', 'Gallery berhasil dihapus.');
    }
}
