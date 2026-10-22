<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 't002_transaksi';

    protected $fillable = [
        'nomor_transaksi',
        'user_pelanggan_id',
        'user_kasir_id',
        'nama_pelanggan',
        'tanggal_transaksi',
        'total_harga',
        'total_bayar',
        'kembalian',
        'metode_pembayaran',
        'nomor_va',
        'qris_payload',
        'status_pembayaran',
        'catatan',
    ];

    protected $casts = [
        'tanggal_transaksi' => 'datetime',
        'total_harga' => 'float',
        'total_bayar' => 'float',
        'kembalian' => 'float',
    ];

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_pelanggan_id');
    }

    public function kasir(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_kasir_id');
    }

    public function detail(): HasMany
    {
        return $this->hasMany(DetailTransaksi::class, 'transaksi_id');
    }

    public function scopeCari($query, $keyword)
    {
        if ($keyword) {
            return $query->where(function ($q) use ($keyword) {
                $q->where('nomor_transaksi', 'like', "%{$keyword}%")
                  ->orWhere('nama_pelanggan', 'like', "%{$keyword}%")
                  ->orWhere('metode_pembayaran', 'like', "%{$keyword}%")
                  ->orWhere('status_pembayaran', 'like', "%{$keyword}%");
            });
        }
        return $query;
    }
}
