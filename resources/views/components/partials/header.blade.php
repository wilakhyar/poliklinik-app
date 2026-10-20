<header class="bg-base-100 border-b border-base-300 h-16 flex items-center px-6 gap-4 sticky top-0 z-30 shadow-sm">

    {{-- Mobile Hamburger --}}
    <button onclick="toggleSidebar()" class="btn btn-square btn-ghost lg:hidden">
        <i data-lucide="menu" class="w-5 h-5"></i>
    </button>

    {{-- Breadcrumb --}}
    <div class="flex-1">
        <div class="flex items-center gap-2 text-sm">
            <span class="text-base-content/50">Poliklinik</span>
            <i data-lucide="chevron-right" class="w-4 h-4 text-base-content/30"></i>
            <span class="font-semibold text-base-content">
                {{ $title ?? 'Dashboard' }}
            </span>
        </div>
    </div>

    {{-- Notifikasi --}}
    @php
        $unreadCount = auth()->user()->unreadNotifications()->count();
        $latestNotifs = auth()->user()->notifications()->latest()->take(5)->get();
    @endphp
    <div class="dropdown dropdown-end">
        <div tabindex="0" role="button" class="btn btn-ghost btn-square relative">
            <i class="fas fa-bell"></i>
            @if($unreadCount > 0)
            <span class="badge badge-error badge-sm absolute -top-1 -right-1 text-white">
                {{ $unreadCount }}
            </span>
            @endif
        </div>

        <div tabindex="0"
            class="dropdown-content z-50 mt-3 w-80 rounded-xl bg-base-100 shadow-lg border border-base-300 overflow-hidden">

            <div class="px-4 py-3 border-b border-base-300 font-semibold text-sm">
                Notifikasi
            </div>

            <div class="max-h-80 overflow-y-auto">
                @forelse($latestNotifs as $notif)
                <div class="px-4 py-3 border-b border-base-200 {{ $notif->read_at ? 'bg-base-100' : 'bg-indigo-50' }}">
                    <p class="text-sm font-semibold text-slate-800">{{ $notif->data['judul'] ?? 'Notifikasi' }}</p>
                    <p class="text-xs text-slate-600 mt-0.5">{{ $notif->data['pesan'] ?? '' }}</p>
                    <div class="flex items-center justify-between mt-2">
                        <span class="text-[11px] text-slate-400">{{ $notif->created_at->format('d/m/Y H:i') }}</span>
                        @if(!$notif->read_at)
                        <form method="POST" action="{{ route('notifikasi.baca', $notif->id) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-[11px] font-semibold text-indigo-600 hover:underline">
                                Tandai dibaca
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                @empty
                <div class="px-4 py-8 text-center text-sm text-slate-400">
                    Belum ada notifikasi
                </div>
                @endforelse
            </div>

            <a href="{{ route('notifikasi.index') }}"
                class="block text-center text-sm font-semibold text-indigo-600 py-3 hover:bg-slate-50">
                Lihat semua
            </a>
        </div>
    </div>

    {{-- Fullscreen --}}
    <button onclick="toggleFullscreen()" class="btn btn-square btn-ghost">
        <i id="fsIcon" class="fas fa-expand w-5 h-5"></i>
    </button>

    {{-- User Info --}}
    <div class="flex items-center gap-3">

        <div class="text-right hidden sm:block">
            <div class="text-sm font-semibold leading-tight">
                {{ auth()->user()->nama ?? 'Pengguna' }}
            </div>
            <div class="text-xs text-base-content/50 leading-tight">
                {{ auth()->user()->role ?? 'Admin Sistem' }}
            </div>
        </div>

        <div class="avatar">
            <div class="w-10 h-10 rounded-full bg-primary text-primary-content flex items-center justify-center">
                <span class="text-sm font-semibold leading-none">
                    {{ strtoupper(substr(auth()->user()->nama ?? 'U', 0, 1)) }}
                </span>
            </div>
        </div>
    </div>

</header>

<script>
    function toggleFullscreen() {
    const icon = document.getElementById('fsIcon');

    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen();
        icon.classList.remove('fa-expand');
        icon.classList.add('fa-compress');
    } else {
        document.exitFullscreen();
        icon.classList.remove('fa-compress');
        icon.classList.add('fa-expand');
    }
}
</script>