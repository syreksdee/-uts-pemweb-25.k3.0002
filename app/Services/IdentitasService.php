<?php

namespace App\Services;

use App\Repositories\Contracts\UserRepositoryInterface;

class IdentitasService
{
    protected UserRepositoryInterface $userRepo;

    public function __construct(UserRepositoryInterface $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function getIdentitas(): array
    {
        $admin = $this->userRepo->findByNimOrEmail('25.K3.0002');

        $nim = $admin ? $admin->nim_nik : '25.K3.0002';
        $nama = $admin ? $admin->nama_lengkap : 'Aloysius Jaison Gondopurwanto';
        $fotoPath = ($admin && $admin->foto) ? $admin->foto : 'uploads/profil/0002.jpg';

        return [
            'nim' => $nim,
            'nama' => $nama,
            'foto' => url($fotoPath),
            'aplikasi' => 'SiToko — Toko Online Sederhana 002',
        ];
    }
}
