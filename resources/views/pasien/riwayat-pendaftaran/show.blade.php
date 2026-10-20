<x-layouts.app title="Detail Riwayat">

    @php
        $totalObat = $periksa->detailPeriksas->sum(fn ($d) => $d->obat->harga * $d->jumlah);
        $jasaDokter = $periksa->biaya_periksa - $totalObat;
    @endphp

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('riwayat-pendaftaran.index') }}" class="flex items-center justify-center w-9 h-9
                  rounded-lg bg-slate-100 hover:bg-slate-200
                  text-slate-600 transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>

        <h2 class="text-2xl font-bold text-slate-800">
            Detail Pemeriksaan
        </h2>
    </div>

    {{-- Informasi --}}
    <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200 mb-6">
        <div class="card-body p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm">
                <div>
                    <p class="text-slate-400 text-xs uppercase tracking-wider">Poli</p>
                    <p class="font-semibold text-slate-800">{{ $daftar->jadwalPeriksa->dokter->poli->nama_poli ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-slate-400 text-xs uppercase tracking-wider">Dokter</p>
                    <p class="font-semibold text-slate-800">{{ $daftar->jadwalPeriksa->dokter->nama ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-slate-400 text-xs uppercase tracking-wider">Jadwal</p>
                    <p class="font-semibold text-slate-800">
                        {{ $daftar->jadwalPeriksa->hari }}, {{ $daftar->tanggal?->format('d/m/Y') ?? '-' }}
                    </p>
                </div>
                <div>
                    <p class="text-slate-400 text-xs uppercase tracking-wider">No Antrian</p>
                    <p class="font-semibold text-slate-800">{{ $daftar->no_antrian }}</p>
                </div>
                <div>
                    <p class="text-slate-400 text-xs uppercase tracking-wider">Tanggal Periksa</p>
                    <p class="font-semibold text-slate-800">
                        {{ \Carbon\Carbon::parse($periksa->tgl_periksa)->format('d/m/Y H:i') }}
                    </p>
                </div>
                <div>
                    <p class="text-slate-400 text-xs uppercase tracking-wider">Keluhan</p>
                    <p class="font-semibold text-slate-800">{{ $daftar->keluhan ?: '-' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Catatan dokter --}}
    <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200 mb-6">
        <div class="card-body p-6">
            <p class="text-slate-400 text-xs uppercase tracking-wider mb-1">Catatan Dokter</p>
            <p class="text-sm text-slate-800">{{ $periksa->catatan ?: 'Tidak ada catatan.' }}</p>
        </div>
    </div>

    {{-- Obat --}}
    <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200 mb-6">
        <div class="card-body p-0">
            <div class="px-6 py-4 border-b border-slate-100 font-semibold text-slate-800">
                Obat yang Diresepkan
            </div>
            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-xs tracking-wider">
                        <tr>
                            <th class="px-6 py-3">Obat</th>
                            <th class="px-6 py-3">Harga</th>
                            <th class="px-6 py-3 text-center">Jumlah</th>
                            <th class="px-6 py-3 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-slate-700">
                        @forelse($periksa->detailPeriksas as $detail)
                        <tr class="border-t border-slate-100">
                            <td class="px-6 py-3 font-semibold text-slate-800">{{ $detail->obat->nama_obat }}</td>
                            <td class="px-6 py-3">Rp {{ number_format($detail->obat->harga, 0, ',', '.') }}</td>
                            <td class="px-6 py-3 text-center">{{ $detail->jumlah }}</td>
                            <td class="px-6 py-3 text-right font-semibold">
                                Rp {{ number_format($detail->obat->harga * $detail->jumlah, 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-8 text-slate-400">Tidak ada obat yang diresepkan</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Ringkasan biaya --}}
    <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200">
        <div class="card-body p-6 text-sm space-y-2">
            <div class="flex justify-between">
                <span class="text-slate-500">Jasa dokter</span>
                <span class="font-semibold">Rp {{ number_format($jasaDokter, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Total obat</span>
                <span class="font-semibold">Rp {{ number_format($totalObat, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between border-t border-slate-200 pt-3 text-base">
                <span class="font-semibold text-slate-700">Total biaya</span>
                <span class="font-bold text-slate-800">Rp {{ number_format($periksa->biaya_periksa, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

</x-layouts.app>