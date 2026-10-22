<?php

namespace App\Repositories\Contracts;

use App\Models\Transaksi;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface TransaksiRepositoryInterface
{
    public function getPaginated(int $perPage = 10, ?string $search = null, ?string $status = null, ?string $startDate = null, ?string $endDate = null): LengthAwarePaginator;
    public function findById(int $id): ?Transaksi;
    public function findByNomor(string $nomor): ?Transaksi;
    public function create(array $data): Transaksi;
    public function updateStatus(int $id, string $status, ?float $totalBayar = null, ?float $kembalian = null): bool;
    public function getByUser(int $userId, int $perPage = 10): LengthAwarePaginator;
    public function getSummaryStats(): array;
    public function getDailySalesChartData(): array;
}
