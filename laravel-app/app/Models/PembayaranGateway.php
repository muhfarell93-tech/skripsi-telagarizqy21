<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranGateway extends Model
{
    use HasFactory;

    protected $table = 'pembayaran_gateways';

    protected $fillable = [
        'transaksi_id',
        'order_id',
        'transaction_id',
        'payment_type',
        'gross_amount',
        'currency',
        'transaction_status',
        'fraud_status',
        'status_code',
        'signature_key',
        'raw_notification',
        'waktu_notifikasi',
    ];

    protected $casts = [
        'gross_amount'     => 'decimal:2',
        'waktu_notifikasi' => 'datetime',
    ];

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id');
    }
}

