<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Barang;
use App\Models\Kategori;
use App\Services\TransaksiService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SiTokoRequirementsTest extends TestCase
{
    /**
     * Requirement 10: Endpoint GET /api/identitas
     */
    public function test_api_identitas_endpoint(): void
    {
        $response = $this->getJson('/api/identitas');

        $response->assertStatus(200)
            ->assertJson([
                'nim' => '25.K3.0002',
                'nama' => 'Aloysius Jaison Gondopurwanto',
                'aplikasi' => 'SiToko — Toko Online Sederhana 002',
            ])
            ->assertJsonStructure(['nim', 'nama', 'foto', 'aplikasi']);
    }

    /**
     * Requirement 10: REST API Authentication & Role Permissions Matrix
     */
    public function test_api_barang_unauthorized_without_token(): void
    {
        $response = $this->getJson('/api/barang');
        $response->assertStatus(401);
    }

    public function test_api_barang_list_and_detail_with_token(): void
    {
        $admin = User::where('nim_nik', '25.K3.0002')->first();

        // 1. GET Daftar
        $responseList = $this->withHeader('Authorization', 'Bearer ' . $admin->api_token)
            ->getJson('/api/barang');
        $responseList->assertStatus(200)
            ->assertJsonStructure(['status', 'data', 'pagination']);

        // 2. GET Detail
        $barang = Barang::first();
        $responseDetail = $this->withHeader('Authorization', 'Bearer ' . $admin->api_token)
            ->getJson('/api/barang/' . $barang->id);
        $responseDetail->assertStatus(200)
            ->assertJson(['status' => 'success']);

        // 3. GET Not Found (404)
        $response404 = $this->withHeader('Authorization', 'Bearer ' . $admin->api_token)
            ->getJson('/api/barang/99999');
        $response404->assertStatus(404);
    }

    public function test_api_barang_role_matrix_and_crud(): void
    {
        $pelanggan = User::where('nim_nik', 'CUST-001')->first();
        $gudang = User::where('nim_nik', 'GDG-001')->first();
        $kategori = Kategori::first();

        // 4. Role Pelanggan dilarang POST (403 Forbidden)
        $respForbidden = $this->withHeader('Authorization', 'Bearer ' . $pelanggan->api_token)
            ->postJson('/api/barang', [
                'nama_barang' => 'Test Barang Terlarang',
                'kategori_id' => $kategori->id,
            ]);
        $respForbidden->assertStatus(403);

        // 5. Validasi Gagal (400 Bad Request)
        $respBad = $this->withHeader('Authorization', 'Bearer ' . $gudang->api_token)
            ->postJson('/api/barang', [
                'nama_barang' => '',
            ]);
        $respBad->assertStatus(400);

        // 6. POST Sukses oleh Gudang (201 Created)
        $respCreate = $this->withHeader('Authorization', 'Bearer ' . $gudang->api_token)
            ->postJson('/api/barang', [
                'kategori_id' => $kategori->id,
                'nama_barang' => 'Barang Uji Unit Test',
                'harga_beli' => 7000,
                'harga_jual' => 10000,
                'stok' => 50,
                'satuan' => 'pcs',
            ]);
        $respCreate->assertStatus(201);
        $newId = $respCreate->json('data.id');

        // 7. PUT / Update Sukses (200 OK)
        $respUpdate = $this->withHeader('Authorization', 'Bearer ' . $gudang->api_token)
            ->putJson('/api/barang/' . $newId, [
                'nama_barang' => 'Barang Uji Unit Test Updated',
                'harga_jual' => 12000,
            ]);
        $respUpdate->assertStatus(200);

        // 8. DELETE Sukses (200 OK)
        $respDelete = $this->withHeader('Authorization', 'Bearer ' . $gudang->api_token)
            ->deleteJson('/api/barang/' . $newId);
        $respDelete->assertStatus(200);
    }

    /**
     * Requirement 4a: UKasir modul Kasir mengurangi stok berdasar penjualan
     */
    public function test_kasir_pos_reduces_stock_automatically(): void
    {
        $service = app(TransaksiService::class);
        $kasir = User::where('nim_nik', 'KASIR-001')->first();
        $barang = Barang::where('stok', '>=', 5)->first();

        $stockBefore = $barang->stok;

        $transaksi = $service->processPosSale([
            'items' => [
                ['barang_id' => $barang->id, 'jumlah' => 2]
            ],
            'metode_pembayaran' => 'tunai',
            'total_bayar' => 100000,
            'nama_pelanggan' => 'Pembeli Tes Kasir'
        ], $kasir);

        $this->assertEquals('lunas', $transaksi->status_pembayaran);
        $this->assertEquals($stockBefore - 2, $barang->fresh()->stok);
    }

    /**
     * Requirement 3a: Validasi Foto Profil (JPG/PNG, Maksimal 2MB)
     */
    public function test_profile_photo_validation(): void
    {
        Storage::fake('public');
        $user = User::where('nim_nik', '25.K3.0002')->first();

        // 1. Gagal jika tipe bukan jpg/png (contoh txt / pdf)
        $fakeTxt = UploadedFile::fake()->create('dokumen.pdf', 500);
        $respMime = $this->actingAs($user)
            ->put(route('profile.update'), [
                'nama_lengkap' => 'Aloysius Jaison Gondopurwanto',
                'email' => $user->email,
                'foto' => $fakeTxt,
            ]);
        $respMime->assertSessionHasErrors(['foto']);

        // 2. Gagal jika ukuran lebih dari 2MB (2500 KB)
        $fakeLarge = UploadedFile::fake()->create('foto_besar.jpg', 2500);
        $respSize = $this->actingAs($user)
            ->put(route('profile.update'), [
                'nama_lengkap' => 'Aloysius Jaison Gondopurwanto',
                'email' => $user->email,
                'foto' => $fakeLarge,
            ]);
        $respSize->assertSessionHasErrors(['foto']);
    }

    /**
     * Requirement 7: Tab title dan Navbar mengandung nama toko
     */
    public function test_store_name_in_title_and_navbar(): void
    {
        $response = $this->get('/toko');

        $response->assertStatus(200);
        $response->assertSee('SiToko — Toko Online Sederhana 002');
        $response->assertSee('2026 Aloysius Jaison Gondopurwanto');
        $response->assertSee('25.K3.0002');
    }
}
