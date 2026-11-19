<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksis';

    protected $fillable = [
        'kode_invoice',
        'outlet_id',
        'user_id',
        'tanggal_transaksi',
        'total_item',
        'total_harga',
        'total_bayar',
        'kembalian',
        'metode_pembayaran', // 'tunai', 'transfer', 'qris'
        'status_pembayaran', // 'lunas', 'belum_lunas', 'batal'
        'catatan',
        'snap_token',
        'gateway_order_id',
        'gateway_trans_id',
        'gateway_payment_type',
        'waktu_kadaluarsa',
        'waktu_lunas',
        'url_bukti_bayar',
        'stok_dikurangi',
        'is_demo',
    ];

    protected $casts = [
        'tanggal_transaksi' => 'datetime',
        'waktu_kadaluarsa'  => 'datetime',
        'waktu_lunas'       => 'datetime',
        'total_item'        => 'integer',
        'total_harga'       => 'decimal:2',
        'total_bayar'       => 'decimal:2',
        'kembalian'         => 'decimal:2',
        'stok_dikurangi'    => 'boolean',
        'is_demo'           => 'boolean',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function details()
    {
        return $this->hasMany(DetailTransaksi::class, 'transaksi_id');
    }

    public function gatewayLogs()
    {
        return $this->hasMany(PembayaranGateway::class, 'transaksi_id');
    }

    public function isLunas(): bool
    {
        return $this->status_pembayaran === 'lunas';
    }
}

