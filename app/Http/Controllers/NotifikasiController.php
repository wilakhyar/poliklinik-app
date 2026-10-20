<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        $notifikasis = $user->notifications()->latest()->paginate(15);

        return view('notifikasi.index', compact('notifikasis'));
    }

    public function baca(string $id)
    {
        /** @var User $user */
        $user = Auth::user();

        $notif = $user->notifications()->findOrFail($id);
        $notif->markAsRead();

        return back();
    }

    public function bacaSemua()
    {
        /** @var User $user */
        $user = Auth::user();

        $user->unreadNotifications->markAsRead();

        return back();
    }
}