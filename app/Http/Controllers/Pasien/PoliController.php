<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use App\Models\DaftarPoli;
use App\Models\JadwalPeriksa;
use App\Models\Poli;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PoliController extends Controller
{
    // Nomor hari sesuai Carbon: Minggu = 0 ... Sabtu = 6
    private const HARI = [
        'Minggu' => 0, 'Senin' => 1, 'Selasa' => 2, 'Rabu' => 3,
        'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6,
    ];

    public function get()
    {
        $user = Auth::user();
        $polis = Poli::all();
        $jadwal = JadwalPeriksa::with('dokter', 'dokter.poli')->get();

        return view('pasien.daftar', [
            'user'    => $user,
            'polis'   => $polis,
            'jadwals' => $jadwal,
        ]);
    }

    public function submit(Request $request)
    {
        $request->validate([
            'id_poli'   => 'required|exists:poli,id',
            'id_jadwal' => 'required|exists:jadwal_periksa,id',
            'keluhan'   => 'required|string',
        ]);

        $pasienId = Auth::id();

        // Aturan: tidak boleh punya dua antrian aktif, daftar lagi setelah pemeriksaan selesai
        $punyaAntrianAktif = DaftarPoli::where('id_pasien', $pasienId)
            ->whereIn('status', ['menunggu', 'dilayani'])
            ->whereDate('tanggal', '>=', today())
            ->exists();

        if ($punyaAntrianAktif) {
            return back()->withInput()->with(
                'error',
                'Anda masih memiliki antrian aktif. Anda dapat mendaftar lagi setelah pemeriksaan selesai.'
            );
        }

        $jadwal = JadwalPeriksa::with('dokter')->findOrFail($request->id_jadwal);

        // Pastikan jadwal memang milik poli yang dipilih
        if ((int) $jadwal->dokter->id_poli !== (int) $request->id_poli) {
            return back()->withInput()->with('error', 'Jadwal tidak sesuai dengan poli yang dipilih.');
        }

        $tanggal = $this->tanggalPelaksanaan($jadwal);

        DB::transaction(function () use ($pasienId, $jadwal, $tanggal, $request) {
            // Nomor antrian: urutan berikutnya pada jadwal dan tanggal yang sama
            $nomorTerakhir = DaftarPoli::where('id_jadwal', $jadwal->id)
                ->whereDate('tanggal', $tanggal)
                ->lockForUpdate()
                ->max('no_antrian');

            DaftarPoli::create([
                'id_pasien'  => $pasienId,
                'id_jadwal'  => $jadwal->id,
                'tanggal'    => $tanggal->toDateString(),
                'keluhan'    => $request->keluhan,
                'no_antrian' => ($nomorTerakhir ?? 0) + 1,
                'status'     => 'menunggu',
            ]);
        });

        return redirect()
            ->route('pasien.dashboard')
            ->with('success', 'Berhasil mendaftar ke poli.');
    }

    /**
     * Tanggal pelaksanaan = kemunculan berikutnya dari hari jadwal.
     * Jika jadwalnya hari ini tetapi jam selesai sudah lewat, pindah ke pekan depan.
     */
    private function tanggalPelaksanaan(JadwalPeriksa $jadwal): Carbon
    {
        $hariIni = Carbon::today();
        $selisih = (self::HARI[$jadwal->hari] - $hariIni->dayOfWeek + 7) % 7;

        if ($selisih === 0 && now()->format('H:i:s') >= $jadwal->jam_selesai) {
            $selisih = 7;
        }

        return $hariIni->addDays($selisih);
    }
}