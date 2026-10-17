<x-layouts.app title="Periksa Pasien">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('periksa-pasien.index') }}" class="flex items-center justify-center w-9 h-9 rounded-lg 
                  bg-slate-100 hover:bg-slate-200 
                  text-slate-600 transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>

        <h2 class="text-2xl font-bold text-slate-800">
            Periksa Pasien
        </h2>
    </div>

    {{-- Card Form --}}
    <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200">
        <div class="card-body p-8">

            <form action="{{ route('periksa-pasien.store') }}" method="POST" id="formPeriksa">
                @csrf

                {{-- Data tersembunyi --}}
                <input type="hidden" name="id_daftar_poli" value="{{ $id }}">
                <input type="hidden" name="obat_json" id="obatJson" value="[]">
                <input type="hidden" name="biaya_periksa" id="biayaPeriksa" value="0">

                {{-- Pilih Obat --}}
                <div class="form-control mb-6">
                    <label class="label">
                        <span class="label-text font-semibold text-slate-700 text-sm">
                            Pilih Obat <span class="text-red-500">*</span>
                        </span>
                    </label>
                    <select id="selectObat" class="select select-bordered border-2 rounded-lg p-2 w-full
                                   focus:ring-2 focus:ring-primary">
                        <option value="">— Pilih Obat —</option>
                        @foreach($obats as $obat)
                        <option value="{{ $obat->id }}" data-nama="{{ $obat->nama_obat }}"
                            data-harga="{{ $obat->harga }}">
                            {{ $obat->nama_obat }} - {{ $obat->kemasan }} (Rp {{ number_format($obat->harga, 0, ',', '.') }})
                        </option>
                        @endforeach
                    </select>
                    @error('obat_json')
                    <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Obat Terpilih --}}
                <div class="mb-6">
                    <label class="label">
                        <span class="label-text font-semibold text-slate-700 text-sm">
                            Obat Terpilih
                        </span>
                    </label>
                    <ul id="listObat" class="space-y-2">
                        <li id="obatKosong" class="text-sm text-slate-400">Belum ada obat dipilih</li>
                    </ul>
                </div>

                {{-- Total Harga --}}
                <div class="form-control mb-6">
                    <label class="label">
                        <span class="label-text font-semibold text-slate-700 text-sm">
                            Total Harga
                        </span>
                    </label>
                    <input type="text" id="totalHarga" value="Rp 0" readonly
                        class="input input-bordered border-2 rounded-lg p-2 w-full bg-slate-100 text-slate-700">
                </div>

                {{-- Catatan --}}
                <div class="form-control mb-8">
                    <label class="label">
                        <span class="label-text font-semibold text-slate-700 text-sm">
                            Catatan <span class="text-slate-400 font-normal">(Opsional)</span>
                        </span>
                    </label>
                    <textarea name="catatan" rows="4" placeholder="Masukkan catatan..." class="textarea textarea-bordered border-2 rounded-lg p-2 w-full
                                     focus:ring-2 focus:ring-primary
                                     @error('catatan') textarea-error @enderror">{{ old('catatan') }}</textarea>
                    @error('catatan')
                    <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Buttons --}}
                <div class="flex items-center gap-3">
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5
                               bg-primary hover:bg-primary/90
                               text-white rounded-lg font-semibold text-sm transition">
                        <i class="fas fa-save text-sm"></i>
                        Simpan
                    </button>

                    <a href="{{ route('periksa-pasien.index') }}" class="inline-flex items-center gap-2 px-6 py-2.5
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
        const selectObat   = document.getElementById('selectObat');
        const listObat     = document.getElementById('listObat');
        const obatKosong   = document.getElementById('obatKosong');
        const obatJson     = document.getElementById('obatJson');
        const biayaPeriksa = document.getElementById('biayaPeriksa');
        const totalHarga   = document.getElementById('totalHarga');
        const form         = document.getElementById('formPeriksa');

        // Daftar obat terpilih: [{id, nama, harga}]
        let terpilih = [];

        function formatRupiah(angka) {
            return 'Rp ' + angka.toLocaleString('id-ID');
        }

        function render() {
            listObat.innerHTML = '';

            if (terpilih.length === 0) {
                listObat.appendChild(obatKosong);
            }

            terpilih.forEach(function (obat, index) {
                const li = document.createElement('li');
                li.className = 'flex items-center justify-between px-4 py-2 rounded-lg bg-slate-50 border border-slate-200 text-sm';

                const info = document.createElement('span');
                info.className = 'text-slate-700';
                info.textContent = obat.nama + ' - ' + formatRupiah(obat.harga);

                const hapus = document.createElement('button');
                hapus.type = 'button';
                hapus.className = 'text-red-500 hover:text-red-700 text-xs font-semibold';
                hapus.innerHTML = '<i class="fas fa-trash"></i> Hapus';
                hapus.addEventListener('click', function () {
                    terpilih.splice(index, 1);
                    render();
                });

                li.appendChild(info);
                li.appendChild(hapus);
                listObat.appendChild(li);
            });

            const total = terpilih.reduce(function (sum, o) { return sum + o.harga; }, 0);

            totalHarga.value   = formatRupiah(total);
            biayaPeriksa.value = total;
            obatJson.value     = JSON.stringify(terpilih.map(function (o) { return o.id; }));
        }

        selectObat.addEventListener('change', function () {
            const opt = selectObat.options[selectObat.selectedIndex];
            if (!opt.value) return;

            const id = parseInt(opt.value);

            // Cegah obat yang sama ditambahkan dua kali
            if (!terpilih.some(function (o) { return o.id === id; })) {
                terpilih.push({
                    id: id,
                    nama: opt.dataset.nama,
                    harga: parseInt(opt.dataset.harga)
                });
                render();
            }

            selectObat.value = '';
        });

        form.addEventListener('submit', function (e) {
            if (terpilih.length === 0) {
                e.preventDefault();
                alert('Pilih minimal satu obat terlebih dahulu.');
            }
        });
    </script>
    @endpush

</x-layouts.app>