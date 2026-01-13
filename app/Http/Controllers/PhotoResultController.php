<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\PhotoResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use Illuminate\Support\Facades\Http;

class PhotoResultController extends Controller
{
    /**
     * Halaman admin daftar transaksi & foto hasil.
     */
    public function index(Request $request)
{
    $query = Transaction::with(['photoResults', 'user', 'appointment']);

if ($request->filled('search')) {
    $search = $request->search;

    $query->where(function($q) use ($search) {
        $q->where('transaction_code', 'like', "%{$search}%")
          ->orWhereHas('user', function ($q2) use ($search) {
              $q2->where('name', 'like', "%{$search}%");
          })
          ->orWhereHas('appointment', function ($q2) use ($search) {
              $q2->where('name', 'like', "%{$search}%");
          });
    });
}

    $transactions = $query->orderBy('created_at', 'desc')->paginate(10);

    return view('backend.photo_results.index', compact('transactions'));
}


    /**
     * Upload foto hasil untuk transaksi tertentu.
     */
    public function store(Request $request)
{
    $request->validate([
        'transaction_id' => 'required|exists:transactions,id',
        'photos' => 'required|array',
    ]);

    $transaction = Transaction::findOrFail($request->transaction_id);

    // Jika transaksi belum punya token publik, buatkan
    if (!$transaction->public_token) {
        $transaction->public_token = bin2hex(random_bytes(8));
        $transaction->public_token_expires_at = now()->addDays(7);
        $transaction->save();
    }

    $uploadedFiles = [];
    $failedFiles = [];

    foreach ($request->file('photos') as $file) {
        try {
            // Validasi tiap file
            $validator = \Validator::make(
                ['file' => $file],
                ['file' => 'image|mimes:jpg,jpeg,png|max:5120'] // max 5MB
            );

            if ($validator->fails()) {
                $failedFiles[] = $file->getClientOriginalName();
                continue; // skip file gagal
            }

            // Simpan file ke storage
            $path = $file->store('photos/' . $transaction->id, 'public');

            // Simpan record ke database
            PhotoResult::create([
                'transaction_id' => $transaction->id,
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'uploaded_at' => now(),
            ]);

            $uploadedFiles[] = $file->getClientOriginalName();
        } catch (\Exception $e) {
            \Log::error("Gagal upload file '{$file->getClientOriginalName()}': {$e->getMessage()}");
            $failedFiles[] = $file->getClientOriginalName();
        }
    }

    // Buat pesan sukses / error
    $messages = [];
    if ($uploadedFiles) {
        $messages[] = "Berhasil mengupload: " . implode(', ', $uploadedFiles);
    }
    if ($failedFiles) {
        $messages[] = "Gagal mengupload: " . implode(', ', $failedFiles);
    }

    return back()->with('success', implode(' | ', $messages));
}

    /**
     * Hapus satu foto hasil.
     */
    public function destroy($id)
    {
        $photo = PhotoResult::findOrFail($id);

        if (Storage::disk('public')->exists($photo->file_path)) {
            Storage::disk('public')->delete($photo->file_path);
        }

        $photo->delete();

        return back()->with('success', 'Foto berhasil dihapus!');
    }

    /**
 * Hapus semua foto hasil untuk satu transaksi.
 */
public function destroyAll($transactionId)
{
    $transaction = Transaction::with('photoResults')->findOrFail($transactionId);

    if ($transaction->photoResults->isEmpty()) {
        return back()->with('error', 'Tidak ada foto untuk dihapus.');
    }

    // Hapus file dari storage
    foreach ($transaction->photoResults as $photo) {
        if (Storage::disk('public')->exists($photo->file_path)) {
            Storage::disk('public')->delete($photo->file_path);
        }
    }

    // Hapus record dari database
    $transaction->photoResults()->delete();

    return back()->with('success', 'Semua foto berhasil dihapus!');
}


    /**
     * ✅ Halaman publik hasil foto untuk user (akses tanpa login).
     */
    public function showPublic($token)
    {
        $transaction = Transaction::where('public_token', $token)
            ->with(['photoResults', 'user'])
            ->where('public_token_expires_at', '>=', now())
            ->first();

        if (!$transaction) {
            return response()->view('frontend.photo-results.expired', [], 410);
        }

        return view('frontend.photo-results.public', compact('transaction'));
    }

