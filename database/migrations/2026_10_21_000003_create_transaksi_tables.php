<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('t002_transaksi', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_transaksi', 60)->unique();
            $table->foreignId('user_pelanggan_id')->nullable()->constrained('t002_users')->nullOnDelete();
            $table->foreignId('user_kasir_id')->nullable()->constrained('t002_users')->nullOnDelete();
            $table->string('nama_pelanggan', 150)->default('Pelanggan Umum');
            $table->dateTime('tanggal_transaksi');
            $table->decimal('total_harga', 14, 2)->default(0);
            $table->decimal('total_bayar', 14, 2)->default(0);
            $table->decimal('kembalian', 14, 2)->default(0);
            $table->enum('metode_pembayaran', ['tunai', 'qris', 'va_bca', 'va_mandiri', 'va_bri', 'va_bni'])->default('tunai');
            $table->string('nomor_va', 60)->nullable();
            $table->text('qris_payload')->nullable();
            $table->enum('status_pembayaran', ['menunggu', 'lunas', 'batal'])->default('lunas');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        Schema::create('t002_detail_transaksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_id')->constrained('t002_transaksi')->cascadeOnDelete();
            $table->foreignId('barang_id')->constrained('t002_barang')->restrictOnDelete();
            $table->string('nama_barang', 150);
            $table->integer('jumlah')->default(1);
            $table->decimal('harga_satuan', 14, 2)->default(0);
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t002_detail_transaksi');
        Schema::dropIfExists('t002_transaksi');
    }
};
