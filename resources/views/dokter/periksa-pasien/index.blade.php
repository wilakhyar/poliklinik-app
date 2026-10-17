<x-layouts.app title="Periksa Pasien">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-slate-800">
            Periksa Pasien
        </h2>
    </div>

    {{-- Card Table --}}
    <div class="card bg-base-100 shadow-md rounded-2xl border">
        <div class="card-body p-0">

            <div class="overflow-x-auto">
                <table class="table w-full">

                    {{-- Table Head --}}
                    <thead class="bg-slate-100 text-slate-600 text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-4">ID</th>
                            <th class="px-6 py-4">Pasien</th>
                            <th class="px-6 py-4">Keluhan</th>
                            <th class="px-6 py-4">No Antrian</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>

                    {{-- Table Body --}}
                    <tbody class="text-sm text-slate-700">

                        @forelse($daftarPasien as $daftar)
                        <tr class="border-b border-slate-100 hover:bg-slate-50 transition">

                            <td class="px-6 py-4">
                                {{ $daftar->id }}
                            </td>

                            <td class="px-6 py-4 font-semibold text-slate-800">
                                {{ $daftar->pasien->nama ?? '-' }}
                            </td>

                            <td class="px-6 py-4">
                                {{ $daftar->keluhan ?? '-' }}
                            </td>

                            <td class="px-6 py-4">
                                {{ $daftar->no_antrian }}
                            </td>

                            <td class="px-6 py-4 text-right">
                                @if($daftar->periksas->isNotEmpty())
                                {{-- Sudah diperiksa --}}
                                <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full
                                             bg-green-100 text-green-700 text-xs font-semibold">
                                    <i class="fas fa-circle-check text-xs"></i>
                                    Sudah Diperiksa
                                </span>
                                @else
                                {{-- Belum diperiksa --}}
                                <a href="{{ route('periksa-pasien.create', $daftar->id) }}"
                                    class="inline-flex items-center gap-1 px-3 py-2
                                           bg-amber-500 hover:bg-amber-600
                                           text-white rounded-lg text-xs font-semibold transition">
                                    <i class="fas fa-stethoscope text-xs"></i>
                                    Periksa
                                </a>
                                @endif
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-16 text-slate-400">
                                <i class="fas fa-inbox text-3xl mb-3 block"></i>
                                Belum ada pasien yang mendaftar
                            </td>
                        </tr>
                        @endforelse

                    </tbody>

                </table>
            </div>

        </div>
    </div>

</x-layouts.app>