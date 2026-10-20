<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use App\Models\DaftarPoli;
use App\Models\JadwalPeriksa;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $hariIni = today();

        // Antrian aktif milik pasien yang sedang login (yang terdekat)
        $antrianAktif = DaftarPoli::with('jadwalPeriksa.dokter.poli')
            ->where('id_pasien', Auth::id())
            ->whereIn('status', ['menunggu', 'dilayani'])
            ->whereDate('tanggal', '>=', $hariIni)
            ->orderBy('tanggal')
            ->first();

        // Semua jadwal poliklinik
        $jadwals = JadwalPeriksa::with('dokter.poli')
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get();

        // Nomor yang sedang dilayani hari ini: [id_jadwal => no_antrian]
        $sedangDilayani = DaftarPoli::where('status', 'dilayani')
            ->whereDate('tanggal', $hariIni)
            ->pluck('no_antrian', 'id_jadwal');

        return view('pasien.dashboard', compact('antrianAktif', 'jadwals', 'sedangDilayani'));
    }
}