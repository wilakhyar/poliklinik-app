<?php

namespace App\Exports;

use App\Models\Obat;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Collection;

class ObatExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    private int $no = 0;

    public function collection(): Collection
    {
        return Obat::orderBy('nama_obat')->get();
    }

    public function headings(): array
    {
        return ['No', 'Nama Obat', 'Kemasan', 'Harga', 'Stok'];
    }

    public function map($obat): array
    {
        $this->no++;

        return [
            $this->no,
            $obat->nama_obat,
            $obat->kemasan ?? '-',
            $obat->harga,
            $obat->stok,
        ];
    }
}