<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use InvalidArgumentException;

class ProfileService
{
    protected UserRepositoryInterface $userRepo;

    public function __construct(UserRepositoryInterface $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    /**
     * Update user profile information & profile photo upload
     * Memvalidasi tipe file (jpg/png) dan ukuran maksimal 2MB
     * Disimpan di folder uploads/profil, path disimpan di database
     */
    public function updateProfile(User $user, array $data, $fotoFile = null): bool
    {
        if (empty($data['nama_lengkap'])) {
            throw new InvalidArgumentException('Nama lengkap wajib diisi.');
        }

        $updateData = [
            'nama_lengkap' => $data['nama_lengkap'],
            'email' => $data['email'] ?? $user->email,
            'no_hp' => $data['no_hp'] ?? $user->no_hp,
            'alamat' => $data['alamat'] ?? $user->alamat,
        ];

        // Update password if provided
        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        // Upload foto profil
        if ($fotoFile) {
            // Validasi extension & mime
            $extension = strtolower($fotoFile->getClientOriginalExtension());
            if (!in_array($extension, ['jpg', 'jpeg', 'png'])) {
                throw new InvalidArgumentException('Foto profil harus berupa file gambar dengan format JPG atau PNG.');
            }

            // Validasi ukuran maksimal 2MB (2048 KB = 2097152 bytes)
            if ($fotoFile->getSize() > 2097152) {
                throw new InvalidArgumentException('Ukuran foto profil tidak boleh melebihi 2 MB.');
            }

            // Hapus foto lama jika bukan foto default atau foto bawaan admin 0002
            if ($user->foto && file_exists(public_path($user->foto)) && !Str::contains($user->foto, ['default.png', '0002.jpg'])) {
                @unlink(public_path($user->foto));
            }

            $filename = 'profil_' . $user->id . '_' . time() . '.' . $extension;
            $destination = public_path('uploads/profil');

            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }

            $fotoFile->move($destination, $filename);

            // Path disimpan di database (bukan BLOB)
            $updateData['foto'] = 'uploads/profil/' . $filename;
        }

        return $this->userRepo->update($user->id, $updateData);
    }
}
