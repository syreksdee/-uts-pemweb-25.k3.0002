<?php

namespace App\Services;

use App\Models\Barang;
use App\Repositories\Contracts\BarangRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use InvalidArgumentException;

class BarangService
{
    protected BarangRepositoryInterface $barangRepo;

    public function __construct(BarangRepositoryInterface $barangRepo)
    {
        $this->barangRepo = $barangRepo;
    }

    public function getPaginated(int $perPage = 10, ?string $search = null, ?int $kategoriId = null): LengthAwarePaginator
    {
        return $this->barangRepo->getPaginated($perPage, $search, $kategoriId);
    }

    public function getAllActive(): Collection
    {
        return $this->barangRepo->getAllActive();
    }

    public function getById(int $id): ?Barang
    {
        return $this->barangRepo->findById($id);
    }

    public function create(array $data, $imageFile = null): Barang
    {
        // Business logic: Generate kode_barang if empty
        if (empty($data['kode_barang'])) {
            $data['kode_barang'] = 'BRG-' . strtoupper(Str::random(6));
        }

        // Generate slug
        $data['slug'] = Str::slug($data['nama_barang']) . '-' . Str::random(4);

        // Upload image handling
        if ($imageFile && $imageFile->isValid()) {
            $filename = 'brg_' . time() . '_' . Str::random(6) . '.' . $imageFile->getClientOriginalExtension();
            $imageFile->move(public_path('uploads/barang'), $filename);
            $data['gambar'] = 'uploads/barang/' . $filename;
        }

        return $this->barangRepo->create($data);
    }

    public function update(int $id, array $data, $imageFile = null): bool
    {
        $barang = $this->barangRepo->findById($id);
        if (!$barang) {
            throw new InvalidArgumentException("Barang dengan ID {$id} tidak ditemukan.");
        }

        if (isset($data['nama_barang']) && $data['nama_barang'] !== $barang->nama_barang) {
            $data['slug'] = Str::slug($data['nama_barang']) . '-' . Str::random(4);
        }

        // Upload image replacement
        if ($imageFile && $imageFile->isValid()) {
            // Delete old custom image if exists
            if ($barang->gambar && file_exists(public_path($barang->gambar)) && !Str::contains($barang->gambar, ['kopi.jpg', 'teh.jpg', 'beras.jpg'])) {
                @unlink(public_path($barang->gambar));
            }

            $filename = 'brg_' . time() . '_' . Str::random(6) . '.' . $imageFile->getClientOriginalExtension();
            $imageFile->move(public_path('uploads/barang'), $filename);
            $data['gambar'] = 'uploads/barang/' . $filename;
        }

        return $this->barangRepo->update($id, $data);
    }

    public function delete(int $id): bool
    {
        $barang = $this->barangRepo->findById($id);
        if (!$barang) {
            return false;
        }

        // Prevent deletion if items are in transactions
        if ($barang->detailTransaksi()->count() > 0) {
            // Soft deactivate instead
            return $this->barangRepo->update($id, ['status' => 'nonaktif']);
        }

        return $this->barangRepo->delete($id);
    }
}
