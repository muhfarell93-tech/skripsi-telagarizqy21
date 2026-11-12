<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produks';

    protected $fillable = [
        'outlet_id',
        'kategori_id',
        'kode_produk',
        'nama_produk',
        'harga_modal',
        'harga_jual',
        'stok',
        'stok_minimum',
        'satuan',
        'foto',
    ];

    protected $casts = [
        'harga_modal' => 'decimal:2',
        'harga_jual'  => 'decimal:2',
        'stok'        => 'integer',
        'stok_minimum'=> 'integer',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id');
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriProduk::class, 'kategori_id');
    }

    public function detailTransaksis()
    {
        return $this->hasMany(DetailTransaksi::class, 'produk_id');
    }

    public function isStokMenipis(): bool
    {
        return $this->stok <= $this->stok_minimum;
    }
}

