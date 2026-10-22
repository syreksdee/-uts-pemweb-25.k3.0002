<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Role;
use App\Models\User;
use App\Models\Kategori;
use App\Models\Barang;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. ROLES
        $roleAdmin = Role::create([
            'kode_role' => 'admin',
            'nama_role' => 'Administrator',
            'deskripsi' => 'Akses penuh laporan, manajemen kasir & pelanggan (Read/view focus)',
        ]);

        $roleKasir = Role::create([
            'kode_role' => 'kasir',
            'nama_role' => 'Kasir (POS)',
            'deskripsi' => 'Modul kasir penjualan, cetak struk, dan pengurangan stok otomatis',
        ]);

        $roleGudang = Role::create([
            'kode_role' => 'gudang',
            'nama_role' => 'Staf Gudang',
            'deskripsi' => 'Modul inventaris dan CRUD data barang & stok',
        ]);

        $rolePelanggan = Role::create([
            'kode_role' => 'pelanggan',
            'nama_role' => 'Pelanggan',
            'deskripsi' => 'Modul belanja katalog toko online, checkout, QRIS & VA',
        ]);

        // 2. USERS (12 Users Dummy)
        // Admin (Wajib: login 25.K3.0002, nama Aloysius Jaison Gondopurwanto)
        $adminUser = User::create([
            'role_id' => $roleAdmin->id,
            'nim_nik' => '25.K3.0002',
            'nama_lengkap' => 'Aloysius Jaison Gondopurwanto',
            'email' => 'admin@sitoko.com',
            'password' => Hash::make('password'),
            'no_hp' => '081234567890',
            'alamat' => 'Jl. Pemuda No. 02, Semarang, Jawa Tengah',
            'foto' => 'uploads/profil/0002.jpg',
            'api_token' => 'tok_admin_25k30002_sitoko_secret_token_12345',
        ]);

        // Kasir
        $kasir1 = User::create([
            'role_id' => $roleKasir->id,
            'nim_nik' => 'KASIR-001',
            'nama_lengkap' => 'Siti Nurhaliza',
            'email' => 'kasir1@sitoko.com',
            'password' => Hash::make('password'),
            'no_hp' => '082134567801',
            'alamat' => 'Jl. Pandanaran No. 12, Semarang',
            'foto' => 'uploads/profil/default.png',
            'api_token' => 'tok_kasir1_sitoko_secret_token_12345',
        ]);

        $kasir2 = User::create([
            'role_id' => $roleKasir->id,
            'nim_nik' => 'KASIR-002',
            'nama_lengkap' => 'Ahmad Fadhil',
            'email' => 'kasir2@sitoko.com',
            'password' => Hash::make('password'),
            'no_hp' => '082134567802',
            'alamat' => 'Jl. Gajahmada No. 44, Semarang',
            'foto' => 'uploads/profil/default.png',
            'api_token' => 'tok_kasir2_sitoko_secret_token_12345',
        ]);

        // Gudang
        $gudang1 = User::create([
            'role_id' => $roleGudang->id,
            'nim_nik' => 'GDG-001',
            'nama_lengkap' => 'Budi Santoso',
            'email' => 'gudang1@sitoko.com',
            'password' => Hash::make('password'),
            'no_hp' => '082134567803',
            'alamat' => 'Jl. Majapahit No. 88, Semarang',
            'foto' => 'uploads/profil/default.png',
            'api_token' => 'tok_gudang1_sitoko_secret_token_12345',
        ]);

        $gudang2 = User::create([
            'role_id' => $roleGudang->id,
            'nim_nik' => 'GDG-002',
            'nama_lengkap' => 'Rian Hendrawan',
            'email' => 'gudang2@sitoko.com',
            'password' => Hash::make('password'),
            'no_hp' => '082134567804',
            'alamat' => 'Jl. Soekarno Hatta No. 15, Semarang',
            'foto' => 'uploads/profil/default.png',
            'api_token' => 'tok_gudang2_sitoko_secret_token_12345',
        ]);

        // Pelanggan (7 orang pelanggan dummy, total 12 user)
        $pelangganData = [
            ['nim_nik' => 'CUST-001', 'nama' => 'Dewi Sartika', 'email' => 'pelanggan1@sitoko.com', 'hp' => '085711223344', 'token' => 'tok_cust1_sitoko_token'],
            ['nim_nik' => 'CUST-002', 'nama' => 'Andi Pratama', 'email' => 'pelanggan2@sitoko.com', 'hp' => '085711223345', 'token' => 'tok_cust2_sitoko_token'],
            ['nim_nik' => 'CUST-003', 'nama' => 'Citra Lestari', 'email' => 'pelanggan3@sitoko.com', 'hp' => '085711223346', 'token' => 'tok_cust3_sitoko_token'],
            ['nim_nik' => 'CUST-004', 'nama' => 'Dimas Anggara', 'email' => 'pelanggan4@sitoko.com', 'hp' => '085711223347', 'token' => 'tok_cust4_sitoko_token'],
            ['nim_nik' => 'CUST-005', 'nama' => 'Eka Putri Handayani', 'email' => 'pelanggan5@sitoko.com', 'hp' => '085711223348', 'token' => 'tok_cust5_sitoko_token'],
            ['nim_nik' => 'CUST-006', 'nama' => 'Farhan Maulana', 'email' => 'pelanggan6@sitoko.com', 'hp' => '085711223349', 'token' => 'tok_cust6_sitoko_token'],
            ['nim_nik' => 'CUST-007', 'nama' => 'Gita Gutawa Putri', 'email' => 'pelanggan7@sitoko.com', 'hp' => '085711223350', 'token' => 'tok_cust7_sitoko_token'],
        ];

        $pelangganModels = [];
        foreach ($pelangganData as $p) {
            $pelangganModels[] = User::create([
                'role_id' => $rolePelanggan->id,
                'nim_nik' => $p['nim_nik'],
                'nama_lengkap' => $p['nama'],
                'email' => $p['email'],
                'password' => Hash::make('password'),
                'no_hp' => $p['hp'],
                'alamat' => 'Kota Semarang',
                'foto' => 'uploads/profil/default.png',
                'api_token' => $p['token'],
            ]);
        }

        // 3. KATEGORI (12 Kategori Dummy)
        $kategoriList = [
            ['nama' => 'Minuman Kopi & Teh', 'icon' => 'fa-coffee', 'desc' => 'Koleksi kopi robusta, arabika dan aneka teh celup pilihan'],
            ['nama' => 'Sembako & Pangan Pokok', 'icon' => 'fa-wheat-awn', 'desc' => 'Beras premium, gula kristal, minyak goreng higienis'],
            ['nama' => 'Mie & Makanan Instan', 'icon' => 'fa-bowl-rice', 'desc' => 'Aneka mie kuah, mie goreng instan favorit keluarga'],
            ['nama' => 'Susu & Produk Olahan', 'icon' => 'fa-bottle-water', 'desc' => 'Susu UHT sapi segar, kental manis dan keju olahan'],
            ['nama' => 'Sabun & Perawatan Badan', 'icon' => 'fa-soap', 'desc' => 'Sabun mandi antibakteri, shampoo herbal, pasta gigi'],
            ['nama' => 'Kebersihan & Cuci Piring', 'icon' => 'fa-spray-can-sparkles', 'desc' => 'Deterjen konsentrat, pembersih lantai dan sabun cuci'],
            ['nama' => 'Biskuit & Camilan Manis', 'icon' => 'fa-cookie-bite', 'desc' => 'Biskuit gandum, wafer cokelat renyah, kue kering'],
            ['nama' => 'Air Mineral & Minuman Dingin', 'icon' => 'fa-glass-water', 'desc' => 'Air mineral pegunungan, jus kemasan dan soda segar'],
            ['nama' => 'Bumbu Dapur & Saus Sambal', 'icon' => 'fa-pepper-hot', 'desc' => 'Kecap manis kental gurih, saus sambal ekstra pedas'],
            ['nama' => 'Snack Gurih & Keripik', 'icon' => 'fa-bowl-food', 'desc' => 'Keripik kentang renyah, kacang sangrai, kerupuk'],
            ['nama' => 'Obat-obatan & P3K', 'icon' => 'fa-kit-medical', 'desc' => 'Plester luka, minyak kayu putih, vitamin daya tahan tubuh'],
            ['nama' => 'Alat Tulis & Kantor', 'icon' => 'fa-pen-to-square', 'desc' => 'Buku catatan, pulpen gel, spidol, perekat dan map'],
        ];

        $kategoriModels = [];
        foreach ($kategoriList as $k) {
            $kategoriModels[] = Kategori::create([
                'nama_kategori' => $k['nama'],
                'slug' => Str::slug($k['nama']),
                'deskripsi' => $k['desc'],
                'icon' => $k['icon'],
            ]);
        }

        // 4. BARANG (15 Barang Dummy - Minimal 12 baris)
        $barangData = [
            [
                'kategori_id' => $kategoriModels[0]->id,
                'kode_barang' => 'BRG-001',
                'nama_barang' => 'Kopi Robusta Premium 250g',
                'deskripsi' => 'Biji kopi robusta pilihan dari dataran tinggi Temanggung, aroma wangi kuat dan rasa bold mantap.',
                'harga_beli' => 18000,
                'harga_jual' => 25000,
                'stok' => 50,
                'satuan' => 'bungkus',
                'gambar' => 'uploads/barang/kopi.jpg',
            ],
            [
                'kategori_id' => $kategoriModels[0]->id,
                'kode_barang' => 'BRG-002',
                'nama_barang' => 'Teh Melati Wangi Celup 25s',
                'deskripsi' => 'Teh celup daun teh asli berpadu melati alami menyegarkan untuk momen santai pagi dan sore.',
                'harga_beli' => 6500,
                'harga_jual' => 9500,
                'stok' => 60,
                'satuan' => 'kotak',
                'gambar' => 'uploads/barang/teh.jpg',
            ],
            [
                'kategori_id' => $kategoriModels[1]->id,
                'kode_barang' => 'BRG-003',
                'nama_barang' => 'Beras Premium Pulen 5kg',
                'deskripsi' => 'Beras putih murni kualitas super tanpa pemutih dan pengawet, hasil nasi empuk dan pulen.',
                'harga_beli' => 65000,
                'harga_jual' => 74000,
                'stok' => 35,
                'satuan' => 'karung',
                'gambar' => 'uploads/barang/beras.jpg',
            ],
            [
                'kategori_id' => $kategoriModels[1]->id,
                'kode_barang' => 'BRG-004',
                'nama_barang' => 'Minyak Goreng Sawit Higienis 2L',
                'deskripsi' => 'Minyak goreng kelapa sawit dua kali penyaringan, tahan panas tinggi dan masakan lebih garing.',
                'harga_beli' => 28000,
                'harga_jual' => 34000,
                'stok' => 45,
                'satuan' => 'pouch',
                'gambar' => 'uploads/barang/minyak.jpg',
            ],
            [
                'kategori_id' => $kategoriModels[1]->id,
                'kode_barang' => 'BRG-005',
                'nama_barang' => 'Gula Pasir Kristal Putih 1kg',
                'deskripsi' => 'Gula tebu alami kristal putih berkualitas tinggi, manis alami dan mudah larut untuk teh & kue.',
                'harga_beli' => 14500,
                'harga_jual' => 18000,
                'stok' => 55,
                'satuan' => 'kg',
                'gambar' => 'uploads/barang/gula.jpg',
            ],
            [
                'kategori_id' => $kategoriModels[2]->id,
                'kode_barang' => 'BRG-006',
                'nama_barang' => 'Indomie Mi Goreng Spesial 85g',
                'deskripsi' => 'Mi instan goreng favorit nusantara dengan bumbu gurih lengkap bawang goreng renyah.',
                'harga_beli' => 2600,
                'harga_jual' => 3500,
                'stok' => 140,
                'satuan' => 'bungkus',
                'gambar' => 'uploads/barang/indomie.jpg',
            ],
            [
                'kategori_id' => $kategoriModels[3]->id,
                'kode_barang' => 'BRG-007',
                'nama_barang' => 'Susu Sapi UHT Full Cream 1 Liter',
                'deskripsi' => 'Susu cair segar kaya kalsium dan vitamin D untuk pemenuhan gizi seluruh anggota keluarga.',
                'harga_beli' => 16000,
                'harga_jual' => 21000,
                'stok' => 40,
                'satuan' => 'karton',
                'gambar' => 'uploads/barang/susu.jpg',
            ],
            [
                'kategori_id' => $kategoriModels[4]->id,
                'kode_barang' => 'BRG-008',
                'nama_barang' => 'Sabun Mandi Antibakteri 85g',
                'deskripsi' => 'Melindungi kulit dari 99.9% kuman berbahaya dengan keharuman segar sepanjang hari.',
                'harga_beli' => 3500,
                'harga_jual' => 5000,
                'stok' => 90,
                'satuan' => 'batang',
                'gambar' => 'uploads/barang/sabun.jpg',
            ],
            [
                'kategori_id' => $kategoriModels[4]->id,
                'kode_barang' => 'BRG-009',
                'nama_barang' => 'Shampoo Herbal Anti Ketombe 180ml',
                'deskripsi' => 'Formula ekstrak lidah buaya dan habbatus sauda membersihkan kulit kepala dari ketombe membandel.',
                'harga_beli' => 18000,
                'harga_jual' => 24500,
                'stok' => 30,
                'satuan' => 'botol',
                'gambar' => 'uploads/barang/shampoo.jpg',
            ],
            [
                'kategori_id' => $kategoriModels[5]->id,
                'kode_barang' => 'BRG-010',
                'nama_barang' => 'Deterjen Bubuk Konsentrat 800g',
                'deskripsi' => 'Mampu angkat noda membandel 1x kucek, wangi semerbak tahan hingga 48 jam.',
                'harga_beli' => 15000,
                'harga_jual' => 20000,
                'stok' => 38,
                'satuan' => 'bag',
                'gambar' => 'uploads/barang/deterjen.jpg',
            ],
            [
                'kategori_id' => $kategoriModels[6]->id,
                'kode_barang' => 'BRG-011',
                'nama_barang' => 'Biskuit Gandum Crispy 200g',
                'deskripsi' => 'Tinggi serat gandum utuh, renyah dan sehat untuk sarapan cepat penahan lapar.',
                'harga_beli' => 9000,
                'harga_jual' => 13000,
                'stok' => 42,
                'satuan' => 'roll',
                'gambar' => 'uploads/barang/biskuit.jpg',
            ],
            [
                'kategori_id' => $kategoriModels[7]->id,
                'kode_barang' => 'BRG-012',
                'nama_barang' => 'Air Mineral Botol Pegunungan 600ml',
                'deskripsi' => 'Air mineral murni dari sumber mata air alami pegunungan yang terjaga kemurniannya.',
                'harga_beli' => 2500,
                'harga_jual' => 4000,
                'stok' => 120,
                'satuan' => 'botol',
                'gambar' => 'uploads/barang/air_mineral.jpg',
            ],
            [
                'kategori_id' => $kategoriModels[8]->id,
                'kode_barang' => 'BRG-013',
                'nama_barang' => 'Kecap Manis Kental Gurih 520ml',
                'deskripsi' => 'Terbuat dari kedelai hitam Malika pilihan difermentasi tradisional menciptakan cita rasa lezat.',
                'harga_beli' => 16000,
                'harga_jual' => 22000,
                'stok' => 35,
                'satuan' => 'pouch',
                'gambar' => 'uploads/barang/kecap.jpg',
            ],
            [
                'kategori_id' => $kategoriModels[8]->id,
                'kode_barang' => 'BRG-014',
                'nama_barang' => 'Saus Sambal Pedas Ekstra 335ml',
                'deskripsi' => 'Cabai rawit segar dengan paduan rempah tradisional, sensasi pedas mantap menggugah selera.',
                'harga_beli' => 12000,
                'harga_jual' => 16500,
                'stok' => 36,
                'satuan' => 'botol',
                'gambar' => 'uploads/barang/saus.jpg',
            ],
            [
                'kategori_id' => $kategoriModels[9]->id,
                'kode_barang' => 'BRG-015',
                'nama_barang' => 'Keripik Kentang Renyah Balado 75g',
                'deskripsi' => 'Irisan kentang asli tipis renyah dibalut bumbu balado pedas manis gurih nagih.',
                'harga_beli' => 8000,
                'harga_jual' => 11500,
                'stok' => 65,
                'satuan' => 'bungkus',
                'gambar' => 'uploads/barang/snack.jpg',
            ],
        ];

        $barangModels = [];
        foreach ($barangData as $b) {
            $barangModels[] = Barang::create([
                'kategori_id' => $b['kategori_id'],
                'kode_barang' => $b['kode_barang'],
                'nama_barang' => $b['nama_barang'],
                'slug' => Str::slug($b['nama_barang']),
                'deskripsi' => $b['deskripsi'],
                'harga_beli' => $b['harga_beli'],
                'harga_jual' => $b['harga_jual'],
                'stok' => $b['stok'],
                'satuan' => $b['satuan'],
                'gambar' => $b['gambar'],
                'status' => 'aktif',
            ]);
        }

        // 5. TRANSAKSI & DETAIL TRANSAKSI (14 Dummy Transaksi - Minimal 12 baris)
        $metodeList = ['tunai', 'qris', 'va_bca', 'va_mandiri', 'va_bri', 'tunai', 'qris'];

        for ($i = 1; $i <= 14; $i++) {
            $pelanggan = $pelangganModels[($i - 1) % count($pelangganModels)];
            $kasir = ($i % 3 === 0) ? null : ($i % 2 === 0 ? $kasir2 : $kasir1);
            $metode = $metodeList[($i - 1) % count($metodeList)];
            $isPaid = ($i <= 12); // 12 lunas, 2 menunggu pembayaran
            $status = $isPaid ? 'lunas' : 'menunggu';

            $trxDate = now()->subDays(14 - $i)->setTime(8 + ($i % 12), ($i * 7) % 60);

            // Item random 2 sampai 4 item
            $itemsCount = ($i % 3) + 2;
            $selectedIndexes = array_rand($barangModels, $itemsCount);
            if (!is_array($selectedIndexes)) {
                $selectedIndexes = [$selectedIndexes];
            }

            $totalHarga = 0;
            $itemsToInsert = [];

            foreach ($selectedIndexes as $idx) {
                $brg = $barangModels[$idx];
                $qty = ($i % 3) + 1;
                $subtotal = $qty * $brg->harga_jual;
                $totalHarga += $subtotal;

                $itemsToInsert[] = [
                    'barang_id' => $brg->id,
                    'nama_barang' => $brg->nama_barang,
                    'jumlah' => $qty,
                    'harga_satuan' => $brg->harga_jual,
                    'subtotal' => $subtotal,
                ];
            }

            $bayar = $isPaid ? ($metode === 'tunai' ? ceil($totalHarga / 50000) * 50000 : $totalHarga) : 0;
            $kembali = $isPaid ? ($bayar - $totalHarga) : 0;

            $nomorTrx = 'TRX-' . $trxDate->format('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT);
            $nomorVa = Str::startsWith($metode, 'va_') ? '88002' . str_pad($i, 7, '0', STR_PAD_LEFT) : null;
            $qrisPayload = ($metode === 'qris') ? '00020101021226600016ID.CO.SITOKO.WWW011893600000002' . $i . '5204581253033605802ID' : null;

            $trx = Transaksi::create([
                'nomor_transaksi' => $nomorTrx,
                'user_pelanggan_id' => $pelanggan->id,
                'user_kasir_id' => $kasir ? $kasir->id : null,
                'nama_pelanggan' => $pelanggan->nama_lengkap,
                'tanggal_transaksi' => $trxDate,
                'total_harga' => $totalHarga,
                'total_bayar' => $bayar,
                'kembalian' => $kembali,
                'metode_pembayaran' => $metode,
                'nomor_va' => $nomorVa,
                'qris_payload' => $qrisPayload,
                'status_pembayaran' => $status,
                'catatan' => 'Pembelian dummy seeder #' . $i,
            ]);

            foreach ($itemsToInsert as $item) {
                $trx->detail()->create($item);
            }
        }
    }
}