    /**
     * Regenerasi link publik (untuk admin panel).
     */
    public function regenerateLink(Transaction $transaction)
{
    $transaction->public_token = bin2hex(random_bytes(8));
    $transaction->public_token_expires_at = now()->addDays(1);
    $transaction->save();

    return back()->with('success', 'Link publik berhasil diperbarui!');
}

public function download($photoResult, $token)
{
    $photo = PhotoResult::findOrFail($photoResult);
    $transaction = $photo->transaction;

    if (!$transaction) {
        abort(404, 'Transaksi tidak ditemukan.');
    }

    if (
        $transaction->public_token !== $token ||
        !$transaction->public_token_expires_at ||
        now()->gt($transaction->public_token_expires_at)
    ) {
        abort(403, 'Link tidak valid atau sudah kedaluwarsa.');
    }

    if (!Storage::disk('public')->exists($photo->file_path)) {
        abort(404, 'File tidak ditemukan di storage.');
    }

    // Gunakan nama asli file
    $fileName = $photo->file_name;

    /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
    $disk = Storage::disk('public');

    return $disk->download($photo->file_path, $fileName);
}


public function downloadAll($token)
{
    $transaction = \App\Models\Transaction::where('public_token', $token)->firstOrFail();

    if (! $transaction->photoResults || $transaction->photoResults->isEmpty()) {
        return back()->with('error', 'Tidak ada foto untuk diunduh.');
    }

    $zip = new ZipArchive;
    $zipFileName = 'hasil-foto-' . ($transaction->transaction_code ?? 'booking') . '.zip';
    $zipPath = storage_path('app/public/temp/' . $zipFileName);

    // Buat folder temp kalau belum ada
    if (!file_exists(dirname($zipPath))) {
        mkdir(dirname($zipPath), 0777, true);
    }

    if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
        foreach ($transaction->photoResults as $photo) {
            $filePath = storage_path('app/public/' . $photo->file_path);
            if (file_exists($filePath)) {
                // Gunakan file_name agar tetap seperti nama asli saat upload
                $zip->addFile($filePath, $photo->file_name);
            }
        }
        $zip->close();
    } else {
        return back()->with('error', 'Gagal membuat arsip zip.');
    }

    return response()->download($zipPath)->deleteFileAfterSend(true);
}
public function sendWhatsappFonnte(Transaction $transaction)
{
    try {
        // Ambil nomor dari user atau appointment (guest)
        $userPhone = null;

        if ($transaction->user && !empty($transaction->user->phone)) {
            $userPhone = $transaction->user->phone;
        } elseif ($transaction->appointment && !empty($transaction->appointment->phone)) {
            $userPhone = $transaction->appointment->phone;
        }

        if (!$userPhone) {
            return back()->with('error', 'Nomor pelanggan tidak ditemukan.');
        }

        if (!$transaction->public_token) {
            return back()->with('error', 'Link hasil foto belum tersedia.');
        }

        // Ambil token dari .env
        $token = env('FONNTE_TOKEN');
        if (!$token) {
            \Log::warning('FONNTE_TOKEN not found in .env');
            return back()->with('error', 'Token Fonnte tidak ditemukan.');
        }

        // Normalisasi nomor ke format 62xxxx
        $waNumber = preg_replace('/[^0-9]/', '', $userPhone);
        if (preg_match('/^0/', $waNumber)) {
            $waNumber = '62' . substr($waNumber, 1);
        } elseif (!preg_match('/^62/', $waNumber)) {
            $waNumber = '62' . $waNumber;
        }

        // Buat pesan WhatsApp
        $message = "Halo, berikut link hasil foto Anda 📸:\n\n" .
            route('photo-result.public', $transaction->public_token) . "\n\n" .
            "Link ini berlaku hingga " .
            $transaction->public_token_expires_at->translatedFormat('d F Y - H:i') .
            " WIB\n\nTerima kasih telah berfoto bersama kami 🤝";

        // Kirim melalui Fonnte API
        $response = Http::withHeaders([
            'Authorization' => $token,
        ])->post('https://api.fonnte.com/send', [
            'target' => $waNumber,
            'message' => $message,
        ]);

        // Logging untuk debug
        \Log::info('Fonnte Send Response', [
            'number' => $waNumber,
            'response' => $response->body()
        ]);

        if ($response->successful()) {
            return back()->with('success', 'Pesan WhatsApp berhasil dikirim!');
        } else {
            return back()->with('error', 'Gagal mengirim pesan WhatsApp. Cek log untuk detail.');
        }

    } catch (\Exception $e) {
        \Log::error('Gagal kirim WhatsApp via Fonnte: ' . $e->getMessage());
        return back()->with('error', 'Terjadi kesalahan saat mengirim pesan WhatsApp.');
    }
}



}
