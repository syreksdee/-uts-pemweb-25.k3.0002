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
        // Tabel Role / Level Pengguna
        Schema::create('t002_roles', function (Blueprint $table) {
            $table->id();
            $table->string('kode_role', 30)->unique();
            $table->string('nama_role', 100);
            $table->string('deskripsi', 255)->nullable();
            $table->timestamps();
        });

        // Tabel Pengguna
        Schema::create('t002_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('t002_roles')->cascadeOnDelete();
            $table->string('nim_nik', 50)->unique();
            $table->string('nama_lengkap', 150);
            $table->string('email', 100)->unique();
            $table->string('password');
            $table->string('no_hp', 25)->nullable();
            $table->text('alamat')->nullable();
            $table->string('foto')->nullable();
            $table->string('api_token', 80)->unique()->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('t002_users');
        Schema::dropIfExists('t002_roles');
    }
};
