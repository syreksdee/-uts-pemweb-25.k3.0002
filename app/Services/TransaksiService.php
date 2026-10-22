<?php

namespace App\Services;

use App\Models\Transaksi;
use App\Models\User;
use App\Repositories\Contracts\BarangRepositoryInterface;
use App\Repositories\Contracts\TransaksiRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Exception;

class TransaksiService
{
    protected TransaksiRepositoryInterface $transaksiRepo;
    protected BarangRepositoryInterface $barangRepo;

    public function __construct(
        TransaksiRepositoryInterface $transaksiRepo,
        BarangRepositoryInterface $barangRepo
    ) {
        $this->transaksiRepo = $transaksiRepo;
        $this->barangRepo = $barangRepo;
    }

    public function getPaginated(int $perPage = 10, ?string $search = null, ?string $status = null, ?string $startDate = null, ?string $endDate = null): LengthAwarePaginator
    {
        return $this->transaksiRepo->getPaginated($perPage, $search, $status, $startDate, $endDate);
    }

    public function getById(int $id): ?Transaksi
    {
        return $this->transaksiRepo->findById($id);
    }

    public function getByNomor(string $nomor): ?Transaksi
    {
        return $this->transaksiRepo->findByNomor($nomor);
    }

    public function getByPelanggan(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->transaksiRepo->getByUser($userId, $perPage);
    }

    /**
     * Proses Penjualan Kasir (POS) - UKasir
     * Mengurangi stok otomatis berdasarkan item yang dijual
     */
    public function processPosSale(array $payload, ?User $kasir): Transaksi
    {
        if (empty($payload['items']) || !is_array($payload['items'])) {
            throw new InvalidArgumentException('Daftar barang penjualan tidak boleh kosong.');
        }

        return DB::transaction(function () use ($payload, $kasir) {
            $totalHarga = 0;
            $itemsValidated = [];

            // 1. Validasi stok semua item terlebih dahulu
            foreach ($payload['items'] as $item) {
                $barangId = (int) $item['barang_id'];
                $qty = (int) $item['jumlah'];

                if ($qty <= 0) {
                    throw new InvalidArgumentException('Jumlah barang harus minimal 1.');
                }

                $barang = $this->barangRepo->findById($barangId);
                if (!$barang) {
                    throw new InvalidArgumentException("Barang ID {$barangId} tidak ditemukan.");
                }

                if ($barang->stok < $qty) {
                    throw new InvalidArgumentException("Stok tidak mencukupi untuk '{$barang->nama_barang}'. Tersedia: {$barang->stok}, diminta: {$qty}.");
                }

                $subtotal = $qty * $barang->harga_jual;
                $totalHarga += $subtotal;

                $itemsValidated[] = [
                    'barang' => $barang,
                    'qty' => $qty,
                    'harga_satuan' => $barang->harga_jual,
                    'subtotal' => $subtotal,
                ];
            }

            // 2. Pembayaran Kasir
            $metode = $payload['metode_pembayaran'] ?? 'tunai';
            $bayar = isset($payload['total_bayar']) ? (float) $payload['total_bayar'] : $totalHarga;

            if ($metode === 'tunai' && $bayar < $totalHarga) {
                throw new InvalidArgumentException('Nominal pembayaran tunai kurang dari total belanja.');
            }

            $kembalian = max(0, $bayar - $totalHarga);
            $nomorTrx = 'POS-' . date('YmdHis') . '-' . Str::upper(Str::random(4));

            $nomorVa = null;
            $qrisPayload = null;

            if (Str::startsWith($metode, 'va_')) {
                $nomorVa = '88002' . date('dHis') . rand(10, 99);
            } elseif ($metode === 'qris') {
                $qrisPayload = '00020101021226600016ID.CO.SITOKO.WWW011893600000002' . time() . '5204581253033605802ID';
            }

            // 3. Simpan Transaksi Utama
            $transaksi = $this->transaksiRepo->create([
                'nomor_transaksi' => $nomorTrx,
                'user_pelanggan_id' => $payload['user_pelanggan_id'] ?? null,
                'user_kasir_id' => $kasir ? $kasir->id : null,
                'nama_pelanggan' => $payload['nama_pelanggan'] ?? 'Pelanggan Toko (Umum)',
                'tanggal_transaksi' => now(),
                'total_harga' => $totalHarga,
                'total_bayar' => $bayar,
                'kembalian' => $kembalian,
                'metode_pembayaran' => $metode,
                'nomor_va' => $nomorVa,
                'qris_payload' => $qrisPayload,
                'status_pembayaran' => 'lunas',
                'catatan' => $payload['catatan'] ?? 'Transaksi Kasir POS SiToko 002',
            ]);

            // 4. Simpan Detail Transaksi & Kurangi Stok Barang (Atomic Decrement)
            foreach ($itemsValidated as $valid) {
                $transaksi->detail()->create([
                    'barang_id' => $valid['barang']->id,
                    'nama_barang' => $valid['barang']->nama_barang,
                    'jumlah' => $valid['qty'],
                    'harga_satuan' => $valid['harga_satuan'],
                    'subtotal' => $valid['subtotal'],
                ]);

                // Kurangi stok
                $reduced = $this->barangRepo->reduceStock($valid['barang']->id, $valid['qty']);
                if (!$reduced) {
                    throw new Exception("Gagal mengurangi stok barang '{$valid['barang']->nama_barang}'. Silakan coba lagi.");
                }
            }

            return $transaksi->load(['detail.barang', 'kasir']);
        });
    }

