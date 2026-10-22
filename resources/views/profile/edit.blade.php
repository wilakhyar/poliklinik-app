<x-layouts.app title="Profile">

    <h2 class="text-2xl font-bold text-slate-800 mb-6">Profile</h2>

    <div class="card bg-base-100 shadow-md rounded-2xl border max-w-xl">
        <div class="card-body p-8">

            {{-- Info akun --}}
            <div class="mb-6 pb-6 border-b border-slate-100">
                <p class="font-semibold text-slate-800">{{ auth()->user()->nama }}</p>
                <p class="text-sm text-slate-500">{{ auth()->user()->email }}</p>
            </div>

            <h3 class="text-lg font-bold text-slate-800 mb-4">Ganti Password</h3>

            <form action="{{ route(auth()->user()->role . '.profile.password') }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Password lama --}}
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Password Lama</label>
                    <input type="password" name="current_password"
                        class="w-full px-4 py-2 border-2 rounded-lg focus:border-primary focus:outline-none
                        @error('current_password') border-red-500 @enderror">
                    @error('current_password')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password baru --}}
                <div class="mb-4">

                    <label class="block text-sm font-semibold text-slate-700 mb-1">Password Baru</label>
                    <input type="password" name="password"
                        class="w-full px-4 py-2 border-2 rounded-lg focus:border-primary focus:outline-none
                        @error('password') border-red-500 @enderror">
                    @error('password')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-slate-400 mt-1">Minimal 8 karakter.</p>
                </div>

                {{-- Konfirmasi --}}
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation"
                        class="w-full px-4 py-2 border-2 rounded-lg focus:border-primary focus:outline-none">
                </div>

                <button type="submit" class="px-6 py-2.5 rounded-xl bg-primary hover:bg-primary/90
                    text-white font-semibold text-sm transition">
                    <i class="fas fa-key mr-1"></i>
                    Simpan Password
                </button>
            </form>

        </div>
    </div>

</x-layouts.app>