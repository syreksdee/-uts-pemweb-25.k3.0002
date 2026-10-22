<?php

namespace App\Http\Controllers;

use App\Services\TransaksiService;
use App\Services\UserService;
use App\Services\BarangService;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    protected TransaksiService $transaksiService;
    protected UserService $userService;
    protected BarangService $barangService;
    protected UserRepositoryInterface $userRepo;

    public function __construct(
        TransaksiService $transaksiService,
        UserService $userService,
        BarangService $barangService,
        UserRepositoryInterface $userRepo
    ) {
        $this->transaksiService = $transaksiService;
        $this->userService = $userService;
        $this->barangService = $barangService;
        $this->userRepo = $userRepo;
    }

    /**
     * Admin Dashboard: Read/view laporan, ringkasan transaksi & performa
     */
    public function dashboard()
    {
        $stats = $this->transaksiService->getDashboardStats();
        $recentTransactions = $this->transaksiService->getPaginated(5);
        $totalBarang = $this->barangService->getAllActive()->count();

        return view('admin.dashboard', compact('stats', 'recentTransactions', 'totalBarang'));
    }

    /**
     * Laporan Penjualan (Read/View dengan filter tanggal, status, dan metode bayar)
     */
    public function laporan(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $status = $request->query('status');
        $search = $request->query('search');

        $laporan = $this->transaksiService->getPaginated(15, $search, $status, $startDate, $endDate);
        $stats = $this->transaksiService->getDashboardStats();

        return view('admin.laporan', compact('laporan', 'stats', 'startDate', 'endDate', 'status', 'search'));
    }

    /**
     * Detail Transaksi (Read-only view)
     */
    public function detailTransaksi(int $id)
    {
        $transaksi = $this->transaksiService->getById($id);
        if (!$transaksi) {
            return redirect()->route('admin.laporan')->with('error', 'Transaksi tidak ditemukan.');
        }

        return view('admin.transaksi_detail', compact('transaksi'));
    }

    /**
     * Manajemen Kasir (Admin mengatur kasir)
     */
    public function manageKasir(Request $request)
    {
        $search = $request->query('search');
        $kasirList = $this->userService->getPaginated(10, $search, 'kasir');

        return view('admin.kasir.index', compact('kasirList', 'search'));
    }

    public function storeKasir(Request $request)
    {
        $request->validate([
            'nim_nik' => 'required|string|unique:t002_users,nim_nik',
            'nama_lengkap' => 'required|string|max:150',
            'email' => 'required|email|unique:t002_users,email',
            'password' => 'required|min:6',
            'no_hp' => 'nullable|string|max:25',
            'alamat' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        $roles = $this->userRepo->getAllRoles();
        $kasirRole = $roles->firstWhere('kode_role', 'kasir');

        $data = $request->all();
        $data['role_id'] = $kasirRole->id;

        $this->userService->create($data, $request->file('foto'));

        return redirect()->route('admin.kasir.index')->with('success', 'Akun Kasir berhasil ditambahkan.');
    }

    public function deleteKasir(int $id)
    {
        $this->userService->delete($id);
        return redirect()->route('admin.kasir.index')->with('success', 'Akun Kasir berhasil dihapus.');
    }

    /**
     * Manajemen Pelanggan (Admin mengatur pelanggan)
     */
    public function managePelanggan(Request $request)
    {
        $search = $request->query('search');
        $pelangganList = $this->userService->getPaginated(10, $search, 'pelanggan');

        return view('admin.pelanggan.index', compact('pelangganList', 'search'));
    }

    public function storePelanggan(Request $request)
    {
        $request->validate([
            'nim_nik' => 'required|string|unique:t002_users,nim_nik',
            'nama_lengkap' => 'required|string|max:150',
            'email' => 'required|email|unique:t002_users,email',
            'password' => 'required|min:6',
            'no_hp' => 'nullable|string|max:25',
            'alamat' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        $roles = $this->userRepo->getAllRoles();
        $pelangganRole = $roles->firstWhere('kode_role', 'pelanggan');

        $data = $request->all();
        $data['role_id'] = $pelangganRole->id;

        $this->userService->create($data, $request->file('foto'));

        return redirect()->route('admin.pelanggan.index')->with('success', 'Data Pelanggan berhasil ditambahkan.');
    }

    public function deletePelanggan(int $id)
    {
        $this->userService->delete($id);
        return redirect()->route('admin.pelanggan.index')->with('success', 'Data Pelanggan berhasil dihapus.');
    }
}
