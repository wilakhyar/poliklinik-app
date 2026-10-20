<x-layouts.app title="Riwayat Pendaftaran">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-slate-800">
            Riwayat Pendaftaran Poli
        </h2>
    </div>

    {{-- Card --}}
    <div class="card bg-base-100 shadow-md rounded-2xl border">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table w-full">

                    <thead class="bg-slate-50 text-slate-500 uppercase text-xs tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Poli</th>
                            <th class="px-6 py-4">Dokter</th>
                            <th class="px-6 py-4">Tanggal Jadwal</th>
                            <th class="px-6 py-4 text-center">No Antrian</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="text-sm text-slate-700">
                        @forelse($riwayats as $riwayat)
                        @php
                            $periksa = $riwayat->periksas->first();

                            if ($periksa) {
                                $label = 'Sudah diperiksa';
                                $warna = 'bg-green-100 text-green-600';
                            } elseif ($riwayat->status === 'dilayani') {
                                $label = 'Sedang dilayani';
                                $warna = 'bg-amber-100 text-amber-600';
                            } elseif ($riwayat->tanggal && $riwayat->tanggal->lt(today())) {
                                $label = 'Terlewat';
                                $warna = 'bg-slate-100 text-slate-500';
                            } else {
                                $label = 'Menunggu';
                                $warna = 'bg-indigo-100 text-indigo-600';
                            }
                        @endphp
                        <tr class="border-t border-slate-100 hover:bg-slate-50 transition">
                            <td class="px-6 py-4 font-semibold text-slate-800">
                                {{ $riwayat->jadwalPeriksa->dokter->poli->nama_poli ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $riwayat->jadwalPeriksa->dokter->nama ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $riwayat->jadwalPeriksa->hari ?? '-' }},
                                {{ $riwayat->tanggal?->format('d/m/Y') ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-center font-semibold">
                                {{ $riwayat->no_antrian }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full {{ $warna }}">
                                    {{ $label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($periksa)
                                <a href="{{ route('riwayat-pendaftaran.show', $riwayat->id) }}"
                                    class="inline-flex items-center gap-1 px-4 py-2
                                           bg-primary hover:bg-primary/90
                                           text-white text-xs font-semibold
                                           rounded-lg transition">
                                    <i class="fas fa-eye text-xs"></i>
                                    Detail
                                </a>
                                @else
                                <span class="text-slate-300">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-slate-400">
                                <i class="fas fa-inbox text-3xl mb-3 block"></i>
                                Belum ada riwayat pendaftaran
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>
    </div>

    <div class="mt-4">
        {{ $riwayats->links() }}
    </div>

</x-layouts.app>