<?php

namespace App\Repositories\Eloquent;

use App\Models\Role;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class UserRepository implements UserRepositoryInterface
{
    public function getPaginated(int $perPage = 10, ?string $search = null, ?string $role = null): LengthAwarePaginator
    {
        $query = User::with('role');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nim_nik', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (!empty($role)) {
            $query->whereHas('role', function ($q) use ($role) {
                $q->where('kode_role', $role);
            });
        }

        return $query->orderBy('id', 'asc')->paginate($perPage);
    }

    public function findById(int $id): ?User
    {
        return User::with('role')->find($id);
    }

    public function findByNimOrEmail(string $identifier): ?User
    {
        return User::with('role')
            ->where('nim_nik', $identifier)
            ->orWhere('email', $identifier)
            ->first();
    }

    public function findByApiToken(string $token): ?User
    {
        return User::with('role')->where('api_token', $token)->first();
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $user = $this->findById($id);
        if ($user) {
            return $user->update($data);
        }
        return false;
    }

    public function delete(int $id): bool
    {
        $user = $this->findById($id);
        if ($user) {
            return (bool) $user->delete();
        }
        return false;
    }

    public function getAllRoles(): Collection
    {
        return Role::all();
    }
}
