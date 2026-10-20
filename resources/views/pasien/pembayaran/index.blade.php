<x-layouts.app title="Pembayaran">

    @php
        $statusLabel = [
            'belum_bayar'         => ['Belum bayar', 'bg-red-100 text-red-600'],
            'menunggu_verifikasi' => ['Menunggu verifikasi', 'bg-amber-100 text-amber-600'],
            'lunas'               => ['Lunas', 'bg-green-100 text-green-600'],
        ];
    @endphp

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-slate-800">
            Pembayaran
        </h2>
    </div>

    <div class="space-y-4">
        @forelse($pembayarans as $pembayaran)
        @php
            $periksa = $pembayaran->periksa;
            $dokter = $periksa->daftarPoli->jadwalPeriksa->dokter;
            [$label, $warna] = $statusLabel[$pembayaran->status];
        @endphp

        <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200">
            <div class="card-body p-6">

                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-x-8 gap-y-3 text-sm">
                        <div>
                            <p class="text-slate-400 text-xs uppercase tracking-wider">Poli</p>
                            <p class="font-semibold text-slate-800">{{ $dokter->poli->nama_poli ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-slate-400 text-xs uppercase tracking-wider">Dokter</p>
                            <p class="font-semibold text-slate-800">{{ $dokter->nama }}</p>
                        </div>
                        <div>
                            <p class="text-slate-400 text-xs uppercase tracking-wider">Tanggal Periksa</p>
                            <p class="font-semibold text-slate-800">
                                {{ \Carbon\Carbon::parse($periksa->tgl_periksa)->format('d/m/Y') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-slate-400 text-xs uppercase tracking-wider">Total Tagihan</p>
                            <p class="font-bold text-slate-800">
                                Rp {{ number_format($periksa->biaya_periksa, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full {{ $warna }}">
                            {{ $label }}
                        </span>
                        <a href="{{ route('riwayat-pendaftaran.show', $periksa->id_daftar_poli) }}"
                            class="text-xs font-semibold text-indigo-600 hover:underline">
                            Lihat rincian
                        </a>
                    </div>
                </div>

                {{-- Belum bayar: form upload --}}
                @if($pembayaran->status === 'belum_bayar')
                <form method="POST" action="{{ route('pasien.pembayaran.upload', $pembayaran->id) }}"
                    enctype="multipart/form-data"
                    class="mt-4 pt-4 border-t border-slate-100 flex flex-col md:flex-row md:items-start gap-3">
                    @csrf
                    <div class="flex-1">
                        <label class="block text-sm font-semibold text-slate-700 mb-1">
                            Foto Bukti Pembayaran
                        </label>
                        <input type="file" name="bukti" accept="image/png,image/jpeg"
                            class="file-input file-input-bordered w-full" required>
                        @error('bukti', 'bukti' . $pembayaran->id)
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-slate-400 mt-1">Format JPG atau PNG, maksimal 2 MB.</p>
                    </div>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-primary hover:bg-primary/90
                               text-white font-semibold text-sm transition md:mt-6">
                        <i class="fas fa-upload mr-1"></i>
                        Upload Bukti
                    </button>
                </form>
                @endif

                {{-- Sudah upload --}}
                @if($pembayaran->status !== 'belum_bayar')
                <div class="mt-4 pt-4 border-t border-slate-100 flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-slate-600">
                    <span>
                        Bukti diunggah:
                        <strong>{{ $pembayaran->tgl_upload?->format('d/m/Y H:i') ?? '-' }}</strong>
                    </span>
                    @if($pembayaran->bukti_bayar)
                    <a href="{{ asset('storage/' . $pembayaran->bukti_bayar) }}" target="_blank"
                        class="font-semibold text-indigo-600 hover:underline">
                        <i class="fas fa-image mr-1"></i> Lihat bukti
                    </a>
                    @endif
                    @if($pembayaran->status === 'menunggu_verifikasi')
                    <span class="text-amber-600">Menunggu verifikasi admin</span>
                    @else
                    <span>
                        Diverifikasi:
                        <strong>{{ $pembayaran->tgl_verifikasi?->format('d/m/Y H:i') ?? '-' }}</strong>
                    </span>
                    @endif
                </div>
                @endif

            </div>
        </div>
        @empty
        <div class="card bg-base-100 shadow-md rounded-2xl border">
            <div class="card-body items-center text-center py-12 text-slate-400">
                <i class="fas fa-receipt text-3xl mb-3"></i>
                Belum ada tagihan. Tagihan muncul setelah dokter memeriksa Anda.
            </div>
        </div>
        @endforelse
    </div>

</x-layouts.app>