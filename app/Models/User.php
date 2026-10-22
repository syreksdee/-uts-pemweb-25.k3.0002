<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 't002_users';

    protected $fillable = [
        'role_id',
        'nim_nik',
        'nama_lengkap',
        'email',
        'password',
        'no_hp',
        'alamat',
        'foto',
        'api_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'api_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function transaksiPelanggan(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'user_pelanggan_id');
    }

    public function transaksiKasir(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'user_kasir_id');
    }

    public function isAdmin(): bool
    {
        return $this->role && $this->role->kode_role === 'admin';
    }

    public function isKasir(): bool
    {
        return $this->role && $this->role->kode_role === 'kasir';
    }

    public function isGudang(): bool
    {
        return $this->role && $this->role->kode_role === 'gudang';
    }

    public function isPelanggan(): bool
    {
        return $this->role && $this->role->kode_role === 'pelanggan';
    }

    public function getFotoUrlAttribute(): string
    {
        if ($this->foto && file_exists(public_path($this->foto))) {
            return asset($this->foto);
        }
        return asset('uploads/profil/default.png');
    }
}
