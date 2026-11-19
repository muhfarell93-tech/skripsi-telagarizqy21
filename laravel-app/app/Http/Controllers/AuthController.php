<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route($this->destinationFor(Auth::user()->role));
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        $identityField = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $identity = $identityField === 'email' ? mb_strtolower($credentials['login']) : $credentials['login'];

        if (Auth::attempt([
            $identityField => $identity,
            'password' => $credentials['password'],
            'is_active' => true,
            'role' => ['admin', 'kasir'],
        ], $request->boolean('remember'))) {
            $request->session()->regenerate();
            LogAktivitas::catat(Auth::id(), 'AUTENTIKASI', 'login', 'Pengguna berhasil login ke sistem');

            return redirect()->route($this->destinationFor(Auth::user()->role));
        }

        return back()->withErrors([
            'login' => 'Email/username atau kata sandi salah, atau akun tidak aktif.',
        ])->onlyInput('login');
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            LogAktivitas::catat(Auth::id(), 'AUTENTIKASI', 'logout', 'Pengguna keluar dari sistem');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function switchRole(Request $request, string $targetRole)
    {
        $targetUser = match ($targetRole) {
            'admin' => \App\Models\User::where('role', 'admin')->where('is_active', true)->where('id', 7)->first()
                       ?? \App\Models\User::where('role', 'admin')->where('is_active', true)->first(),
            'kasir' => \App\Models\User::where('role', 'kasir')->where('is_active', true)->where('id', 9)->first()
                       ?? \App\Models\User::where('role', 'kasir')->where('is_active', true)->first(),
            default => null,
        };

        if (!$targetUser) {
            return back()->with('error', 'Akun target tidak ditemukan.');
        }

        Auth::login($targetUser);
        $request->session()->regenerate();
        LogAktivitas::catat(Auth::id(), 'AUTENTIKASI', 'login', "Beralih peran cepat ke {$targetUser->name} ({$targetUser->role})");

        $redirectRoute = ($targetUser->role === 'admin') ? 'dashboard' : 'pos.index';
        return redirect()->route($redirectRoute)->with('success', "Sekarang Anda masuk sebagai {$targetUser->name} (" . strtoupper($targetUser->role) . ")");
    }

    private function destinationFor(string $role): string
    {
        return match ($role) {
            'admin' => 'dashboard',
            default => 'pos.index',
        };
    }
}
