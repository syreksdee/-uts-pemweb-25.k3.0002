<?php

namespace App\Repositories\Contracts;

use App\Models\Kategori;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface KategoriRepositoryInterface
{
    public function getAll(): Collection;
    public function getPaginated(int $perPage = 10, ?string $search = null): LengthAwarePaginator;
    public function findById(int $id): ?Kategori;
    public function create(array $data): Kategori;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}
