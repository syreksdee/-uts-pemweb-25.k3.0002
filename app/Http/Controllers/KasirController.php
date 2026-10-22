<?php

namespace App\Http\Controllers;

use App\Services\BarangService;
use App\Services\KategoriService;
use App\Services\TransaksiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class KasirController extends Controller
{
    protected BarangService $barangService;
    protected KategoriService $kategoriService;
    protected TransaksiService $transaksiService;

    public function __construct(
        BarangService $barangService,
        KategoriService $kategoriService,
        TransaksiService $transaksiService
    ) {
        $this->barangService = $barangService;
        $this->kategoriService = $kategoriService;
        $this->transaksiService = $transaksiService;
    }

    /**
     * Tampilan Desktop POS Kasir
     */
    public function pos(Request $request)
    {
        $search = $request->query('search');
        $kategoriId = $request->query('kategori_id');

        $barangList = $this->barangService->getPaginated(18, $search, $kategoriId);
        $kategoriList = $this->kategoriService->getAll();

        return view('kasir.pos', compact('barangList', 'kategoriList', 'search', 'kategoriId'));
    }

    /**
     * Memproses Penjualan Kasir & Mengurangi Stok Otomatis
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:t002_barang,id',
            'items.*.jumlah' => 'required|integer|min:1',
            'metode_pembayaran' => 'required|in:tunai,qris,va_bca,va_mandiri,va_bri',
            'total_bayar' => 'nullable|numeric|min:0',
            'nama_pelanggan' => 'nullable|string|max:100',
            'catatan' => 'nullable|string',
        ]);

        try {
            $transaksi = $this->transaksiService->processPosSale($request->all(), Auth::user());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Transaksi berhasil diproses dan stok telah berkurang!',
                    'transaksi' => $transaksi,
                ]);
            }

            return redirect()->route('kasir.struk', $transaksi->id)->with('success', 'Transaksi berhasil disimpan!');
        } catch (Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage(),
                ], 400);
            }

            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Tampilan Struk Transaksi Kasir
     */
    public function struk(int $id)
    {
        $transaksi = $this->transaksiService->getById($id);
        if (!$transaksi) {
            return redirect()->route('kasir.pos')->with('error', 'Transaksi tidak ditemukan.');
        }

        return view('kasir.struk', compact('transaksi'));
    }

    /**
     * Riwayat Transaksi Kasir
     */
    public function riwayat(Request $request)
    {
        $search = $request->query('search');
        $transaksiList = $this->transaksiService->getPaginated(15, $search);

        return view('kasir.riwayat', compact('transaksiList', 'search'));
    }
}
