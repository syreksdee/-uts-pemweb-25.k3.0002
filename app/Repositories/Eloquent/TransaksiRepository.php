<?php

namespace App\Repositories\Eloquent;

use App\Models\Transaksi;
use App\Repositories\Contracts\TransaksiRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class TransaksiRepository implements TransaksiRepositoryInterface
{
    public function getPaginated(int $perPage = 10, ?string $search = null, ?string $status = null, ?string $startDate = null, ?string $endDate = null): LengthAwarePaginator
    {
        $query = Transaksi::with(['pelanggan', 'kasir', 'detail.barang']);

        if (!empty($search)) {
            $query->cari($search);
        }

        if (!empty($status)) {
            $query->where('status_pembayaran', $status);
        }

        if (!empty($startDate) && !empty($endDate)) {
            $query->whereBetween('tanggal_transaksi', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }

        return $query->orderBy('tanggal_transaksi', 'desc')->paginate($perPage);
    }

    public function findById(int $id): ?Transaksi
    {
        return Transaksi::with(['pelanggan', 'kasir', 'detail.barang'])->find($id);
    }

    public function findByNomor(string $nomor): ?Transaksi
    {
        return Transaksi::with(['pelanggan', 'kasir', 'detail.barang'])->where('nomor_transaksi', $nomor)->first();
    }

    public function create(array $data): Transaksi
    {
        return Transaksi::create($data);
    }

    public function updateStatus(int $id, string $status, ?float $totalBayar = null, ?float $kembalian = null): bool
    {
        $trx = Transaksi::find($id);
        if ($trx) {
            $update = ['status_pembayaran' => $status];
            if ($totalBayar !== null) {
                $update['total_bayar'] = $totalBayar;
            }
            if ($kembalian !== null) {
                $update['kembalian'] = $kembalian;
            }
            return $trx->update($update);
        }
        return false;
    }

    public function getByUser(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        return Transaksi::with(['detail.barang'])
            ->where('user_pelanggan_id', $userId)
            ->orderBy('tanggal_transaksi', 'desc')
            ->paginate($perPage);
    }

    public function getSummaryStats(): array
    {
        $totalPendapatan = Transaksi::where('status_pembayaran', 'lunas')->sum('total_harga');
        $totalTransaksi = Transaksi::count();
        $transaksiLunas = Transaksi::where('status_pembayaran', 'lunas')->count();
        $transaksiMenunggu = Transaksi::where('status_pembayaran', 'menunggu')->count();
        $totalItemTerjual = DB::table('t002_detail_transaksi')
            ->join('t002_transaksi', 't002_detail_transaksi.transaksi_id', '=', 't002_transaksi.id')
            ->where('t002_transaksi.status_pembayaran', 'lunas')
            ->sum('t002_detail_transaksi.jumlah');

        return [
            'total_pendapatan' => (float) $totalPendapatan,
            'total_transaksi' => $totalTransaksi,
            'transaksi_lunas' => $transaksiLunas,
            'transaksi_menunggu' => $transaksiMenunggu,
            'total_item_terjual' => (int) $totalItemTerjual,
        ];
    }

    public function getDailySalesChartData(): array
    {
        $sales = Transaksi::selectRaw('DATE(tanggal_transaksi) as tgl, SUM(total_harga) as total, COUNT(*) as jml')
            ->where('status_pembayaran', 'lunas')
            ->groupBy('tgl')
            ->orderBy('tgl', 'asc')
            ->limit(14)
            ->get();

        $labels = [];
        $values = [];
        foreach ($sales as $s) {
            $labels[] = date('d M', strtotime($s->tgl));
            $values[] = (float) $s->total;
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }
}
