<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    use HasFactory;

    protected $table = 'pengeluarans';

    protected $fillable = [
        'kode_pengeluaran',
        'outlet_id',
        'user_id',
        'kategori_pengeluaran',
        'keterangan',
        'nominal',
        'tanggal_pengeluaran',
        'is_demo',
    ];

    protected $casts = [
        'nominal'             => 'decimal:2',
        'tanggal_pengeluaran' => 'datetime',
        'is_demo' => 'boolean',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

