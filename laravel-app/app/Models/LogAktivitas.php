<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogAktivitas extends Model
{
    use HasFactory;

    protected $table = 'log_aktivitas';

    public $timestamps = false; // Menggunakan kolom waktu kustom

    protected $fillable = [
        'user_id',
        'modul',
        'aksi',
        'deskripsi',
        'waktu',
    ];

    protected $casts = [
        'waktu' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function catat($userId, string $modul, string $aksi, string $deskripsi): self
    {
        return self::create([
            'user_id'   => $userId,
            'modul'     => $modul,
            'aksi'      => $aksi,
            'deskripsi' => $deskripsi,
            'waktu'     => now(),
        ]);
    }
}

