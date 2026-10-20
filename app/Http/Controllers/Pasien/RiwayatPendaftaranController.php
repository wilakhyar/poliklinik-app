<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use App\Models\DaftarPoli;
use Illuminate\Support\Facades\Auth;

class RiwayatPendaftaranController extends Controller
{
    public function index()
    {
        $riwayats = DaftarPoli::with(['jadwalPeriksa.dokter.poli', 'periksas'])
            ->where('id_pasien', Auth::id())
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->paginate(10);

        return view('pasien.riwayat-pendaftaran.index', compact('riwayats'));
    }

    public function show($id)
    {
        $daftar = DaftarPoli::with([
                'jadwalPeriksa.dokter.poli',
                'periksas.detailPeriksas.obat',
            ])
            ->where('id_pasien', Auth::id())
            ->findOrFail($id);

        $periksa = $daftar->periksas->first();

        // Detail hanya ada kalau pasien sudah diperiksa
        abort_if(! $periksa, 404);

        return view('pasien.riwayat-pendaftaran.show', compact('daftar', 'periksa'));
    }
}