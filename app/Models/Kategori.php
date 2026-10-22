<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 't002_kategori';

    protected $fillable = [
        'nama_kategori',
        'slug',
        'deskripsi',
        'icon',
    ];

    public function barang(): HasMany
    {
        return $this->hasMany(Barang::class, 'kategori_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($kategori) {
            if (empty($kategori->slug)) {
                $kategori->slug = Str::slug($kategori->nama_kategori) . '-' . Str::random(4);
            }
        });
    }
}
