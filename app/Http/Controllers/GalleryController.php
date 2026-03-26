<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    // ===========================
    // FRONTEND
    // ===========================
    public function index()
    {
        $galleries = Gallery::latest()->get();
        return view('frontend.gallery', compact('galleries'));
    }

    // ===========================
    // BACKEND
    // ===========================
    public function adminIndex()
    {
        $galleries = Gallery::latest()->paginate(10);
        return view('backend.gallery.index', compact('galleries'));
    }

    public function create()
    {
        $categories = $this->getCategories();
        return view('backend.gallery.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'    => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'image'    => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $imageName = time() . '.' . $request->image->extension();
        $request->image->move(public_path('uploads/gallery'), $imageName);

        Gallery::create([
            'title'       => $request->title,
            'description' => $request->description,
            'category'    => $request->category,
            'image'       => $imageName,
            'status'      => 1,
        ]);

        return redirect()
            ->route('gallery.index')
            ->with('success', 'Gallery berhasil ditambahkan.');
    }

    public function edit(Gallery $gallery)
    {
        $categories = $this->getCategories();
        return view('backend.gallery.edit', compact('gallery', 'categories'));
    }

    public function update(Request $request, Gallery $gallery)
    {
        $request->validate([
            'title'    => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'image'    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $data = [
            'title'       => $request->title,
            'description' => $request->description,
            'category'    => $request->category,
        ];

        if ($request->hasFile('image')) {
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
        if ($gallery->image && file_exists(public_path('uploads/gallery/' . $gallery->image))) {
            unlink(public_path('uploads/gallery/' . $gallery->image));
        }

        $gallery->delete();

        return redirect()
            ->route('gallery.index')
            ->with('success', 'Gallery berhasil dihapus.');
    }

    // ===========================
    // PRIVATE
    // ===========================
    private function getCategories()
    {
        return [
            'Self Photo Studio',
            'Personal Selfphoto Studio',
            'Pas Photo',
            'Wide Box Maroon',
            'Wide Box Grey',
            'Corner Box',
            'Gorden Box',
        ];
    }
}
