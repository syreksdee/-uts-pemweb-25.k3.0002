<?php

namespace App\Http\Controllers;

use App\Services\BarangService;
use App\Services\KategoriService;
use Illuminate\Http\Request;
use Exception;

class BarangController extends Controller
{
    protected BarangService $barangService;
    protected KategoriService $kategoriService;

    public function __construct(BarangService $barangService, KategoriService $kategoriService)
    {
        $this->barangService = $barangService;
        $this->kategoriService = $kategoriService;
    }

    /**
     * Halaman Daftar Barang (Pencarian & Pagination)
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $kategoriId = $request->query('kategori_id') ? (int) $request->query('kategori_id') : null;

        $barangList = $this->barangService->getPaginated(10, $search, $kategoriId);
        $kategoriList = $this->kategoriService->getAll();

        return view('gudang.barang.index', compact('barangList', 'kategoriList', 'search', 'kategoriId'));
    }

    public function create()
    {
        $kategoriList = $this->kategoriService->getAll();
        return view('gudang.barang.create', compact('kategoriList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kategori_id' => 'required|exists:t002_kategori,id',
            'kode_barang' => 'nullable|string|max:50|unique:t002_barang,kode_barang',
            'nama_barang' => 'required|string|max:150',
            'deskripsi' => 'nullable|string',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'satuan' => 'required|string|max:30',
            'status' => 'required|in:aktif,nonaktif',
            'gambar' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        try {
            $this->barangService->create($request->all(), $request->file('gambar'));
            return redirect()->route('gudang.barang.index')->with('success', 'Barang baru berhasil ditambahkan ke inventaris!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(int $id)
    {
        $barang = $this->barangService->getById($id);
        if (!$barang) {
            return redirect()->route('gudang.barang.index')->with('error', 'Barang tidak ditemukan.');
        }

        $kategoriList = $this->kategoriService->getAll();
        return view('gudang.barang.edit', compact('barang', 'kategoriList'));
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'kategori_id' => 'required|exists:t002_kategori,id',
            'kode_barang' => 'nullable|string|max:50|unique:t002_barang,kode_barang,' . $id,
            'nama_barang' => 'required|string|max:150',
            'deskripsi' => 'nullable|string',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'satuan' => 'required|string|max:30',
            'status' => 'required|in:aktif,nonaktif',
            'gambar' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        try {
            $this->barangService->update($id, $request->all(), $request->file('gambar'));
            return redirect()->route('gudang.barang.index')->with('success', 'Data barang berhasil diperbarui!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(int $id)
    {
        $deleted = $this->barangService->delete($id);
        if ($deleted) {
            return redirect()->route('gudang.barang.index')->with('success', 'Barang berhasil dihapus/dinonaktifkan.');
        }

        return redirect()->route('gudang.barang.index')->with('error', 'Gagal menghapus barang.');
    }
}
