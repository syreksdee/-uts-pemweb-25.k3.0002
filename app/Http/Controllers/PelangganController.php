<?php

namespace App\Http\Controllers;

use App\Services\BarangService;
use App\Services\KategoriService;
use App\Services\TransaksiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class PelangganController extends Controller
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
     * Mobile-first Katalog Toko Online
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $kategoriId = $request->query('kategori_id') ? (int) $request->query('kategori_id') : null;

        $barangList = $this->barangService->getPaginated(12, $search, $kategoriId);
        $kategoriList = $this->kategoriService->getAll();

        return view('pelanggan.index', compact('barangList', 'kategoriList', 'search', 'kategoriId'));
    }

    /**
     * Detail Produk
     */
    public function show(int $id)
    {
        $barang = $this->barangService->getById($id);
        if (!$barang) {
            return redirect()->route('toko.index')->with('error', 'Produk tidak ditemukan.');
        }

        return view('pelanggan.detail', compact('barang'));
    }

    /**
     * Halaman Keranjang Belanja
     */
    public function keranjang()
    {
        return view('pelanggan.keranjang');
    }

    /**
     * Proses Checkout Pesanan Online
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:t002_barang,id',
            'items.*.jumlah' => 'required|integer|min:1',
            'metode_pembayaran' => 'required|in:qris,va_bca,va_mandiri,va_bri,va_bni',
            'catatan' => 'nullable|string|max:255',
        ]);

        try {
            $user = Auth::user();
            $transaksi = $this->transaksiService->processCustomerCheckout($request->all(), $user);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Pesanan berhasil dibuat! Silakan selesaikan pembayaran.',
                    'redirect' => route('toko.bayar', $transaksi->id),
                    'transaksi' => $transaksi,
                ]);
            }

            return redirect()->route('toko.bayar', $transaksi->id)->with('success', 'Pesanan berhasil dibuat!');
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
     * Halaman Pembayaran (QRIS & Virtual Account Simulation)
     */
    public function bayar(int $id)
    {
        $transaksi = $this->transaksiService->getById($id);
        if (!$transaksi) {
            return redirect()->route('toko.index')->with('error', 'Pesanan tidak ditemukan.');
        }

        return view('pelanggan.bayar', compact('transaksi'));
    }

    /**
     * Simulasi Pembayaran QRIS / VA Sukses
     */
    public function simulatePayment(int $id)
    {
        try {
            $transaksi = $this->transaksiService->simulatePayment($id);

            return redirect()->route('toko.bayar', $id)->with('success', 'Pembayaran berhasil dikonfirmasi! Stok barang telah otomatis terpotong.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Riwayat Pesanan Pelanggan
     */
    public function pesananSaya()
    {
        $user = Auth::user();
        $pesananList = $this->transaksiService->getByPelanggan($user->id, 10);

        return view('pelanggan.pesanan_saya', compact('pesananList'));
    }
}
