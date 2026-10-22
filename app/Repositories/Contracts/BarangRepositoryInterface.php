<?php

namespace App\Repositories\Contracts;

use App\Models\Barang;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface BarangRepositoryInterface
{
    public function getPaginated(int $perPage = 10, ?string $search = null, ?int $kategoriId = null): LengthAwarePaginator;
    public function getAllActive(): Collection;
    public function findById(int $id): ?Barang;
    public function findByKode(string $kode): ?Barang;
    public function create(array $data): Barang;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function reduceStock(int $id, int $quantity): bool;
    public function restoreStock(int $id, int $quantity): bool;
}
