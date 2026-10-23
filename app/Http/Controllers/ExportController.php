<?php

namespace App\Http\Controllers;

use App\Exports\JadwalPeriksaExport;
use App\Exports\RiwayatPasienExport;
use App\Exports\UserExport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    // Role admin
    public function dokter()
    {
        return Excel::download(new UserExport('dokter'), 'data-dokter-' . now()->format('Ymd') . '.xlsx');
    }

    public function pasien()
    {
        return Excel::download(new UserExport('pasien'), 'data-pasien-' . now()->format('Ymd') . '.xlsx');
    }

    // Role dokter: hanya data milik dokter yang sedang login
    public function jadwalPeriksa()
    {
        return Excel::download(new JadwalPeriksaExport(Auth::id()), 'jadwal-periksa-' . now()->format('Ymd') . '.xlsx');
    }

    public function riwayatPasien()
    {
        return Excel::download(new RiwayatPasienExport(Auth::id()), 'riwayat-pasien-' . now()->format('Ymd') . '.xlsx');
    }
}