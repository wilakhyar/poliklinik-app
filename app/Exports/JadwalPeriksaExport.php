<?php

namespace App\Exports;

use App\Models\JadwalPeriksa;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class JadwalPeriksaExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    private int $no = 0;

    public function __construct(private int $idDokter) {}

    public function collection(): Collection
    {
        return JadwalPeriksa::where('id_dokter', $this->idDokter)
            ->orderByRaw("FIELD(hari, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu')")
            ->orderBy('jam_mulai')
            ->get();
    }

    public function headings(): array
    {
        return ['No', 'Hari', 'Jam Mulai', 'Jam Selesai'];
    }

    public function map($jadwal): array

    {
        $this->no++;

        return [
            $this->no,
            $jadwal->hari,
            Carbon::parse($jadwal->jam_mulai)->format('H:i'),
            Carbon::parse($jadwal->jam_selesai)->format('H:i'),
        ];
    }
}