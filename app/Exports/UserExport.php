<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class UserExport extends DefaultValueBinder implements
    FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithCustomValueBinder
{
    private int $no = 0;

    public function __construct(private string $role) {}

    public function collection(): Collection
    {
        return User::where('role', $this->role)
            ->when($this->role === 'dokter', fn ($q) => $q->with('poli'))
            ->orderBy('nama')
            ->get();
    }

    public function headings(): array
    {

        return $this->role === 'dokter'
            ? ['No', 'Nama', 'Email', 'No KTP', 'No HP', 'Alamat', 'Poli']
            : ['No', 'Nama', 'No RM', 'Email', 'No KTP', 'No HP', 'Alamat'];
    }

    public function map($user): array
    {
        $this->no++;

        if ($this->role === 'dokter') {
            return [
                $this->no, $user->nama, $user->email, $user->no_ktp,
                $user->no_hp, $user->alamat, $user->poli->nama_poli ?? '-',
            ];
        }

        return [
            $this->no, $user->nama, $user->no_rm ?? '-', $user->email,
            $user->no_ktp, $user->no_hp, $user->alamat,
        ];
    }

    // Paksa kolom No KTP dan No HP menjadi teks agar tidak berubah jadi 3.37E+15
    public function bindValue(Cell $cell, mixed $value): bool
    {
        $kolomTeks = $this->role === 'dokter' ? ['D', 'E'] : ['E', 'F'];

        if (in_array($cell->getColumn(), $kolomTeks, true)) {
            $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);

            return true;
        }


        return parent::bindValue($cell, $value);
    }
}