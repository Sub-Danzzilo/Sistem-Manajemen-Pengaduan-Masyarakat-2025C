<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use App\Models\SystemLog;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request, string $account, string $role): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request, string $account, string $role): RedirectResponse
    {
        DB::beginTransaction();
        try {
            $request->user()->fill($request->validated());

            if ($request->user()->isDirty('email')) {
                $request->user()->email_verified_at = null;
            }

            $request->user()->save();
            DB::commit();

            return Redirect::route('profile.edit')->with('status', 'profile-updated');
        } catch (\Throwable $e) {
            DB::rollBack();
            SystemLog::log(
                message: 'Gagal update profil: ' . $e->getMessage(),
                category: 'ProfileController@update',
                exception: $e
            );
            return back()->withErrors(['error' => 'Gagal memperbarui profil. Silakan coba lagi.']);
        }
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request, string $account, string $role): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        DB::beginTransaction();
        try {
            Auth::logout();

            $user->delete();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            DB::commit();
            return Redirect::to('/');
        } catch (\Throwable $e) {
            DB::rollBack();
            SystemLog::log(
                message: 'Gagal hapus akun: ' . $e->getMessage(),
                category: 'ProfileController@destroy',
                exception: $e
            );
            return back()->withErrors(['error' => 'Terjadi kesalahan saat menghapus akun.']);
        }
    }
}
