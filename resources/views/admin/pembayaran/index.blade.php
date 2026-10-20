<x-layouts.app title="Verifikasi Pembayaran">

    @php
        $statusLabel = [
            'belum_bayar'         => ['Belum bayar', 'bg-red-100 text-red-600'],
            'menunggu_verifikasi' => ['Menunggu verifikasi', 'bg-amber-100 text-amber-600'],
            'lunas'               => ['Lunas', 'bg-green-100 text-green-600'],
        ];

        $tabs = [
            'menunggu_verifikasi' => 'Menunggu verifikasi',
            'lunas'               => 'Lunas',
            'belum_bayar'         => 'Belum bayar',
            'semua'               => 'Semua',
        ];
    @endphp

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-slate-800">
            Verifikasi Pembayaran
        </h2>
    </div>

    {{-- Filter status --}}
    <div class="flex flex-wrap gap-2 mb-4">
        @foreach($tabs as $kunci => $nama)
        <a href="{{ route('admin.pembayaran.index', ['status' => $kunci]) }}"
            class="px-4 py-2 rounded-lg text-sm font-semibold transition
                   {{ $status === $kunci ? 'bg-primary text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
            {{ $nama }}
        </a>
        @endforeach
    </div>

    {{-- Card --}}
    <div class="card bg-base-100 shadow-md rounded-2xl border">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table w-full">

                    <thead class="bg-slate-50 text-slate-500 uppercase text-xs tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Pasien</th>
                            <th class="px-6 py-4">Poli / Dokter</th>
                            <th class="px-6 py-4">Tgl Periksa</th>
                            <th class="px-6 py-4">Total</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Bukti</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="text-sm text-slate-700">
                        @forelse($pembayarans as $pembayaran)
                        @php
                            $periksa = $pembayaran->periksa;
                            $daftar = $periksa->daftarPoli;
                            $dokter = $daftar->jadwalPeriksa->dokter;
                            [$label, $warna] = $statusLabel[$pembayaran->status];
                        @endphp
                        <tr class="border-t border-slate-100 hover:bg-slate-50 transition">
                            <td class="px-6 py-4 font-semibold text-slate-800">{{ $daftar->pasien->nama }}</td>
                            <td class="px-6 py-4">
                                <p class="font-semibold">{{ $dokter->poli->nama_poli ?? '-' }}</p>
                                <p class="text-xs text-slate-500">{{ $dokter->nama }}</p>
                            </td>
                            <td class="px-6 py-4">
                                {{ \Carbon\Carbon::parse($periksa->tgl_periksa)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 font-semibold">
                                Rp {{ number_format($periksa->biaya_periksa, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full {{ $warna }}">
                                    {{ $label }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($pembayaran->bukti_bayar)
                                <a href="{{ asset('storage/' . $pembayaran->bukti_bayar) }}" target="_blank"
                                    class="inline-flex items-center gap-2 text-indigo-600 font-semibold hover:underline">
                                    <img src="{{ asset('storage/' . $pembayaran->bukti_bayar) }}" alt="Bukti"
                                        class="w-12 h-12 object-cover rounded-lg border border-slate-200">
                                    Lihat
                                </a>
                                @else
                                <span class="text-slate-300">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($pembayaran->status === 'menunggu_verifikasi')
                                <form method="POST" action="{{ route('admin.pembayaran.verifikasi', $pembayaran->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        onclick="return confirm('Konfirmasi pembayaran ini sebagai lunas?')"
                                        class="inline-flex items-center gap-1 px-4 py-2
                                               bg-green-500 hover:bg-green-600
                                               text-white text-xs font-semibold
                                               rounded-lg transition">
                                        <i class="fas fa-check text-xs"></i>
                                        Konfirmasi Lunas
                                    </button>
                                </form>
                                @else
                                <span class="text-slate-300">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-slate-400">
                                <i class="fas fa-inbox text-3xl mb-3 block"></i>
                                Tidak ada tagihan pada kategori ini
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>
    </div>

    <div class="mt-4">
        {{ $pembayarans->links() }}
    </div>

</x-layouts.app>