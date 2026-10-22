<?php

namespace App\Repositories\Eloquent;

use App\Models\Kategori;
use App\Repositories\Contracts\KategoriRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class KategoriRepository implements KategoriRepositoryInterface
{
    public function getAll(): Collection
    {
        return Kategori::withCount('barang')->orderBy('nama_kategori')->get();
    }

    public function getPaginated(int $perPage = 10, ?string $search = null): LengthAwarePaginator
    {
        $query = Kategori::withCount('barang');

        if (!empty($search)) {
            $query->where('nama_kategori', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
        }

        return $query->orderBy('nama_kategori')->paginate($perPage);
    }

    public function findById(int $id): ?Kategori
    {
        return Kategori::with('barang')->find($id);
    }

    public function create(array $data): Kategori
    {
        return Kategori::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $kategori = $this->findById($id);
        if ($kategori) {
            return $kategori->update($data);
        }
        return false;
    }

    public function delete(int $id): bool
    {
        $kategori = $this->findById($id);
        if ($kategori) {
            return (bool) $kategori->delete();
        }
        return false;
    }
}
