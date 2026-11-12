<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use App\Models\Outlet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegistrationController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'alpha_dash', 'min:3', 'max:50', 'unique:users,username'],
            'email' => ['required', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'string', 'min:4', 'confirmed'],
        ]);

        $outlet = Outlet::query()->first();
        if (!$outlet) {
            return back()->withErrors(['register' => 'Pendaftaran belum tersedia. Data outlet belum disiapkan.'])->withInput();
        }

        $user = User::create([
            'outlet_id' => $outlet->id,
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => mb_strtolower($data['email']),
            'password' => Hash::make($data['password']),
            'role' => 'kasir',
            'is_active' => true,
        ]);

        Auth::login($user);
        $request->session()->regenerate();
        LogAktivitas::catat($user->id, 'AUTENTIKASI', 'login', 'Akun kasir baru didaftarkan dan masuk');

        return redirect()->route('pos.index');
    }
}
