<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Notifications\PembayaranDiunggah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PembayaranController extends Controller
{
    public function index()
    {
        $pembayarans = Pembayaran::with('periksa.daftarPoli.jadwalPeriksa.dokter.poli')
            ->whereHas('periksa.daftarPoli', function ($query) {
                $query->where('id_pasien', Auth::id());
            })
            ->orderByDesc('id')
            ->get();

        return view('pasien.pembayaran.index', compact('pembayarans'));
    }

    public function upload(Request $request, $id)
    {
        // Hanya tagihan milik pasien yang sedang login (selain itu 404)
        $pembayaran = Pembayaran::with('periksa.daftarPoli.jadwalPeriksa.dokter')
            ->whereHas('periksa.daftarPoli', function ($query) {
                $query->where('id_pasien', Auth::id());
            })
            ->findOrFail($id);

        $request->validateWithBag('bukti' . $id, [
            'bukti' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ], [
            'bukti.required' => 'Foto bukti pembayaran wajib diunggah.',
            'bukti.image'    => 'File harus berupa gambar.',
            'bukti.mimes'    => 'Format file harus JPG atau PNG.',
            'bukti.max'      => 'Ukuran file maksimal 2 MB.',
        ]);

        $berhasil = DB::transaction(function () use ($request, $pembayaran) {
            // Kunci baris tagihan supaya klik ganda tidak diproses dua kali
            $tagihan = Pembayaran::lockForUpdate()->find($pembayaran->id);

            if ($tagihan->status !== 'belum_bayar') {
                return false;
            }

            $path = $request->file('bukti')->store('bukti-bayar', 'public');

            $tagihan->update([
                'bukti_bayar' => $path,
                'status'      => 'menunggu_verifikasi',
                'tgl_upload'  => now(),
            ]);

            // Satu kejadian = satu notifikasi, ke dokter yang memeriksa pasien ini
            $dokter = $pembayaran->periksa->daftarPoli->jadwalPeriksa->dokter;
            $dokter->notify(new PembayaranDiunggah($tagihan->id_periksa, Auth::user()->nama));

            return true;
        });

        if (! $berhasil) {
            return back()->with('error', 'Bukti pembayaran untuk tagihan ini sudah diunggah.');
        }

        return back()->with('success', 'Bukti pembayaran berhasil diunggah. Menunggu verifikasi admin.');
    }
}