    /**
     * Proses Checkout Pelanggan Online
     * Menghasilkan invoice pembayaran QRIS / Virtual Account
     */
    public function processCustomerCheckout(array $payload, User $pelanggan): Transaksi
    {
        if (empty($payload['items']) || !is_array($payload['items'])) {
            throw new InvalidArgumentException('Keranjang belanja kosong.');
        }

        return DB::transaction(function () use ($payload, $pelanggan) {
            $totalHarga = 0;
            $itemsValidated = [];

            foreach ($payload['items'] as $item) {
                $barangId = (int) $item['barang_id'];
                $qty = (int) $item['jumlah'];

                $barang = $this->barangRepo->findById($barangId);
                if (!$barang) {
                    throw new InvalidArgumentException("Barang tidak valid.");
                }

                if ($barang->stok < $qty) {
                    throw new InvalidArgumentException("Stok barang '{$barang->nama_barang}' tidak cukup (tersedia: {$barang->stok}).");
                }

                $subtotal = $qty * $barang->harga_jual;
                $totalHarga += $subtotal;

                $itemsValidated[] = [
                    'barang' => $barang,
                    'qty' => $qty,
                    'harga_satuan' => $barang->harga_jual,
                    'subtotal' => $subtotal,
                ];
            }

            $metode = $payload['metode_pembayaran'] ?? 'qris';
            $nomorTrx = 'INV-' . date('YmdHis') . '-' . Str::upper(Str::random(4));

            $nomorVa = null;
            $qrisPayload = null;

            if (Str::startsWith($metode, 'va_')) {
                $prefixBank = match($metode) {
                    'va_bca' => '88002',
                    'va_mandiri' => '89002',
                    'va_bri' => '87002',
                    'va_bni' => '86002',
                    default => '88002'
                };
                $nomorVa = $prefixBank . date('md') . rand(10000, 99999);
            } else {
                $qrisPayload = '00020101021226600016ID.CO.SITOKO.WWW011893600000002' . time() . '5204581253033605802ID';
            }

            $transaksi = $this->transaksiRepo->create([
                'nomor_transaksi' => $nomorTrx,
                'user_pelanggan_id' => $pelanggan->id,
                'user_kasir_id' => null,
                'nama_pelanggan' => $pelanggan->nama_lengkap,
                'tanggal_transaksi' => now(),
                'total_harga' => $totalHarga,
                'total_bayar' => 0,
                'kembalian' => 0,
                'metode_pembayaran' => $metode,
                'nomor_va' => $nomorVa,
                'qris_payload' => $qrisPayload,
                'status_pembayaran' => 'menunggu',
                'catatan' => $payload['catatan'] ?? 'Pesanan online SiToko 002',
            ]);

            foreach ($itemsValidated as $valid) {
                $transaksi->detail()->create([
                    'barang_id' => $valid['barang']->id,
                    'nama_barang' => $valid['barang']->nama_barang,
                    'jumlah' => $valid['qty'],
                    'harga_satuan' => $valid['harga_satuan'],
                    'subtotal' => $valid['subtotal'],
                ]);
            }

            return $transaksi->load('detail.barang');
        });
    }

    /**
     * Konfirmasi / Simulasi Pembayaran QRIS atau Virtual Account
     */
    public function simulatePayment(int $transaksiId): Transaksi
    {
        return DB::transaction(function () use ($transaksiId) {
            $transaksi = $this->transaksiRepo->findById($transaksiId);
            if (!$transaksi) {
                throw new InvalidArgumentException('Transaksi tidak ditemukan.');
            }

            if ($transaksi->status_pembayaran === 'lunas') {
                return $transaksi;
            }

            // Kurangi stok barang karena pesanan sekarang lunas
            foreach ($transaksi->detail as $item) {
                $reduced = $this->barangRepo->reduceStock($item->barang_id, $item->jumlah);
                if (!$reduced) {
                    throw new Exception("Gagal menyelesaikan pembayaran: stok barang '{$item->nama_barang}' tidak mencukupi.");
                }
            }

            $this->transaksiRepo->updateStatus($transaksiId, 'lunas', $transaksi->total_harga, 0);

            return $transaksi->fresh(['detail.barang', 'pelanggan']);
        });
    }

    public function getDashboardStats(): array
    {
        $stats = $this->transaksiRepo->getSummaryStats();
        $chart = $this->transaksiRepo->getDailySalesChartData();

        return array_merge($stats, [
            'chart_labels' => $chart['labels'],
            'chart_values' => $chart['values'],
        ]);
    }
}
