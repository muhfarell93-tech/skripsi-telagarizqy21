<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Outlet extends Model
{
    use HasFactory;

    protected $table = 'outlets';

    protected $fillable = [
        'kode_outlet',
        'nama_outlet',
        'no_telepon',
        'alamat',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'outlet_id');
    }

    public function produks()
    {
        return $this->hasMany(Produk::class, 'outlet_id');
    }

    public function transaksis()
    {
        return $this->hasMany(Transaksi::class, 'outlet_id');
    }

    public function pengeluarans()
    {
        return $this->hasMany(Pengeluaran::class, 'outlet_id');
    }
}

