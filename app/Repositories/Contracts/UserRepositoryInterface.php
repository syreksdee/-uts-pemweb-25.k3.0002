<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface
{
    public function getPaginated(int $perPage = 10, ?string $search = null, ?string $role = null): LengthAwarePaginator;
    public function findById(int $id): ?User;
    public function findByNimOrEmail(string $identifier): ?User;
    public function findByApiToken(string $token): ?User;
    public function create(array $data): User;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function getAllRoles(): Collection;
}
