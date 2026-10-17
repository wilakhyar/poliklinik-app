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

    {{-- Info Pasien --}}
    <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200 mb-6">
        <div class="card-body p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <p class="text-slate-400 text-xs uppercase tracking-wider">Pasien</p>
                    <p class="font-semibold text-slate-800">{{ $daftar->pasien->nama }}</p>
                </div>
                <div>
                    <p class="text-slate-400 text-xs uppercase tracking-wider">No Antrian</p>
                    <p class="font-semibold text-slate-800">{{ $daftar->no_antrian }}</p>
                </div>
                <div>
                    <p class="text-slate-400 text-xs uppercase tracking-wider">Keluhan</p>
                    <p class="font-semibold text-slate-800">{{ $daftar->keluhan ?: '-' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Card Form --}}
    <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200">
        <div class="card-body p-8">

            <form action="{{ route('periksa-pasien.store') }}" method="POST" id="formPeriksa">
                @csrf

                {{-- Data tersembunyi --}}
                <input type="hidden" name="id_daftar_poli" value="{{ $daftar->id }}">
                <input type="hidden" name="obat_json" id="obatJson" value="[]">

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
                            data-harga="{{ $obat->harga }}" data-stok="{{ $obat->stok }}" @disabled($obat->stok < 1)>
                            {{ $obat->nama_obat }} - {{ $obat->kemasan ?? '-' }}
                            (Rp {{ number_format($obat->harga, 0, ',', '.') }}) - stok {{ $obat->stok }}
                        </option>
                        @endforeach
                    </select>
                    <span class="text-xs text-slate-400 mt-1">
                        Pilih obat yang sama lagi atau gunakan tombol + untuk menambah jumlah.
                    </span>
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
                            Total Harga Obat
                        </span>
                    </label>
                    <input type="text" id="totalHarga" value="Rp 0" readonly
                        class="input input-bordered border-2 rounded-lg p-2 w-full bg-slate-100 text-slate-700">
                    <span id="ringkasanBiaya" class="text-xs text-slate-500 mt-1"></span>
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
        const JASA_DOKTER = 150000;

        const selectObat    = document.getElementById('selectObat');
        const listObat      = document.getElementById('listObat');
        const obatKosong    = document.getElementById('obatKosong');
        const obatJson      = document.getElementById('obatJson');
        const totalHarga    = document.getElementById('totalHarga');
        const ringkasanBiaya = document.getElementById('ringkasanBiaya');
        const form          = document.getElementById('formPeriksa');

        // Daftar obat terpilih: [{id, nama, harga, stok, jumlah}]
        let terpilih = [];

        function formatRupiah(angka) {
            return 'Rp ' + angka.toLocaleString('id-ID');
        }

        // Ambil data obat dari atribut <option>
        function dataObat(id) {
            const opt = selectObat.querySelector('option[value="' + id + '"]');
            if (!opt) return null;

            return {
                id: id,
                nama: opt.dataset.nama,
                harga: parseInt(opt.dataset.harga),
                stok: parseInt(opt.dataset.stok)
            };
        }

        function buatTombol(teks, kelas, aksi) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = kelas;
            btn.innerHTML = teks;
            btn.addEventListener('click', aksi);
            return btn;
        }

        function ubahJumlah(index, delta) {
            const obat = terpilih[index];
            const baru = obat.jumlah + delta;

            if (baru < 1) return;

            if (baru > obat.stok) {
                alert('Stok ' + obat.nama + ' hanya ' + obat.stok + '.');
                return;
            }

            obat.jumlah = baru;
            render();
        }

        function render() {
            listObat.innerHTML = '';

            if (terpilih.length === 0) {
                listObat.appendChild(obatKosong);
            }

            terpilih.forEach(function (obat, index) {
                const li = document.createElement('li');
                li.className = 'flex flex-wrap items-center justify-between gap-3 px-4 py-2 rounded-lg bg-slate-50 border border-slate-200 text-sm';

                // Nama dan harga satuan
                const info = document.createElement('span');
                info.className = 'text-slate-700';
                info.textContent = obat.nama + ' - ' + formatRupiah(obat.harga);

                // Kontrol jumlah
                const kontrol = document.createElement('div');
                kontrol.className = 'flex items-center gap-2';

                const jumlah = document.createElement('span');
                jumlah.className = 'w-8 text-center font-semibold';
                jumlah.textContent = obat.jumlah;

                const subtotal = document.createElement('span');
                subtotal.className = 'w-28 text-right font-semibold text-slate-800';
                subtotal.textContent = formatRupiah(obat.harga * obat.jumlah);

                kontrol.appendChild(buatTombol('-', 'w-7 h-7 rounded-lg bg-slate-200 hover:bg-slate-300 font-bold',
                    function () { ubahJumlah(index, -1); }));
                kontrol.appendChild(jumlah);
                kontrol.appendChild(buatTombol('+', 'w-7 h-7 rounded-lg bg-slate-200 hover:bg-slate-300 font-bold',
                    function () { ubahJumlah(index, 1); }));
                kontrol.appendChild(subtotal);
                kontrol.appendChild(buatTombol('<i class="fas fa-trash"></i> Hapus',
                    'ml-2 text-red-500 hover:text-red-700 text-xs font-semibold',
                    function () {
                        terpilih.splice(index, 1);
                        render();
                    }));

                li.appendChild(info);
                li.appendChild(kontrol);
                listObat.appendChild(li);
            });

            const total = terpilih.reduce(function (sum, o) { return sum + o.harga * o.jumlah; }, 0);

            totalHarga.value = formatRupiah(total);
            ringkasanBiaya.textContent = 'Jasa dokter ' + formatRupiah(JASA_DOKTER)
                + ' + obat ' + formatRupiah(total)
                + ' = total biaya ' + formatRupiah(JASA_DOKTER + total);

            obatJson.value = JSON.stringify(terpilih.map(function (o) {
                return { id: o.id, jumlah: o.jumlah };
            }));
        }

        selectObat.addEventListener('change', function () {
            const id = parseInt(selectObat.value);
            if (!id) return;

            const sudahAda = terpilih.find(function (o) { return o.id === id; });

            if (sudahAda) {
                // Obat yang sama dipilih lagi: tambah jumlahnya
                ubahJumlah(terpilih.indexOf(sudahAda), 1);
            } else {
                const obat = dataObat(id);
                if (obat && obat.stok > 0) {
                    obat.jumlah = 1;
                    terpilih.push(obat);
                    render();
                }
            }

            selectObat.value = '';
        });

        form.addEventListener('submit', function (e) {
            if (terpilih.length === 0) {
                e.preventDefault();
                alert('Pilih minimal satu obat terlebih dahulu.');
            }
        });

        // Pulihkan pilihan obat jika form dikembalikan karena error validasi
        try {
            JSON.parse(@json(old('obat_json', '[]'))).forEach(function (row) {
                const obat = dataObat(parseInt(row.id));
                if (obat && obat.stok > 0 && row.jumlah > 0) {
                    obat.jumlah = Math.min(parseInt(row.jumlah), obat.stok);
                    terpilih.push(obat);
                }
            });
        } catch (e) {}

        render();
    </script>
    @endpush

</x-layouts.app>