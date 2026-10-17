<x-layouts.app title="Pendaftaran Periksa">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-slate-800">
            Pendaftaran Periksa
        </h2>
    </div>

    {{-- Flash message dari controller --}}
    @if(session('message'))
    <div class="alert {{ session('type') === 'success' ? 'alert-success' : 'alert-error' }} mb-4 rounded-xl shadow-sm">
        <i class="fas {{ session('type') === 'success' ? 'fa-check-circle' : 'fa-circle-xmark' }}"></i>
        <span>{{ session('message') }}</span>
    </div>
    @endif

    {{-- Card Form --}}
    <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200">
        <div class="card-body p-8">

            <form action="{{ route('pasien.daftar.submit') }}" method="POST">
                @csrf

                {{-- Data pasien yang login --}}
                <input type="hidden" name="id_pasien" value="{{ $user->id }}">

                {{-- Nama Pasien --}}
                <div class="form-control mb-6">
                    <label class="label">
                        <span class="label-text font-semibold text-slate-700 text-sm">
                            Nama Pasien
                        </span>
                    </label>
                    <input type="text" value="{{ $user->nama ?? $user->name ?? '-' }}" readonly
                        class="input input-bordered border-2 rounded-lg p-2 w-full bg-slate-100 text-slate-700">
                    @error('id_pasien')
                    <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Grid 2 Column --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

                    {{-- Pilih Poli --}}
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold text-slate-700 text-sm">
                                Pilih Poli <span class="text-red-500">*</span>
                            </span>
                        </label>
                        <select name="id_poli" id="selectPoli" class="select select-bordered border-2 rounded-lg p-2 w-full
                                       focus:ring-2 focus:ring-primary
                                       @error('id_poli') select-error @enderror" required>
                            <option value="">— Pilih Poli —</option>
                            @foreach($polis as $poli)
                            <option value="{{ $poli->id }}" {{ old('id_poli')==$poli->id ? 'selected' : '' }}>
                                {{ $poli->nama_poli }}
                            </option>
                            @endforeach
                        </select>
                        @error('id_poli')
                        <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Pilih Jadwal --}}
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold text-slate-700 text-sm">
                                Pilih Jadwal <span class="text-red-500">*</span>
                            </span>
                        </label>
                        <select name="id_jadwal" id="selectJadwal" class="select select-bordered border-2 rounded-lg p-2 w-full
                                       focus:ring-2 focus:ring-primary
                                       @error('id_jadwal') select-error @enderror" required>
                            <option value="">— Pilih Jadwal —</option>
                            @foreach($jadwals as $jadwal)
                            <option value="{{ $jadwal->id }}" data-poli="{{ $jadwal->dokter?->id_poli }}"
                                {{ old('id_jadwal')==$jadwal->id ? 'selected' : '' }}>
                                {{ $jadwal->dokter?->nama ?? '-' }} |
                                {{ $jadwal->hari }},
                                {{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }}
                            </option>
                            @endforeach
                        </select>
                        @error('id_jadwal')
                        <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                        @enderror
                        <span id="infoJadwal" class="text-xs text-slate-400 mt-1">
                            Pilih poli terlebih dahulu untuk melihat jadwal dokter.
                        </span>
                    </div>

                </div>

                {{-- Keluhan --}}
                <div class="form-control mb-8">
                    <label class="label">
                        <span class="label-text font-semibold text-slate-700 text-sm">
                            Keluhan <span class="text-slate-400 font-normal">(Opsional)</span>
                        </span>
                    </label>
                    <textarea name="keluhan" rows="4" placeholder="Tuliskan keluhan Anda..." class="textarea textarea-bordered border-2 rounded-lg p-2 w-full
                                     focus:ring-2 focus:ring-primary
                                     @error('keluhan') textarea-error @enderror">{{ old('keluhan') }}</textarea>
                    @error('keluhan')
                    <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Buttons --}}
                <div class="flex items-center gap-3">
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5
                               bg-primary hover:bg-primary/90
                               text-white rounded-lg font-semibold text-sm transition">
                        <i class="fas fa-save text-sm"></i>
                        Daftar
                    </button>

                    <a href="{{ route('pasien.dashboard') }}" class="inline-flex items-center gap-2 px-6 py-2.5
                              bg-slate-100 hover:bg-slate-200
                              text-slate-600 rounded-xl font-semibold text-sm transition">
                        Batal
                    </a>
                </div>

            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        const selectPoli   = document.getElementById('selectPoli');
        const selectJadwal = document.getElementById('selectJadwal');
        const infoJadwal   = document.getElementById('infoJadwal');

        // Tampilkan hanya jadwal milik dokter di poli yang dipilih
        function filterJadwal(resetPilihan) {
            const poliId = selectPoli.value;
            let jumlah = 0;

            if (resetPilihan) selectJadwal.value = '';

            Array.from(selectJadwal.options).forEach(function (opt) {
                if (!opt.value) return; // lewati placeholder

                const cocok = poliId !== '' && opt.dataset.poli === poliId;
                opt.hidden   = !cocok;
                opt.disabled = !cocok;
                if (cocok) jumlah++;
            });

            selectJadwal.disabled = poliId === '';

            if (poliId === '') {
                infoJadwal.textContent = 'Pilih poli terlebih dahulu untuk melihat jadwal dokter.';
            } else if (jumlah === 0) {
                infoJadwal.textContent = 'Belum ada jadwal dokter untuk poli ini.';
            } else {
                infoJadwal.textContent = jumlah + ' jadwal tersedia.';
            }
        }

        selectPoli.addEventListener('change', function () { filterJadwal(true); });

        // Jalankan saat halaman dibuka (menjaga pilihan lama setelah validasi gagal)
        filterJadwal(false);
    </script>
    @endpush

</x-layouts.app>