<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Barang extends Model
{
    use HasFactory;

    protected $table = 't002_barang';

    protected $fillable = [
        'kategori_id',
        'kode_barang',
        'nama_barang',
        'slug',
        'deskripsi',
        'harga_beli',
        'harga_jual',
        'stok',
        'satuan',
        'gambar',
        'status',
    ];

    protected $casts = [
        'harga_beli' => 'float',
        'harga_jual' => 'float',
        'stok' => 'integer',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function detailTransaksi(): HasMany
    {
        return $this->hasMany(DetailTransaksi::class, 'barang_id');
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeCari($query, $keyword)
    {
        if ($keyword) {
            return $query->where(function ($q) use ($keyword) {
                $q->where('nama_barang', 'like', "%{$keyword}%")
                  ->orWhere('kode_barang', 'like', "%{$keyword}%")
                  ->orWhereHas('kategori', function ($sub) use ($keyword) {
                      $sub->where('nama_kategori', 'like', "%{$keyword}%");
                  });
            });
        }
        return $query;
    }

    public function getGambarUrlAttribute(): string
    {
        if ($this->gambar && file_exists(public_path($this->gambar))) {
            return asset($this->gambar);
        }
        return asset('uploads/barang/kopi.jpg');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($barang) {
            if (empty($barang->slug)) {
                $barang->slug = Str::slug($barang->nama_barang) . '-' . Str::random(4);
            }
        });
    }
}
