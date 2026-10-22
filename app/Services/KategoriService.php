<?php

namespace App\Services;

use App\Models\Kategori;
use App\Repositories\Contracts\KategoriRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class KategoriService
{
    protected KategoriRepositoryInterface $kategoriRepo;

    public function __construct(KategoriRepositoryInterface $kategoriRepo)
    {
        $this->kategoriRepo = $kategoriRepo;
    }

    public function getAll(): Collection
    {
        return $this->kategoriRepo->getAll();
    }

    public function getPaginated(int $perPage = 10, ?string $search = null): LengthAwarePaginator
    {
        return $this->kategoriRepo->getPaginated($perPage, $search);
    }

    public function getById(int $id): ?Kategori
    {
        return $this->kategoriRepo->findById($id);
    }

    public function create(array $data): Kategori
    {
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['nama_kategori']);
        }
        return $this->kategoriRepo->create($data);
    }

    public function update(int $id, array $data): bool
    {
        if (isset($data['nama_kategori']) && empty($data['slug'])) {
            $data['slug'] = Str::slug($data['nama_kategori']);
        }
        return $this->kategoriRepo->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->kategoriRepo->delete($id);
    }
}
