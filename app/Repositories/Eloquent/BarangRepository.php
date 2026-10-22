<?php

namespace App\Repositories\Eloquent;

use App\Models\Barang;
use App\Repositories\Contracts\BarangRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class BarangRepository implements BarangRepositoryInterface
{
    public function getPaginated(int $perPage = 10, ?string $search = null, ?int $kategoriId = null): LengthAwarePaginator
    {
        $query = Barang::with('kategori');

        if (!empty($search)) {
            // Prepared statement parameter binding via Eloquent scope
            $query->cari($search);
        }

        if (!empty($kategoriId)) {
            $query->where('kategori_id', $kategoriId);
        }

        return $query->orderBy('id', 'desc')->paginate($perPage);
    }

    public function getAllActive(): Collection
    {
        return Barang::with('kategori')->aktif()->where('stok', '>', 0)->orderBy('nama_barang')->get();
    }

    public function findById(int $id): ?Barang
    {
        return Barang::with('kategori')->find($id);
    }

    public function findByKode(string $kode): ?Barang
    {
        return Barang::with('kategori')->where('kode_barang', $kode)->first();
    }

    public function create(array $data): Barang
    {
        return Barang::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $barang = $this->findById($id);
        if ($barang) {
            return $barang->update($data);
        }
        return false;
    }

    public function delete(int $id): bool
    {
        $barang = $this->findById($id);
        if ($barang) {
            return (bool) $barang->delete();
        }
        return false;
    }

    public function reduceStock(int $id, int $quantity): bool
    {
        // Atomic decrement using prepared parameter binding
        $updated = DB::table('t002_barang')
            ->where('id', $id)
            ->where('stok', '>=', $quantity)
            ->decrement('stok', $quantity);

        return $updated > 0;
    }

    public function restoreStock(int $id, int $quantity): bool
    {
        $updated = DB::table('t002_barang')
            ->where('id', $id)
            ->increment('stok', $quantity);

        return $updated > 0;
    }
}
