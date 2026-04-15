<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SettingController extends Controller
{
    public function index()
    {
        $setting = Setting::first();
        return view('backend.settings.index', compact('setting'));
    }

    public function update(Request $request, Setting $setting)
    {
        $data = $request->validate([
            'bname'                 => 'required|string|max:200',
            'email'                 => 'nullable|email|max:200',
            'phone'                 => 'nullable|string|max:20',
            'currency'              => 'nullable|string|max:20',
            'whatsapp'              => 'nullable|string|max:20',
            'address'               => 'nullable|string|max:255',

            // ✅ tambah dark_logo (tidak ganggu lama)
            'logo'                  => 'nullable|image|mimes:jpg,png,jpeg,gif,svg,webp|max:2048',
            'dark_logo'             => 'nullable|image|mimes:jpg,png,jpeg,gif,svg,webp|max:2048',

            'meta_title'            => 'nullable|string|max:255',
            'meta_keywords'         => 'nullable|string',
            'meta_description'      => 'nullable|string',
            'social'                => 'nullable',
            'map'                   => 'nullable',
            'header'                => 'nullable',
            'footer'                => 'nullable',
            'other'                 => 'nullable',
        ]);

        // =========================
        // FORMAT PHONE → +62
        // =========================
        if (!empty($data['phone'])) {
            $phone = preg_replace('/[^0-9]/', '', $data['phone']);
            $phone = ltrim($phone, '0');
            $data['phone'] = '+62' . $phone;
        }

        // =========================
        // FORMAT WHATSAPP → +62
        // =========================
        if (!empty($data['whatsapp'])) {
            $wa = preg_replace('/[^0-9]/', '', $data['whatsapp']);
            $wa = ltrim($wa, '0');
            $data['whatsapp'] = '+62' . $wa;
        }

        $path = public_path('uploads/images/logo/');

        // =========================
        // UPLOAD LOGO (AMAN)
        // =========================
        if ($request->hasFile('logo')) {

            // hapus lama (optional tapi best practice)
            if (!empty($setting->logo) && File::exists($path . $setting->logo)) {
                File::delete($path . $setting->logo);
            }

            $logoName = 'logo_' . time() . '.' . $request->logo->getClientOriginalExtension();
            $request->logo->move($path, $logoName);

            $data['logo'] = $logoName;
        }

        // =========================
        // UPLOAD DARK LOGO (NEW 🔥)
        // =========================
        if ($request->hasFile('dark_logo')) {

            if (!empty($setting->dark_logo) && File::exists($path . $setting->dark_logo)) {
                File::delete($path . $setting->dark_logo);
            }

            $darkLogoName = 'dark_' . time() . '.' . $request->dark_logo->getClientOriginalExtension();
            $request->dark_logo->move($path, $darkLogoName);

            $data['dark_logo'] = $darkLogoName;
        }

        $setting->update($data);

        return redirect()->route('setting')->with('success', 'Pengaturan berhasil diperbarui');
    }
}
