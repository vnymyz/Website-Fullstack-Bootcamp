# Panduan Ujian Project — ERP Perusahaan Dagang

Panduan step-by-step membuat project Laravel dari nol dengan tema **ERP Perusahaan Dagang**. Project ini menerapkan pola dan konsep yang sama dari `README.md` dan `CATATAN-RINGKASAN.md`, tetapi dalam alur bisnis nyata: membeli barang, menerima stok, menjual barang, mengirim sebagian pesanan, mencetak invoice, dan mencatat keuangan sederhana.

**Tech stack:** Laravel + PHP + MySQL + Blade + Tailwind CSS + Laravel Breeze + Sanctum + PHPUnit.

**Tujuan akhir:** aplikasi internal untuk perusahaan dagang. Sistem harus aman, dapat dipakai beberapa role, dan data transaksi harus saling memengaruhi dengan benar.

> 💡 Mockup HTML statis tersedia di folder [`mockup/`](./mockup/). Buka `mockup/index.html` memakai Live Server sebelum coding. Panduannya ada di [`mockup/README.md`](./mockup/README.md).

## Daftar Isi

- [Soal Ujian Teori Laravel (20 Soal)](#soal-ujian-teori-laravel-20-soal)
- [0. Setup Laravel](#0-setup-laravel)
- [1. Struktur Folder Project](#1-struktur-folder-project)
- [2. Aturan Bisnis & Struktur Database](#2-aturan-bisnis--struktur-database)
- [3. Model dan Migration Master Data](#3-model-dan-migration-master-data)
- [4. Auth, Role, dan Permission](#4-auth-role-dan-permission)
- [5. Pembelian & Penerimaan Barang](#5-pembelian--penerimaan-barang)
- [6. Penjualan, PO, dan Pengiriman Parsial](#6-penjualan-po-dan-pengiriman-parsial)
- [7. Invoice, Retur, Piutang, dan Hutang](#7-invoice-retur-piutang-dan-hutang)
- [8. Stok, Kas/Bank, dan Pengeluaran](#8-stok-kasbank-dan-pengeluaran)
- [9. Komisi Sales, Target, dan Asset Management](#9-komisi-sales-target-dan-asset-management)
- [10. Laporan & Halaman Print](#10-laporan--halaman-print)
- [11. Search, Pagination, Form Request, dan Policy](#11-search-pagination-form-request-dan-policy)
- [12. Seeder & Factory](#12-seeder--factory)
- [13. API Resource + Sanctum](#13-api-resource--sanctum)
- [14. Testing (PHPUnit)](#14-testing-phpunit)
- [Checklist Sebelum Submit](#checklist-sebelum-submit)
- [Fitur Bonus](#fitur-bonus)
- [Catatan Deploy](#catatan-deploy)

---

## Soal Ujian Teori Laravel (20 Soal)

Jawab dengan kalimat sendiri. Referensi: `README.md` dan `CATATAN-RINGKASAN.md`.

1. Jelaskan alur MVC pada proses menyimpan Sales Order.
2. Apa beda route statis `/sales/create` dan route dinamis `/sales/{sale}`? Mengapa urutannya penting?
3. Jelaskan fungsi `@extends`, `@section`, dan `@yield` pada layout dashboard ERP.
4. Mengapa perhitungan total PO dan perubahan stok tidak boleh ditulis di Blade?
5. Apa fungsi `$fillable`? Mengapa `role` dan `status` transaksi harus dijaga dari mass assignment sembarangan?
6. Apa fungsi migration dan foreign key pada tabel `sales` serta `sale_items`?
7. Jelaskan `belongsTo`, `hasMany`, dan contoh relasinya pada Purchase dan PurchaseItem.
8. Apa beda authentication dan authorization? Beri contoh pada ERP.
9. Apa fungsi middleware? Bagaimana middleware role melindungi menu Asset Management?
10. Mengapa menyembunyikan tombol Delete di Blade tidak cukup untuk keamanan?
11. Apa itu route model binding? Kapan memakai `{sale}` dan `{sale:number}`?
12. Mengapa transaksi pengiriman parsial perlu database transaction (`DB::transaction()`)?
13. Jelaskan cara kerja search dengan `when()` dan alasan memakai `GET`.
14. Apa manfaat pagination dan `withQueryString()` pada daftar penjualan?
15. Apa keuntungan Form Request dibanding validasi di Controller?
16. Apa fungsi Policy? Beri contoh Policy untuk membatasi pembatalan transaksi.
17. Apa perbedaan web route dan API route?
18. Jelaskan alur Sanctum dari login API sampai request yang memakai Bearer token.
19. Mengapa test memakai `RefreshDatabase` dan SQLite in-memory?
20. Apa beda Factory dan Seeder? Mengapa transaksi dummy perlu customer, supplier, dan product lebih dulu?

---

## 0. Setup Laravel

```bash
cd D:/laragon/www
composer create-project laravel/laravel erp-perusahaan-dagang
cd erp-perusahaan-dagang
copy .env.example .env
php artisan key:generate
```

Buat database `erp_perusahaan_dagang` melalui phpMyAdmin, lalu atur `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=erp_perusahaan_dagang
DB_USERNAME=root
DB_PASSWORD=
```

```bash
composer require laravel/breeze --dev
php artisan breeze:install
npm install
npm run dev
php artisan migrate
php artisan serve
```

Pilih Breeze **Blade with Alpine** dan PHPUnit saat installer bertanya.

---

## 1. Struktur Folder Project

```text
erp-perusahaan-dagang/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── PurchaseController.php
│   │   │   ├── SaleController.php
│   │   │   ├── DeliveryOrderController.php
│   │   │   ├── AssetController.php
│   │   │   ├── Admin/UserController.php
│   │   │   └── Api/ProductController.php
│   │   ├── Middleware/EnsureUserHasRole.php
│   │   ├── Requests/StoreSaleRequest.php
│   │   └── Resources/ProductResource.php
│   ├── Models/ (Product, Contact, Purchase, Sale, Asset, ...)
│   └── Policies/SalePolicy.php
├── database/
│   ├── migrations/
│   ├── factories/
│   └── seeders/
├── resources/views/
│   ├── layouts/app.blade.php
│   ├── purchases/
│   ├── sales/
│   ├── assets/
│   ├── reports/
│   └── print/
├── routes/web.php
├── routes/api.php
└── tests/Feature/
```

Gunakan Controller untuk menerima request dan menyiapkan response, Model untuk query/relasi, Form Request untuk validasi, Policy untuk aturan izin, serta Blade hanya untuk tampilan.

---

## 2. Aturan Bisnis & Struktur Database

### Aturan wajib

- Pembelian dibuat lebih dulu, lalu barang diterima. **Stok bertambah hanya saat penerimaan barang dikonfirmasi.**
- Ongkir pembelian dapat dicatat `unpaid` setelah barang diterima.
- Quotation dapat dikonversi menjadi Sales Order/PO.
- **Stok tidak berubah saat PO dibuat.** Stok berkurang saat Delivery Order dikonfirmasi.
- Satu PO boleh memiliki banyak Delivery Order. Total terkirim tidak boleh melebihi jumlah PO.
- Invoice dan Delivery Order dapat dicetak dari PO.
- Retur pembelian/penjualan wajib mengubah stok dan nilai hutang/piutang secara sesuai.
- Gunakan status praktis: `draft`, `open`, `partial`, `completed`, `cancelled`, dan `paid` bila relevan.
- Aplikasi hanya mencatat saldo operasional, piutang, dan hutang; **tidak wajib** membuat jurnal akuntansi double-entry.

### Tabel inti

| Tabel                  | Kolom inti                                                                                          | Keterangan                                         |
| ---------------------- | --------------------------------------------------------------------------------------------------- | -------------------------------------------------- |
| `users`                | name, email, password, role, sales_target                                                           | role: admin, purchasing, sales, finance, warehouse |
| `contacts`             | type, name, phone, email, address                                                                   | type: customer atau supplier                       |
| `products`             | sku, name, unit, purchase_price, selling_price, stock, minimum_stock                                | master barang                                      |
| `purchases`            | number, supplier_id, date, status, shipping_cost, shipping_status, total                            | header pembelian                                   |
| `purchase_items`       | purchase_id, product_id, quantity, price, subtotal, received_quantity                               | detail pembelian                                   |
| `sales`                | number, customer_id, sales_user_id, date, status, discount_type, discount_value, total, paid_amount | quotation/PO memakai `document_type`               |
| `sale_items`           | sale_id, product_id, quantity, price, discount, subtotal                                            | detail PO                                          |
| `delivery_orders`      | sale_id, number, date, status                                                                       | header pengiriman                                  |
| `delivery_order_items` | delivery_order_id, sale_item_id, quantity                                                           | detail pengiriman parsial                          |
| `returns`              | type, reference_type, reference_id, date, status, total                                             | retur beli/jual                                    |
| `bank_accounts`        | name, account_number, opening_balance                                                               | wajib dibuat 4 akun                                |
| `cash_expenses`        | bank_account_id, date, category, description, amount                                                | pengeluaran kas/bank                               |
| `assets`               | code, name, acquired_at, acquisition_cost, residual_value, useful_life_months                       | aset tetap                                         |

Gunakan foreign key untuk relasi. Nominal gunakan `decimal(15,2)`, kuantitas gunakan `decimal(15,2)`, dan jangan memakai `float` untuk uang.

---

## 3. Model dan Migration Master Data

Mulai dari user role, contact, dan product:

```bash
php artisan make:model Contact -m
php artisan make:model Product -m
php artisan make:migration add_role_and_sales_target_to_users_table --table=users
```

Contoh migration product:

```php
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('sku')->unique();
    $table->string('name');
    $table->string('unit')->default('pcs');
    $table->decimal('purchase_price', 15, 2)->default(0);
    $table->decimal('selling_price', 15, 2)->default(0);
    $table->decimal('stock', 15, 2)->default(0);
    $table->decimal('minimum_stock', 15, 2)->default(0);
    $table->timestamps();
});
```

`Contact` memiliki scope sederhana agar query mudah dibaca:

```php
public function scopeCustomers($query)
{
    return $query->where('type', 'customer');
}
```

```bash
php artisan migrate
```

---

## 4. Auth, Role, dan Permission

Tambahkan kolom `role` dengan default `sales`. Admin boleh mengelola user dan seluruh modul; role lain hanya membuka modul yang diberikan.

```bash
php artisan make:middleware EnsureUserHasRole
```

Middleware menerima role yang diperbolehkan dan mengembalikan 403 bila user tidak sesuai. Daftarkan alias `role` di `bootstrap/app.php`, lalu gunakan:

```php
Route::middleware(['auth', 'role:admin,finance'])->group(function () {
    Route::resource('assets', AssetController::class);
});
```

Jangan mengandalkan sidebar saja. Route, Controller/Policy, dan Form Request juga harus memeriksa izin.

---

## 5. Pembelian & Penerimaan Barang

```bash
php artisan make:model Purchase -m
php artisan make:model PurchaseItem -m
php artisan make:controller PurchaseController --resource
php artisan make:controller GoodsReceiptController
```

Alur:

1. Purchasing memilih supplier dan menambahkan product, quantity, serta harga ke Purchase.
2. Simpan total dari seluruh item. Status awal `open`.
3. Warehouse menerima barang melalui Goods Receipt. Validasi jumlah diterima tidak boleh melampaui jumlah beli.
4. Dalam `DB::transaction()`, tambah `received_quantity`, tambah `products.stock`, dan ubah status menjadi `partial` atau `completed`.
5. Jika ongkir belum dibayar, simpan `shipping_cost` dan `shipping_status = unpaid`.
6. List hutang menampilkan total purchase dikurangi pembayaran supplier bila fitur pembayaran supplier dibuat.

Relasi inti:

```php
// Purchase.php
public function supplier() { return $this->belongsTo(Contact::class, 'supplier_id'); }
public function items() { return $this->hasMany(PurchaseItem::class); }

// PurchaseItem.php
public function product() { return $this->belongsTo(Product::class); }
```

---

## 6. Penjualan, PO, dan Pengiriman Parsial

```bash
php artisan make:model Sale -m
php artisan make:model SaleItem -m
php artisan make:model DeliveryOrder -m
php artisan make:model DeliveryOrderItem -m
php artisan make:controller SaleController --resource
php artisan make:controller DeliveryOrderController
```

`Sales` menyimpan `document_type` bernilai `quotation` atau `order`. Quotation belum mengubah stok; ketika disetujui, buat/konversi menjadi order.

Saat membuat Delivery Order:

```php
DB::transaction(function () use ($sale, $validated) {
    // validasi qty baru <= qty PO - qty yang sudah dikirim
    // buat delivery order dan itemnya
    // kurangi stock setiap product
    // status PO: partial atau completed
});
```

Tolak transaksi jika stok tidak cukup atau jumlah kirim melebihi sisa quantity PO. Nomor dokumen harus unik, misalnya `SO-2026-0001`, `DO-2026-0001`, dan `INV-2026-0001`.

Komisi dikonfigurasi per sales: `fixed_per_item` atau `percentage_of_sale`. Hitung komisi setelah order/delivery memenuhi aturan target yang disimpan pada user.

---

## 7. Invoice, Retur, Piutang, dan Hutang

Invoice dapat memakai data Sale yang sama: tampilkan nomor, customer, item, diskon, total, paid amount, dan outstanding balance.

```php
$outstanding = max(0, $sale->total - $sale->paid_amount);
```

Buat route halaman print terpisah seperti `/sales/{sale}/invoice/print` dan `/delivery-orders/{deliveryOrder}/print`. View print harus memakai CSS `@media print` agar sidebar/tombol tidak ikut tercetak.

Retur:

- Retur penjualan: barang masuk kembali ke stok, nilai retur mengurangi piutang penjualan.
- Retur pembelian: barang keluar dari stok, nilai retur mengurangi hutang pembelian.
- Return tidak boleh diproses melebihi barang yang benar-benar sudah diterima/dikirim.

---

## 8. Stok, Kas/Bank, dan Pengeluaran

Halaman stok menampilkan SKU, nama, current stock, minimum stock, dan label `Stok Menipis` jika current stock di bawah minimum.

```bash
php artisan make:model BankAccount -m
php artisan make:model CashExpense -m
php artisan make:controller CashExpenseController --resource
```

Seeder wajib membuat empat akun: Kas Kecil, Bank BCA, Bank Mandiri, dan Bank BRI. Pengeluaran memilih satu akun, kategori, tanggal, deskripsi, dan nominal. Saldo tampilan:

```php
$balance = $bankAccount->opening_balance - $bankAccount->cashExpenses()->sum('amount');
```

Pengeluaran tidak boleh memakai nominal nol/negatif atau saldo yang tidak cukup.

---

## 9. Komisi Sales, Target, dan Asset Management

Asset Management adalah modul advanced **wajib**.

```bash
php artisan make:model Asset -m
php artisan make:controller AssetController --resource
```

Pakai metode garis lurus:

```php
$monthlyDepreciation = ($asset->acquisition_cost - $asset->residual_value)
    / $asset->useful_life_months;
```

Tampilkan depresiasi per bulan, akumulasi depresiasi sampai bulan laporan, dan nilai buku. Validasi: nilai residu tidak boleh melebihi harga perolehan dan masa manfaat minimal satu bulan.

---

## 10. Laporan & Halaman Print

Buat `ReportController` dengan filter `start_date` dan `end_date`. Minimal sediakan:

- laporan penjualan periode custom;
- daftar pembelian dan pembelian belum diterima;
- stok dan stok menipis;
- piutang customer dan hutang supplier;
- pengeluaran per bank account;
- ringkasan sederhana untuk perpajakan: total penjualan, pembelian, pengeluaran, serta estimasi laba kotor;
- daftar aset dan depresiasi periode.

Gunakan form `GET`, `when()`, dan `withQueryString()` agar filter tetap ada ketika pindah halaman.

---

## 11. Search, Pagination, Form Request, dan Policy

Gunakan pagination pada halaman product, contact, purchase, sales, asset, dan expense.

```php
$sales = Sale::query()
    ->when($request->search, fn ($query, $search) =>
        $query->where('number', 'like', "%{$search}%"))
    ->latest()
    ->paginate(10)
    ->withQueryString();
```

Buat Form Request minimal untuk store/update product, purchase, sale, delivery order, dan asset. Rules item array harus memvalidasi product tersedia, quantity lebih dari nol, serta price tidak negatif.

Buat `SalePolicy` untuk memastikan hanya role berwenang yang dapat mengubah/membatalkan Sale. Policy harus dipakai di Controller, Form Request, dan Blade dengan `@can`.

---

## 12. Seeder & Factory

```bash
php artisan make:factory ProductFactory --model=Product
php artisan make:factory ContactFactory --model=Contact
php artisan make:seeder MasterDataSeeder
php artisan make:seeder TransactionSeeder
```

`MasterDataSeeder` membuat admin, user sales/purchasing/finance/warehouse, minimal 10 product, customer, supplier, serta empat bank account. `TransactionSeeder` membuat contoh pembelian, PO parsial, delivery order, expense, dan asset.

```bash
php artisan migrate:fresh --seed
```

Pastikan data relasi dibuat berurutan: user/contact/product lebih dahulu, kemudian purchase/sale, lalu detail dan delivery order.

---

## 13. API Resource + Sanctum

```bash
php artisan install:api
php artisan make:resource ProductResource
php artisan make:controller Api/ProductController --api
```

Sediakan endpoint terautentikasi:

```php
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/stock-summary', [ProductController::class, 'stockSummary']);
});
```

`ProductResource` minimal mengembalikan `id`, `sku`, `name`, `unit`, `stock`, dan status stok menipis. Uji melalui Thunder Client menggunakan header `Authorization: Bearer <token>`.

---

## 14. Testing (PHPUnit)

```bash
php artisan make:test ErpWorkflowTest
php artisan test
```

Minimal test berikut:

```php
public function test_guest_tidak_bisa_membuka_sales(): void
{
    $this->get('/sales')->assertRedirect('/login');
}

public function test_delivery_order_mengurangi_stok(): void
{
    // buat PO dan product stock 10
    // buat delivery quantity 3
    // assert stock menjadi 7
}
```

Tambahkan test 403 untuk role tanpa izin, pengiriman parsial melebihi PO, retur yang memperbarui stok, dan perhitungan depresiasi aset.

---

## Checklist Sebelum Submit

- [ ] `php artisan migrate:fresh --seed` selesai tanpa error.
- [ ] Admin, purchasing, warehouse, sales, dan finance bisa login sesuai role.
- [ ] Customer, supplier, product, dan stock bisa dikelola.
- [ ] Barang pembelian menambah stok hanya setelah diterima.
- [ ] PO tidak langsung mengurangi stok.
- [ ] Satu PO bisa dikirim sebagian melalui lebih dari satu Delivery Order.
- [ ] Invoice dan Delivery Order bisa dibuka dalam tampilan print.
- [ ] Retur memperbarui stok serta piutang/hutang.
- [ ] Ada empat akun kas/bank dan expense terhubung ke akun yang dipilih.
- [ ] Laporan periode custom, piutang, hutang, stok, dan aset tersedia.
- [ ] Depresiasi/amortisasi asset berjalan dengan metode garis lurus.
- [ ] API product dan stock summary memakai Sanctum.
- [ ] `php artisan test` semua PASS.
- [ ] `.env` tidak ikut di-commit.

## Fitur Bonus

Setelah semua checklist wajib selesai, siswa boleh menambah integrasi payment gateway **sandbox** untuk pembayaran invoice. Payment notification/webhook harus memvalidasi signature dari provider dan hanya boleh mengubah status pembayaran setelah notifikasi valid. HRM/payroll, export Excel/PDF, audit log, dan notifikasi email/WhatsApp juga boleh menjadi bonus.

## Catatan Deploy

- Set `APP_DEBUG=false` di produksi.
- Jangan pernah menjalankan `migrate:fresh` pada database produksi.
- Gunakan kredensial payment gateway sandbox saat demo; jangan taruh API key di repository.
- Pastikan semua role diuji dengan akun berbeda sebelum project dipresentasikan.
