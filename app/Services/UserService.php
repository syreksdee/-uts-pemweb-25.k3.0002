<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use InvalidArgumentException;

class UserService
{
    protected UserRepositoryInterface $userRepo;

    public function __construct(UserRepositoryInterface $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function getPaginated(int $perPage = 10, ?string $search = null, ?string $role = null): LengthAwarePaginator
    {
        return $this->userRepo->getPaginated($perPage, $search, $role);
    }

    public function getById(int $id): ?User
    {
        return $this->userRepo->findById($id);
    }

    public function getAllRoles(): Collection
    {
        return $this->userRepo->getAllRoles();
    }

    public function create(array $data, $fotoFile = null): User
    {
        if (empty($data['nama_lengkap'])) {
            throw new InvalidArgumentException('Nama lengkap wajib diisi.');
        }

        $data['password'] = Hash::make($data['password'] ?? 'password');
        $data['api_token'] = 'tok_' . Str::random(40);

        if ($fotoFile && $fotoFile->isValid()) {
            $filename = 'user_' . time() . '_' . Str::random(6) . '.' . $fotoFile->getClientOriginalExtension();
            $fotoFile->move(public_path('uploads/profil'), $filename);
            $data['foto'] = 'uploads/profil/' . $filename;
        } else {
            $data['foto'] = 'uploads/profil/default.png';
        }

        return $this->userRepo->create($data);
    }

    public function update(int $id, array $data, $fotoFile = null): bool
    {
        $user = $this->userRepo->findById($id);
        if (!$user) {
            throw new InvalidArgumentException("User dengan ID {$id} tidak ditemukan.");
        }

        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        if ($fotoFile && $fotoFile->isValid()) {
            if ($user->foto && file_exists(public_path($user->foto)) && !Str::contains($user->foto, ['default.png', '0002.jpg'])) {
                @unlink(public_path($user->foto));
            }

            $filename = 'user_' . time() . '_' . Str::random(6) . '.' . $fotoFile->getClientOriginalExtension();
            $fotoFile->move(public_path('uploads/profil'), $filename);
            $data['foto'] = 'uploads/profil/' . $filename;
        }

        return $this->userRepo->update($id, $data);
    }

    public function delete(int $id): bool
    {
        // Don't allow deleting master admin
        $user = $this->userRepo->findById($id);
        if ($user && $user->nim_nik === '25.K3.0002') {
            throw new InvalidArgumentException('Akun Administrator utama tidak boleh dihapus.');
        }
        return $this->userRepo->delete($id);
    }
}
