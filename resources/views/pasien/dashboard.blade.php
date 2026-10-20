<x-layouts.app title="Dashboard Pasien">

    <div class="mb-8">
        <h2 class="text-2xl font-bold">Halo, {{ auth()->user()->nama }}</h2>
        <p class="text-sm text-slate-600">
            {{ now()->translatedFormat('l, d F Y') }} - Berikut informasi antrian poliklinik
        </p>
    </div>

    {{-- Banner antrian aktif --}}
    @if($antrianAktif)
    @php
        $jadwalAktif = $antrianAktif->jadwalPeriksa;
        $nomorDilayani = $antrianAktif->tanggal->isToday()
            ? $sedangDilayani->get($jadwalAktif->id, '-')
            : '-';
    @endphp

    <div class="rounded-2xl p-6 mb-8 text-white shadow-lg flex flex-col md:flex-row md:items-center md:justify-between gap-6"
        style="background: linear-gradient(135deg, #1e2d6b 0%, #2d4499 60%, #3b5bdb 100%);">

        <div class="space-y-3">
            <p class="text-xs font-bold uppercase tracking-widest text-indigo-200">Antrian Aktif Anda</p>

            <div>
                <p class="text-[11px] text-indigo-200">Poliklinik</p>
                <p class="text-xl font-bold">{{ $jadwalAktif->dokter->poli->nama_poli ?? '-' }}</p>
            </div>
            <div>
                <p class="text-[11px] text-indigo-200">Dokter</p>
                <p class="font-semibold">{{ $jadwalAktif->dokter->nama }}</p>
            </div>
            <div>
                <p class="text-[11px] text-indigo-200">Jadwal Periksa</p>
                <p class="font-semibold">
                    {{ $jadwalAktif->hari }}, {{ $antrianAktif->tanggal->format('d/m/Y') }}
                    ({{ \Carbon\Carbon::parse($jadwalAktif->jam_mulai)->format('H:i') }}
                    - {{ \Carbon\Carbon::parse($jadwalAktif->jam_selesai)->format('H:i') }})
                </p>
            </div>
        </div>

        <div class="flex gap-4">
            <div class="w-32 rounded-xl bg-white/15 border border-white/20 p-4 text-center">
                <p class="text-[11px] uppercase text-indigo-100">Nomor Anda</p>
                <p class="text-4xl font-extrabold mt-1">{{ $antrianAktif->no_antrian }}</p>
            </div>
            <div class="w-36 rounded-xl bg-white text-slate-800 p-4 text-center">
                <p class="text-[11px] uppercase text-slate-500">Sedang Dilayani</p>
                <p id="bannerDilayani" data-jadwal="{{ $jadwalAktif->id }}"
                    class="text-4xl font-extrabold mt-1 text-indigo-700">{{ $nomorDilayani }}</p>
            </div>
        </div>
    </div>
    @else
    <div class="card bg-base-100 shadow-md rounded-2xl border mb-8">
        <div class="card-body items-center text-center py-10">
            <i class="fas fa-ticket text-3xl text-slate-300 mb-2"></i>
            <p class="font-semibold text-slate-700">Anda belum memiliki antrian aktif</p>
            <p class="text-sm text-slate-500 mb-4">Daftar ke poli untuk mendapatkan nomor antrian.</p>
            <a href="{{ route('pasien.daftar') }}" class="inline-flex items-center gap-2 px-5 py-2.5
                      bg-primary hover:bg-primary/90 text-white text-sm font-semibold rounded-xl transition">
                <i class="fas fa-house-medical text-xs"></i>
                Daftar Poli
            </a>
        </div>
    </div>
    @endif

    {{-- Tabel jadwal --}}
    <div class="card bg-base-100 shadow-md rounded-2xl border">
        <div class="card-body p-0">
            <div class="px-6 py-4 border-b border-slate-100 font-semibold text-slate-800">
                Jadwal Poliklinik
            </div>

            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-xs tracking-wider">
                        <tr>
                            <th class="px-6 py-4">No</th>
                            <th class="px-6 py-4">Poli</th>
                            <th class="px-6 py-4">Dokter</th>
                            <th class="px-6 py-4">Hari</th>
                            <th class="px-6 py-4">Jam Periksa</th>
                            <th class="px-6 py-4 text-center">Sedang Dilayani</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-slate-700">
                        @forelse($jadwals as $jadwal)
                        <tr class="border-t border-slate-100 {{ $antrianAktif && $antrianAktif->id_jadwal == $jadwal->id ? 'bg-indigo-50' : '' }}">
                            <td class="px-6 py-4">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4 font-semibold text-slate-800">
                                {{ $jadwal->dokter->poli->nama_poli ?? '-' }}
                            </td>
                            <td class="px-6 py-4">{{ $jadwal->dokter->nama }}</td>
                            <td class="px-6 py-4">{{ $jadwal->hari }}</td>
                            <td class="px-6 py-4">
                                {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}
                                - {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="nomor-dilayani inline-block min-w-8 px-3 py-1 rounded-full bg-indigo-100 text-indigo-700 font-bold"
                                    data-jadwal="{{ $jadwal->id }}">{{ $sedangDilayani->get($jadwal->id, '-') }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-slate-400">
                                <i class="fas fa-inbox text-3xl mb-3 block"></i>
                                Belum ada jadwal poliklinik
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</x-layouts.app>