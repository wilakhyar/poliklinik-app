<?php

namespace App\Http\Controllers\Dokter;

use App\Events\AntrianDiperbarui;
use App\Http\Controllers\Controller;
use App\Models\DaftarPoli;
use App\Models\DetailPeriksa;
use App\Models\Obat;
use App\Models\Pembayaran;
use App\Models\Periksa;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PeriksaPasienController extends Controller
{
    public function index()
    {
        $dokterId = Auth::id();

        $daftarPasien = DaftarPoli::with(['pasien', 'jadwalPeriksa', 'periksas'])
            ->whereHas('jadwalPeriksa', function ($query) use ($dokterId) {
                $query->where('id_dokter', $dokterId);
            })
            ->orderBy('no_antrian')
            ->get();

        return view('dokter.periksa-pasien.index', compact('daftarPasien'));
    }


    public function create($id)
    {
        $daftar = DaftarPoli::with('pasien')
            ->whereHas('jadwalPeriksa', function ($query) {
                $query->where('id_dokter', Auth::id());
            })
            ->findOrFail($id);

        if ($daftar->periksas()->exists()) {
            return redirect()
                ->route('periksa-pasien.index')
                ->with('error', 'Pasien ini sudah diperiksa.');
        }

        // Tandai pasien ini sebagai sedang dilayani (hanya untuk antrian hari ini)
        if ($daftar->tanggal?->isToday() && $daftar->status !== 'dilayani') {
            DB::transaction(function () use ($daftar) {
                // Pada satu jadwal dan tanggal hanya boleh satu pasien yang sedang dilayani
                DaftarPoli::where('id_jadwal', $daftar->id_jadwal)
                    ->whereDate('tanggal', $daftar->tanggal)
                    ->where('status', 'dilayani')
                    ->update(['status' => 'menunggu']);

                $daftar->update(['status' => 'dilayani']);
            });

            // Siarkan nomor terbaru ke semua dashboard pasien (setelah data tersimpan)
            $this->siarkanAntrian($daftar);
        }

        $obats = Obat::orderBy('nama_obat')->get();


        return view('dokter.periksa-pasien.create', compact('daftar', 'obats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_daftar_poli' => 'required|exists:daftar_poli,id',
            'obat_json'      => 'required|json',
            'catatan'        => 'nullable|string',
        ]);

        // Rapikan data obat: gabungkan id yang sama, buang jumlah <= 0
        $items = [];
        foreach (json_decode($request->obat_json, true) ?? [] as $row) {
            $idObat = (int) ($row['id'] ?? 0);
            $jumlah = (int) ($row['jumlah'] ?? 0);

            if ($idObat > 0 && $jumlah > 0) {
                $items[$idObat] = ($items[$idObat] ?? 0) + $jumlah;
            }
        }

        // Pastikan pasien ini memang terdaftar di jadwal dokter yang sedang login
        $daftarPoli = DaftarPoli::whereHas('jadwalPeriksa', function ($query) {
                $query->where('id_dokter', Auth::id());
            })
            ->findOrFail($request->id_daftar_poli);

        DB::transaction(function () use ($daftarPoli, $items, $request) {
            // Kunci baris pendaftaran supaya tidak diproses dua kali
            $daftar = DaftarPoli::lockForUpdate()->find($daftarPoli->id);


            if ($daftar->periksas()->exists()) {
                throw ValidationException::withMessages([
                    'obat_json' => 'Pasien ini sudah diperiksa.',
                ]);
            }

            // Kunci baris obat yang dipakai, lalu cek stok
            $obats = Obat::whereIn('id', array_keys($items))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $totalObat = 0;
            foreach ($items as $idObat => $jumlah) {
                $obat = $obats->get($idObat);

                if (! $obat) {
                    throw ValidationException::withMessages([
                        'obat_json' => 'Ada obat yang tidak ditemukan.',
                    ]);
                }

                if ($obat->stok < $jumlah) {
                    throw ValidationException::withMessages([
                        'obat_json' => "Stok {$obat->nama_obat} tidak cukup (sisa {$obat->stok}).",
                    ]);
                }

                $totalObat += $obat->harga * $jumlah;
            }


            // Simpan hasil periksa. Biaya dihitung di server: jasa dokter + total obat
            $periksa = Periksa::create([
                'id_daftar_poli' => $daftar->id,
                'tgl_periksa'    => now(),
                'catatan'        => $request->catatan,
                'biaya_periksa'  => 150000 + $totalObat,
            ]);

            // Simpan detail obat dan kurangi stok
            foreach ($items as $idObat => $jumlah) {
                DetailPeriksa::create([
                    'id_periksa' => $periksa->id,
                    'id_obat'    => $idObat,
                    'jumlah'     => $jumlah,
                ]);

                $obats->get($idObat)->decrement('stok', $jumlah);
            }

            // Buat tagihan untuk pasien
            Pembayaran::create([
                'id_periksa' => $periksa->id,
                'status'     => 'belum_bayar',
            ]);

            $daftar->update(['status' => 'selesai']);
        });

        // Transaksi sudah selesai: siarkan bahwa pasien ini tidak lagi dilayani
        $this->siarkanAntrian($daftarPoli);

        return redirect()

            ->route('periksa-pasien.index')
            ->with('success', 'Data periksa berhasil disimpan.');
    }

    /**
     * Kirim nomor yang sedang dilayani pada jadwal ini ke semua dashboard pasien.
     */
    private function siarkanAntrian(DaftarPoli $daftar): void
    {
        // Dashboard hanya menampilkan antrian hari ini
        if (! Carbon::parse($daftar->tanggal)->isToday()) {
            return;
        }

        $nomor = DaftarPoli::where('id_jadwal', $daftar->id_jadwal)
            ->whereDate('tanggal', today())
            ->where('status', 'dilayani')
            ->value('no_antrian');

        AntrianDiperbarui::dispatch($daftar->id_jadwal, $nomor ?? '-');
    }
}