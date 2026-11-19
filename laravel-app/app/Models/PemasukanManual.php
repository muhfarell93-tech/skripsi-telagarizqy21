<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PemasukanManual extends Model
{
    protected $table = 'pemasukan_manual';

    protected $fillable = [
        'kode_pemasukan', 'outlet_id', 'user_id', 'kategori_pemasukan',
        'keterangan', 'nominal', 'tanggal_pemasukan', 'is_demo',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'tanggal_pemasukan' => 'datetime',
        'is_demo' => 'boolean',
    ];

    public function outlet() { return $this->belongsTo(Outlet::class); }
    public function user() { return $this->belongsTo(User::class); }
}
