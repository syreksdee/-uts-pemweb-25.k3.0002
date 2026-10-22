<?php

namespace App\Http\Controllers;

use App\Services\KategoriService;
use Illuminate\Http\Request;
use Exception;

class KategoriController extends Controller
{
    protected KategoriService $kategoriService;

    public function __construct(KategoriService $kategoriService)
    {
        $this->kategoriService = $kategoriService;
    }

    /**
     * Halaman Daftar Kategori (Pencarian & Pagination)
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $kategoriList = $this->kategoriService->getPaginated(8, $search);

        return view('gudang.kategori.index', compact('kategoriList', 'search'));
    }

    public function create()
    {
        return view('gudang.kategori.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:100|unique:t002_kategori,nama_kategori',
            'deskripsi' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
        ]);

        try {
            $this->kategoriService->create($request->all());
            return redirect()->route('gudang.kategori.index')->with('success', 'Kategori baru berhasil ditambahkan!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(int $id)
    {
        $kategori = $this->kategoriService->getById($id);
        if (!$kategori) {
            return redirect()->route('gudang.kategori.index')->with('error', 'Kategori tidak ditemukan.');
        }

        return view('gudang.kategori.edit', compact('kategori'));
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:100|unique:t002_kategori,nama_kategori,' . $id,
            'deskripsi' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
        ]);

        try {
            $this->kategoriService->update($id, $request->all());
            return redirect()->route('gudang.kategori.index')->with('success', 'Kategori berhasil diperbarui!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(int $id)
    {
        $kategori = $this->kategoriService->getById($id);
        if ($kategori && $kategori->barang()->count() > 0) {
            return redirect()->route('gudang.kategori.index')->with('error', "Kategori '{$kategori->nama_kategori}' tidak dapat dihapus karena masih memiliki relasi dengan barang!");
        }

        $this->kategoriService->delete($id);
        return redirect()->route('gudang.kategori.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
