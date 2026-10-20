<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Notifications\PembayaranDiverifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PembayaranController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'menunggu_verifikasi');

        $query = Pembayaran::with([
            'periksa.daftarPoli.pasien',
            'periksa.daftarPoli.jadwalPeriksa.dokter.poli',
        ]);

        if (in_array($status, ['menunggu_verifikasi', 'lunas', 'belum_bayar'], true)) {
            $query->where('status', $status);
        } else {
            $status = 'semua';
        }

        $pembayarans = $query->orderByDesc('id')->paginate(15)->withQueryString();

        return view('admin.pembayaran.index', compact('pembayarans', 'status'));
    }

    public function verifikasi($id)
    {
        $berhasil = DB::transaction(function () use ($id) {
            // Kunci baris tagihan supaya klik ganda tidak diproses dua kali
            $pembayaran = Pembayaran::with('periksa.daftarPoli.pasien')
                ->lockForUpdate()
                ->findOrFail($id);

            if ($pembayaran->status !== 'menunggu_verifikasi') {
                return false;
            }

            $pembayaran->update([
                'status'         => 'lunas',
                'tgl_verifikasi' => now(),
            ]);

            // Satu kejadian = satu notifikasi, ke pasien pemilik tagihan
            $pembayaran->periksa->daftarPoli->pasien
                ->notify(new PembayaranDiverifikasi($pembayaran->id_periksa));

            return true;
        });

        if (! $berhasil) {
            return back()->with('error', 'Pembayaran ini tidak dalam status menunggu verifikasi.');
        }

        return back()->with('success', 'Pembayaran berhasil diverifikasi dan berstatus lunas.');
    }
}