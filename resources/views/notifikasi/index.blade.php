<x-layouts.app title="Notifikasi">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-slate-800">
            Notifikasi
        </h2>

        @if(auth()->user()->unreadNotifications()->count() > 0)
        <form method="POST" action="{{ route('notifikasi.baca-semua') }}">
            @csrf
            @method('PATCH')
            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5
                       bg-primary hover:bg-primary/90
                       text-white text-sm font-semibold
                       rounded-xl transition">
                <i class="fas fa-check-double text-xs"></i>
                Tandai semua dibaca
            </button>
        </form>
        @endif
    </div>

    {{-- Daftar --}}
    <div class="card bg-base-100 shadow-md rounded-2xl border overflow-hidden">
        @forelse($notifikasis as $notif)
        <div class="px-6 py-4 border-b border-slate-100 flex items-start justify-between gap-4
                    {{ $notif->read_at ? 'bg-white' : 'bg-indigo-50' }}">
            <div class="flex items-start gap-3">
                <div class="mt-1.5 w-2.5 h-2.5 rounded-full {{ $notif->read_at ? 'bg-slate-300' : 'bg-indigo-500' }}"></div>
                <div>
                    <p class="font-semibold text-slate-800">{{ $notif->data['judul'] ?? 'Notifikasi' }}</p>
                    <p class="text-sm text-slate-600 mt-0.5">{{ $notif->data['pesan'] ?? '' }}</p>
                    <p class="text-xs text-slate-400 mt-1">{{ $notif->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            @if(!$notif->read_at)
            <form method="POST" action="{{ route('notifikasi.baca', $notif->id) }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="text-xs font-semibold text-indigo-600 hover:underline whitespace-nowrap">
                    Tandai dibaca
                </button>
            </form>
            @endif
        </div>
        @empty
        <div class="text-center py-12 text-slate-400">
            <i class="fas fa-bell-slash text-3xl mb-3 block"></i>
            Belum ada notifikasi
        </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $notifikasis->links() }}
    </div>

</x-layouts.app>