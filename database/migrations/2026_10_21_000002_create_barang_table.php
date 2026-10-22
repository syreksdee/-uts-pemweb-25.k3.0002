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
        Schema::create('t002_barang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')->constrained('t002_kategori')->cascadeOnDelete();
            $table->string('kode_barang', 50)->unique();
            $table->string('nama_barang', 150);
            $table->string('slug', 180)->unique();
            $table->text('deskripsi')->nullable();
            $table->decimal('harga_beli', 14, 2)->default(0);
            $table->decimal('harga_jual', 14, 2)->default(0);
            $table->integer('stok')->default(0);
            $table->string('satuan', 30)->default('pcs');
            $table->string('gambar', 255)->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t002_barang');
    }
};
