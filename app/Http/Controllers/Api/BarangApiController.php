<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BarangService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class BarangApiController extends Controller
{
    protected BarangService $barangService;

    public function __construct(BarangService $barangService)
    {
        $this->barangService = $barangService;
    }

    /**
     * 1. GET /api/barang
     * Menampilkan daftar barang dengan fitur pencarian & pagination
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->query('q') ?? $request->query('search');
        $kategoriId = $request->query('kategori_id') ? (int) $request->query('kategori_id') : null;
        $perPage = $request->query('per_page') ? min(50, max(1, (int) $request->query('per_page'))) : 10;

        $paginated = $this->barangService->getPaginated($perPage, $search, $kategoriId);

        return response()->json([
            'status' => 'success',
            'message' => 'Daftar data barang berhasil diambil',
            'data' => $paginated->items(),
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        ], 200);
    }

    /**
     * 2. GET /api/barang/{id}
     * Menampilkan detail satu barang
     */
    public function show(int $id): JsonResponse
    {
        $barang = $this->barangService->getById($id);

        if (!$barang) {
            return response()->json([
                'status' => 'error',
                'message' => "Barang dengan ID {$id} tidak ditemukan.",
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Detail barang berhasil diambil',
            'data' => $barang,
        ], 200);
    }

    /**
     * 3. POST /api/barang
     * Menambahkan data barang baru (Khusus Role Gudang / Admin)
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'kategori_id' => 'required|exists:t002_kategori,id',
            'kode_barang' => 'nullable|string|max:50|unique:t002_barang,kode_barang',
            'nama_barang' => 'required|string|max:150',
            'deskripsi' => 'nullable|string',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'satuan' => 'nullable|string|max:30',
            'status' => 'nullable|in:aktif,nonaktif',
            'gambar' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi data barang gagal',
                'errors' => $validator->errors(),
            ], 400);
        }

        try {
            $barang = $this->barangService->create($validator->validated(), $request->file('gambar'));

            return response()->json([
                'status' => 'success',
                'message' => 'Barang berhasil ditambahkan',
                'data' => $barang,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menambahkan barang: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * 4. PUT / PATCH /api/barang/{id}
     * Memperbarui data barang (Khusus Role Gudang / Admin)
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $barang = $this->barangService->getById($id);
        if (!$barang) {
            return response()->json([
                'status' => 'error',
                'message' => "Barang dengan ID {$id} tidak ditemukan.",
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'kategori_id' => 'sometimes|required|exists:t002_kategori,id',
            'kode_barang' => 'sometimes|nullable|string|max:50|unique:t002_barang,kode_barang,' . $id,
            'nama_barang' => 'sometimes|required|string|max:150',
            'deskripsi' => 'nullable|string',
            'harga_beli' => 'sometimes|required|numeric|min:0',
            'harga_jual' => 'sometimes|required|numeric|min:0',
            'stok' => 'sometimes|required|integer|min:0',
            'satuan' => 'nullable|string|max:30',
            'status' => 'nullable|in:aktif,nonaktif',
            'gambar' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi pembaruan barang gagal',
                'errors' => $validator->errors(),
            ], 400);
        }

        try {
            $this->barangService->update($id, $validator->validated(), $request->file('gambar'));
            $updated = $this->barangService->getById($id);

            return response()->json([
                'status' => 'success',
                'message' => 'Data barang berhasil diperbarui',
                'data' => $updated,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memperbarui barang: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * 5. DELETE /api/barang/{id}
     * Menghapus barang (Khusus Role Gudang / Admin)
     */
    public function destroy(int $id): JsonResponse
    {
        $barang = $this->barangService->getById($id);
        if (!$barang) {
            return response()->json([
                'status' => 'error',
                'message' => "Barang dengan ID {$id} tidak ditemukan.",
            ], 404);
        }

        $deleted = $this->barangService->delete($id);

        if ($deleted) {
            return response()->json([
                'status' => 'success',
                'message' => "Barang '{$barang->nama_barang}' berhasil dihapus.",
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Gagal menghapus data barang.',
        ], 400);
    }
}
