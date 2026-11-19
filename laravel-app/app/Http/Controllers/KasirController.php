<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class KasirController extends Controller
{
    public function index()
    {
        $kasirs = User::where('outlet_id', Auth::user()->outlet_id)->where('role', 'kasir')
            ->withCount('transaksis')->orderBy('name')->paginate(20);
        return view('kasir.index', compact('kasirs'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'alpha_dash', 'min:3', 'max:50', 'unique:users,username'],
            'email' => ['required', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'string', 'min:4', 'confirmed'],
        ]);
        $kasir = User::create([
            'outlet_id' => Auth::user()->outlet_id,
            'name' => $data['name'], 'username' => $data['username'],
            'email' => mb_strtolower($data['email']), 'password' => Hash::make($data['password']),
            'role' => 'kasir', 'is_active' => true,
        ]);
        LogAktivitas::catat(Auth::id(), 'PENGGUNA', 'tambah', "Membuat akun kasir {$kasir->username}");
        return redirect()->route('kasir.index')->with('success', 'Akun kasir berhasil dibuat.');
    }

    public function updatePassword(Request $request, User $user)
    {
        abort_unless($user->outlet_id === Auth::user()->outlet_id && $user->role === 'kasir', 404);
        $data = $request->validate(['password' => ['required', 'string', 'min:4', 'confirmed']]);
        $user->update(['password' => Hash::make($data['password'])]);
        LogAktivitas::catat(Auth::id(), 'PENGGUNA', 'ubah', "Mengatur ulang kata sandi kasir {$user->username}");
        return redirect()->route('kasir.index')->with('success', 'Kata sandi kasir berhasil diperbarui.');
    }

    public function toggleStatus(User $user)
    {
        abort_unless($user->outlet_id === Auth::user()->outlet_id && $user->role === 'kasir', 404);
        $user->update(['is_active' => !$user->is_active]);
        $state = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        LogAktivitas::catat(Auth::id(), 'PENGGUNA', 'ubah', "Akun kasir {$user->username} {$state}");
        return redirect()->route('kasir.index')->with('success', "Akun kasir {$state}.");
    }
}
