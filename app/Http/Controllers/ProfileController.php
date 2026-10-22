<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePasswordRequest;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.edit');
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $user = $request->user(); // selalu user yang sedang login

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()
            ->route($user->role . '.profile')
            ->with('success', 'Password berhasil diganti.');
    }
}