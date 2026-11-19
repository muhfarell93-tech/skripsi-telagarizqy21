@extends('layouts.app')

@section('title', 'Kelola Akun Kasir')

@section('content')
<div class="space-y-6">

  <!-- Header & Title -->
  <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2">
        <span class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm font-bold shadow-sm">
          <i class="fa-solid fa-users-gear"></i>
        </span>
        <h1 class="text-xl font-black text-slate-900">Kelola Akun Kasir</h1>
      </div>
      <p class="text-xs text-slate-500 mt-1">Buat akun kasir baru, atur ulang kata sandi, dan kelola status akses tanpa menghapus riwayat transaksi.</p>
    </div>
    <div class="bg-indigo-50/70 border border-indigo-200/80 rounded-2xl px-5 py-3 text-right">
      <span class="text-[10px] font-extrabold text-indigo-700 uppercase tracking-wider block">Total Kasir Terdaftar</span>
      <span class="text-lg font-black text-indigo-700 block">{{ $kasirs->total() }}</span>
    </div>
  </div>

  @if(isset($errors) && $errors->any())
    <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-xs font-semibold text-red-700 flex items-center gap-2">
      <i class="fa-solid fa-circle-exclamation text-red-500"></i>
      <span>{{ $errors->first() }}</span>
    </div>
  @endif

  <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

    <!-- Kolom Kiri: Form Buat Akun Kasir -->
    <div class="lg:col-span-4 space-y-4">
      <section class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-sm space-y-4">
        <div class="border-b border-slate-100 pb-3 flex items-center gap-2">
          <i class="fa-solid fa-user-plus text-blue-600"></i>
          <h2 class="text-sm font-black text-slate-900">Buat Akun Kasir Baru</h2>
        </div>

        <form method="POST" action="{{ route('kasir.store') }}" class="space-y-4">
          @csrf
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap</label>
            <input name="name" value="{{ old('name') }}" required maxlength="100"
              class="input-interactive w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs font-semibold text-slate-800"
              placeholder="Contoh: Ahmad Fauzan">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Username</label>
            <input name="username" value="{{ old('username') }}" required minlength="3" maxlength="50"
              class="input-interactive w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs font-semibold text-slate-800"
              placeholder="Contoh: kasir_ahmad">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Email</label>
            <input name="email" type="email" value="{{ old('email') }}" required
              class="input-interactive w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs font-semibold text-slate-800"
              placeholder="kasir@telagarizqy21.com">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Kata Sandi (min. 4 karakter)</label>
            <input name="password" type="password" required minlength="4"
              class="input-interactive w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs font-semibold text-slate-800"
              placeholder="••••••••">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Ulangi Kata Sandi</label>
            <input name="password_confirmation" type="password" required minlength="4"
              class="input-interactive w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs font-semibold text-slate-800"
              placeholder="••••••••">
          </div>

          <button type="submit" class="btn-interactive btn-ripple w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs shadow-md shadow-blue-500/20 flex items-center justify-center gap-1.5 transition">
            <i class="fa-solid fa-user-plus"></i> Buat Akun Kasir
          </button>
        </form>
      </section>
    </div>

    <!-- Kolom Kanan: Tabel Daftar Kasir -->
    <div class="lg:col-span-8">
      <section class="overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-sm">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
          <div>
            <h2 class="text-sm font-black text-slate-900">Daftar Akun Kasir</h2>
            <p class="text-[11px] text-slate-400">Kelola status aktif dan kata sandi petugas kasir</p>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="table-interactive min-w-full divide-y divide-slate-100 text-xs text-slate-700">
            <thead class="bg-slate-50 uppercase text-[10px] text-slate-400 font-bold border-b border-slate-100">
              <tr>
                <th class="px-5 py-3 text-left">Petugas Kasir</th>
                <th class="px-5 py-3 text-center">Transaksi</th>
                <th class="px-5 py-3 text-center">Status</th>
                <th class="px-5 py-3 text-left">Atur Kata Sandi</th>
                <th class="px-5 py-3 text-center">Akses</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              @forelse($kasirs as $kasir)
                <tr class="hover:bg-slate-50/70 transition">
                  <td class="px-5 py-3.5">
                    <div class="font-bold text-slate-900">{{ $kasir->name }}</div>
                    <div class="text-[11px] text-slate-400">{{ $kasir->username }} · {{ $kasir->email }}</div>
                  </td>
                  <td class="px-5 py-3.5 text-center font-bold text-slate-800">
                    {{ $kasir->transaksis_count }}
                  </td>
                  <td class="px-5 py-3.5 text-center">
                    @if($kasir->is_active)
                      <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                      </span>
                    @else
                      <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-500 border border-slate-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Nonaktif
                      </span>
                    @endif
                  </td>
                  <td class="px-5 py-3.5">
                    <form method="POST" action="{{ route('kasir.password', $kasir) }}" class="flex items-center gap-1.5">
                      @csrf @method('PUT')
                      <input type="password" name="password" required minlength="4" placeholder="Sandi baru"
                        class="input-interactive w-24 rounded-lg border border-slate-200 bg-slate-50 px-2 py-1.5 text-[11px]">
                      <input type="password" name="password_confirmation" required minlength="4" placeholder="Ulangi"
                        class="input-interactive w-24 rounded-lg border border-slate-200 bg-slate-50 px-2 py-1.5 text-[11px]">
                      <button type="submit" class="btn-interactive px-2.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg text-[11px] transition shadow-sm">
                        Simpan
                      </button>
                    </form>
                  </td>
                  <td class="px-5 py-3.5 text-center">
                    <form method="POST" action="{{ route('kasir.status', $kasir) }}" onsubmit="return confirm('{{ $kasir->is_active ? 'Nonaktifkan' : 'Aktifkan' }} akun kasir ini?')">
                      @csrf @method('PATCH')
                      @if($kasir->is_active)
                        <button type="submit" class="btn-interactive px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 font-bold rounded-lg text-[11px] border border-red-200 transition">
                          <i class="fa-solid fa-ban text-[10px] mr-0.5"></i> Nonaktifkan
                        </button>
                      @else
                        <button type="submit" class="btn-interactive px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold rounded-lg text-[11px] border border-emerald-200 transition">
                          <i class="fa-solid fa-circle-check text-[10px] mr-0.5"></i> Aktifkan
                        </button>
                      @endif
                    </form>
                  </td>
                </tr>
              @empty
                <tr class="empty-state-row">
                  <td colspan="5" class="px-5 py-12 text-center text-slate-400">
                    <i class="fa-solid fa-users text-3xl mb-2 text-slate-300 block"></i>
                    Belum ada akun kasir terdaftar.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="px-6 py-4 border-t border-slate-100 bg-white">
          {{ $kasirs->links() }}
        </div>
      </section>
    </div>

  </div>
</div>
@endsection
