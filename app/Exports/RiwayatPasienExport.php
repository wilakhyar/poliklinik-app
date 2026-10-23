<?php

namespace App\Exports;

use App\Models\Periksa;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RiwayatPasienExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    private int $no = 0;

    public function __construct(private int $idDokter) {}

    public function collection(): Collection
    {
        // Eager loading: satu query untuk relasi, bukan satu per baris
        return Periksa::whereHas('daftarPoli.jadwalPeriksa', function ($query) {
                $query->where('id_dokter', $this->idDokter);
            })
            ->with(['daftarPoli.pasien', 'detailPeriksas.obat'])
            ->orderBy('tgl_periksa', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return ['No', 'Tanggal Periksa', 'Nama Pasien', 'No RM', 'Keluhan', 'Catatan', 'Obat', 'Biaya Periksa'];

    }

    public function map($periksa): array
    {
        $this->no++;

        $daftar = $periksa->daftarPoli;
        $obat = $periksa->detailPeriksas
            ->map(fn ($d) => ($d->obat->nama_obat ?? '-') . ' x' . ($d->jumlah ?? 1))
            ->implode(', ');

        return [
            $this->no,
            Carbon::parse($periksa->tgl_periksa)->format('d/m/Y H:i'),
            $daftar->pasien->nama ?? '-',
            $daftar->pasien->no_rm ?? '-',
            $daftar->keluhan,
            $periksa->catatan ?? '-',
            $obat ?: '-',
            $periksa->biaya_periksa,
        ];
    }
}