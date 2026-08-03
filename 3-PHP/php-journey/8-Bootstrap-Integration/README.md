# Sesi 8 — Bootstrap Integration

Tujuan: ganti CSS custom yang udah dipakai dari Sesi 5-7 pake **Bootstrap** — framework CSS siap pakai. Fitur/logic-nya sama persis kayak Sesi 7 (auth, role, CRUD buku, favorit), yang beda cuma tampilannya.

**Belum ada perubahan database** — masih `toko_belajar`, table yang sama kayak Sesi 7 (`users`, `buku`, `favorit`). Gak ada `setup.sql` baru di sesi ini.

## Apa Itu Bootstrap

Bootstrap adalah **framework CSS** (plus sedikit JavaScript) — kumpulan class siap pakai buat styling HTML, dibikin sama tim Twitter tahun 2011, sekarang open-source dan dipelihara komunitas. Bedanya sama nulis CSS manual (`style.css` di Sesi 5-7):

| | CSS Manual (Sesi 5-7) | Bootstrap (Sesi 8) |
|---|---|---|
| Cara pakai | Tulis `.btn-like { ... }` sendiri di `style.css`, baru pasang `class="btn-like"` | Tinggal pasang class yang UDAH ADA, misal `class="btn btn-danger"` |
| Konsisten? | Manual jaga sendiri (warna, ukuran, spacing) — gampang beda-beda tiap halaman | Semua komponen udah didesain nyambung satu sama lain |
| Responsive (HP vs desktop) | Nulis `@media` query manual | Otomatis lewat class kayak `col-md-4`, `d-none d-lg-flex` |
| Bug CSS "bocor" (kayak kasus Sesi 7) | Rawan, soalnya semua rule nyampur di 1 file besar | Jarang kejadian, tiap komponen namespace sendiri (`.btn`, `.card`, dst) |
| JS interaktif (modal, dropdown, collapse) | Nulis `addEventListener` sendiri | Tinggal pasang atribut `data-bs-*`, JS-nya udah dihandle Bootstrap |

Intinya: Bootstrap gak ngerubah APA yang dikerjain (masih HTML+CSS+kadang JS), cuma ngasih "kosakata" class siap pakai biar gak nulis dari nol tiap kali butuh tombol, kartu, tabel, dst.

**CDN vs lokal:**
- **CDN** (dipakai di sesi ini) — tinggal `<link>` ke file CSS/JS yang di-host di server lain (jsDelivr), gak perlu download/install apa-apa. Kekurangannya: butuh internet, dan situs lain (jsDelivr) ikut nentuin apakah Bootstrap-nya kepake atau enggak.
- **Lokal** — download file Bootstrap, taro di folder project, link ke situ. Kerja offline, tapi ukuran project jadi lebih besar.

Buat belajar, CDN lebih praktis. Ada 3 file CDN yang dipakai di project ini, semua dari jsDelivr:
```html
<!-- 1. CSS inti Bootstrap -- WAJIB, taro di <head> -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- 2. Paket ikon Bootstrap Icons -- terpisah dari Bootstrap inti, CDN sendiri -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

<!-- 3. JS Bootstrap -- WAJIB buat komponen interaktif (navbar collapse, modal),
     taro SEBELUM </body> (bukan di <head>), soalnya butuh HTML-nya udah kebentuk dulu -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
```

## Cara Menjalankan

1. Laragon — Start All (butuh internet buat narik Bootstrap dari CDN).
2. Database `toko_belajar` udah harus ada isinya dari Sesi 7 (`users`, `buku`, `favorit`). Kalau belum, jalanin `7-Buku-dan-Favorite/setup.sql` dulu.
3. Buka browser:
   ```
   http://localhost/php-journey/8-Bootstrap-Integration/index.php
   ```

## Semua Komponen Bootstrap yang Dipakai di Project Ini

| Komponen | Class Kunci | Dipake di | Fungsinya |
|---|---|---|---|
| **Grid System** | `container`, `row`, `col-12`, `col-md-4`, `col-lg-6` | Hampir semua halaman | Bikin layout kolom yang otomatis nyesuain lebar layar (responsive) tanpa `@media` manual |
| **Navbar** | `navbar`, `navbar-expand-lg`, `navbar-toggler`, `collapse` | `includes/navbar.php` | Menu atas, otomatis jadi hamburger icon di layar HP |
| **Sidebar (nav-pills)** | `nav`, `nav-pills`, `nav-link`, `active` | `includes/admin-sidebar.php`, `includes/user-sidebar.php` | Menu vertikal, `active` nandain menu yang lagi dibuka |
| **Card** | `card`, `card-body`, `card-img-top`, `card-title` | Kartu buku, form login/register, stat dashboard | Kotak konten dengan border+shadow siap pakai |
| **Table** | `table`, `table-hover`, `table-dark`, `table-responsive` | `admin/buku.php`, `admin/users.php` | Tabel data, `table-responsive` bikin bisa di-scroll horizontal di HP |
| **Button** | `btn`, `btn-dark`, `btn-outline-danger`, `btn-sm` | Semua tombol di semua halaman | Tombol dengan warna/ukuran konsisten |
| **Badge** | `badge`, `text-bg-primary`, `text-bg-danger` | Role user, status stok buku | Label kecil buat status/kategori |
| **Alert** | `alert`, `alert-danger`, `alert-success`, `alert-warning` | Flash message & error validasi form | Kotak pesan berwarna (sukses/error/warning) |
| **Modal** | `modal`, `data-bs-toggle="modal"`, `data-bs-target` | Konfirmasi hapus di `admin/buku.php` & `admin/users.php` | Popup di tengah layar, gantiin `confirm()` JS bawaan browser |
| **Dropdown** | `dropdown`, `dropdown-toggle`, `dropdown-menu`, `dropdown-item` | Navbar publik (`includes/navbar.php`) pas user udah login | Menu username yang buka/tutup pas diklik, isinya link Dashboard & Logout |
| **Pagination** | `pagination`, `page-item`, `page-link`, `disabled` | `katalog.php`, `admin/buku.php`, `admin/users.php` | Navigasi halaman data |
| **Forms** | `form-control`, `form-select`, `form-label`, `form-text`, `input-group` | Semua form (login, register, tambah/edit buku, profil) | Styling input/select/textarea konsisten |
| **Utility classes** | `d-flex`, `gap-2`, `shadow-sm`, `rounded-circle`, `text-muted`, `fw-bold` | Di mana-mana | Class kecil "sekali pakai" (flexbox, jarak, warna teks, dst) buat rapiin detail tanpa nulis CSS custom |

## Contoh Kodingan Lengkap per Komponen (dikomentarin bagian Bootstrap-nya)

### 1. Navbar — `includes/navbar.php`
```php
<!-- navbar-expand-lg = navbar full di layar >=992px, collapse jadi hamburger di bawah itu -->
<!-- navbar-dark = teks/ikon di dalem navbar otomatis jadi putih (cocok buat background gelap) -->
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color:#4a2c1d;">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">Toko Buku</a>

        <!-- navbar-toggler = tombol hamburger, cuma keliatan di layar kecil -->
        <!-- data-bs-toggle & data-bs-target = atribut yang "dibaca" Bootstrap JS,
             nyambungin tombol ini ke menu yang mau dibuka/tutup (id="navbarMenu") -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- collapse navbar-collapse = wrapper menu yang disembunyiin/dimunculin
             pas tombol hamburger diklik (JS-nya otomatis, gak perlu ditulis manual) -->
        <div class="collapse navbar-collapse" id="navbarMenu">
            <ul class="navbar-nav mx-auto"> <!-- mx-auto = nge-center menu ini -->
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
```

### 2. Grid + Card — daftar buku di `katalog.php`
```php
<!-- row + col-* = grid system. g-4 = jarak (gap) antar kolom.
     col-12 (HP, 1 kolom penuh) col-sm-6 (2 kolom) col-md-4 (3 kolom di layar medium+) -->
<div class="row g-4">
    <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <div class="col-12 col-sm-6 col-md-4">
            <!-- card = kotak konten. h-100 = tinggi penuh ngikutin card tertinggi
                 di baris yang sama, biar card sejajar rapi -->
            <div class="card h-100">
                <img src="..." class="card-img-top" alt="...">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($row['judul']) ?></h5>
                    <p class="card-text text-muted small">...</p>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
</div>
```

### 3. Modal — konfirmasi hapus di `admin/buku.php`
```php
<!-- Tombol ini gak langsung ngirim form -- data-bs-toggle="modal" bikin dia
     cuma "buka" popup modal (id-nya nunjuk ke hapusModal<id> di bawah).
     Form hapus BENERAN ada DI DALEM modal, submit-nya baru kejadian
     kalau admin klik "Ya, Hapus" di dalem popup. -->
<button type="button" class="btn btn-sm btn-outline-danger"
        data-bs-toggle="modal" data-bs-target="#hapusModal<?= $id ?>">
    <i class="bi bi-trash"></i>
</button>

<!-- modal fade = struktur wajib buat komponen modal + efek fade in/out.
     tabindex="-1" = biar gak ke-fokus keyboard pas modal ketutup -->
<div class="modal fade" id="hapusModal<?= $id ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <!-- btn-close = tombol X bawaan Bootstrap, data-bs-dismiss="modal" nutup modal -->
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">Yakin mau hapus buku ini?</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="buku.php" method="POST">
                    <input type="hidden" name="action" value="hapus">
                    <input type="hidden" name="id" value="<?= $id ?>">
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
```
`data-bs-toggle` dan `data-bs-target` itu atribut HTML yang "dibaca" sama Bootstrap JS — gak perlu nulis JavaScript sendiri buat buka/tutup modalnya. Tiap baris buku/user dapet modal sendiri (`id="hapusModal<id>"`) biar gak ketuker.

### 4. Table + Badge + Pagination — `admin/buku.php`
```php
<!-- table-responsive = bikin table bisa di-scroll horizontal di layar sempit,
     daripada kolom-kolomnya kegencet -->
<div class="table-responsive">
    <!-- table-hover = baris nge-highlight pas di-hover mouse -->
    <table class="table table-hover bg-white align-middle shadow-sm">
        <thead class="table-dark"> <!-- table-dark = header hitam -->
            <tr><th>No</th><th>Judul</th><th>Stok</th></tr>
        </thead>
        <tbody>
            <tr>
                <td><?= $nomor++ ?>.</td>
                <td><?= htmlspecialchars($row['judul']) ?></td>
                <td>
                    <!-- badge = label kecil. text-bg-warning/text-bg-danger nentuin warnanya -->
                    <?php if ((int) $row['stok'] === 0): ?>
                        <span class="badge text-bg-danger">Habis</span>
                    <?php else: ?>
                        <span class="badge text-bg-light border"><?= $row['stok'] ?></span>
                    <?php endif; ?>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!-- pagination = navigasi halaman. page-item.active nandain halaman yang lagi dibuka,
     page-item.disabled buat tombol "sebelumnya/selanjutnya" pas udah di ujung -->
<nav>
    <ul class="pagination justify-content-center">
        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
            <a class="page-link" href="?page=<?= $page - 1 ?>">&laquo;</a>
        </li>
        <?php for ($i = 1; $i <= $totalPage; $i++): ?>
            <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>
```

### 5. Form + Input Group — `login.php`
```php
<!-- input-group = nempelin elemen tambahan (ikon) ke pinggir input,
     jadi 1 kotak visual nyambung -->
<div class="mb-3"> <!-- mb-3 = margin-bottom, spacing antar field -->
    <label class="form-label">Username</label>
    <div class="input-group">
        <span class="input-group-text bg-white"><i class="bi bi-person"></i></span>
        <!-- form-control = styling standar buat input text -->
        <input type="text" name="username" class="form-control" placeholder="Username kamu">
    </div>
</div>

<!-- btn-dark + w-100 (lebar penuh) + py-2 (padding atas-bawah lebih) -->
<button type="submit" class="btn btn-dark w-100 py-2 d-flex align-items-center justify-content-center gap-2">
    <i class="bi bi-box-arrow-in-right"></i> Login
</button>
```

### 6. Utility Classes — contoh dari stat card `admin/dashboard.php`
```php
<!-- Ini yang disebut "utility-first": tiap class ngerjain 1 hal doang,
     digabung-gabung buat bikin komponen custom TANPA nulis CSS sendiri. -->
<div class="card shadow-sm border-0 h-100">
    <!--        ^shadow tipis  ^tanpa border  ^tinggi penuh -->
    <div class="card-body d-flex align-items-center gap-3">
        <!--              ^flexbox  ^rata tengah vertikal  ^jarak antar item -->
        <div class="rounded-circle d-flex align-items-center justify-content-center"
             style="width:56px; height:56px; background-color:#e7f1ff;">
            <!-- rounded-circle = border-radius 50%, bikin div persegi jadi lingkaran -->
            <i class="bi bi-people fs-4 text-primary"></i>
            <!--                    ^ukuran font  ^warna biru bawaan Bootstrap -->
        </div>
        <div>
            <div class="fs-3 fw-bold"><?= $totalUsers ?></div>
            <div class="text-muted small">Total Users</div>
        </div>
    </div>
</div>
```

## Kenapa `includes/navbar.php` Sekarang Jadi Partial

Di Sesi 7, navbar ditulis manual di tiap halaman (`index.php`, `katalog.php`, `about.php`) — waktu itu keputusannya emang dibiarin inline karena baru dipake beberapa halaman, motong jadi partial dianggap prematur.

Sekarang navbar dipake di beberapa halaman publik (`index.php`, `katalog.php`, `about.php`), jadi masuk akal dipisah jadi `includes/navbar.php`. Halaman yang include-nya kirim variabel `$activeMenu` dulu:
```php
$activeMenu = 'home'; // atau 'buku' / 'about'
require "includes/navbar.php";
```
Halaman yang butuh login (dashboard, favorit, profil) pakai `includes/user-sidebar.php` sebagai gantinya — lihat section "Update: Dashboard User Sekarang Pakai Sidebar" di bawah.

## Update: Navbar Pake Dropdown (Bukan Tombol Dashboard/Logout Terpisah)

Sebelumnya, pas udah login, navbar publik (`index.php`/`katalog.php`/`about.php`) nampilin teks "Halo, username" + 2 tombol terpisah (Dashboard, Logout) sejajar. Sekarang diganti komponen **dropdown** Bootstrap — username + ikon jadi 1 tombol, diklik baru muncul menu Dashboard & Logout.

### Cara nambahin dropdown Bootstrap, step by step

**1. Struktur dasar dropdown** (dari dokumentasi Bootstrap, komponen `dropdown`):
```html
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        Dropdown link
    </a>
    <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="#">Action</a></li>
    </ul>
</li>
```
- `dropdown-toggle` di link/tombol — otomatis nambahin panah kecil ke bawah (▾).
- `data-bs-toggle="dropdown"` — atribut yang bikin Bootstrap JS tau ini pemicu dropdown, gak perlu `addEventListener` sendiri.
- `dropdown-menu` — wrapper isi menu, ke-sembunyiin/ke-munculin otomatis pas toggle diklik.
- `dropdown-item` — style 1 baris menu di dalemnya.

**2. Sesuain sama kebutuhan project** — ganti teks "Dropdown link" jadi "Halo, {username}", tambahin ikon orang, isi menu jadi Dashboard + Logout:
```php
<li class="nav-item dropdown">
    <!-- Sapaan "Halo, ..." ada di TOMBOL-nya sendiri (yang keliatan
         di navbar), bukan disembunyiin di dalem menu dropdown-nya.
         gap-2 = jarak ikon ke teks, jangan gap-1 (kegencet, mepet). -->
    <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-person-circle fs-5"></i>
        Halo, <?= htmlspecialchars($_SESSION['username']) ?>
    </a>
    <!-- dropdown-menu-end = nempel ke kanan (bukan kiri), biar gak kepotong
         layar kalau tombolnya di ujung kanan navbar -->
    <ul class="dropdown-menu dropdown-menu-end">
        <li>
            <a class="dropdown-item" href="dashboard.php">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li>
            <a class="dropdown-item text-danger" href="logout.php">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </li>
    </ul>
</li>
```

**3. Pola login/belum-login-nya TETEP sama** kayak sebelumnya (`if/else` cek `$sudahLogin`) — cuma bagian `if`-nya (yang udah login) diganti dropdown, bagian `else`-nya (Login/Register) dibiarin tombol biasa:
```php
<?php if ($sudahLogin): ?>
    <!-- dropdown di atas -->
<?php else: ?>
    <li class="nav-item me-2"><a href="login.php" class="btn btn-light btn-sm">Login</a></li>
    <li class="nav-item"><a href="register.php" class="btn btn-outline-light btn-sm">Register</a></li>
<?php endif; ?>
```

**Penting:** dropdown butuh Bootstrap Icons buat ikon `bi-person-circle` — pastiin CDN-nya ada di `<head>` (lihat bagian "Apa Itu Bootstrap" di atas). Sempet ketinggalan di `katalog.php`, sekarang udah ditambahin.

## File di Folder Ini

```
8-Bootstrap-Integration/
  README.md
  config/
    db.php
  includes/
    auth-check.php
    admin-check.php
    navbar.php          <- BARU: partial navbar
    admin-sidebar.php
  index.php
  katalog.php
  about.php
  favorit-toggle.php
  register.php / login.php / logout.php
  dashboard.php            <- overview: stat, quick action, preview favorit
  favorit-saya.php          <- daftar lengkap wishlist user
  profil.php                <- BARU: ganti username & password
  admin/
    dashboard.php
    buku.php              <- + modal konfirmasi hapus
    buku-tambah.php
    buku-edit.php
    users.php              <- + modal konfirmasi hapus
```

## Urutan Belajar

1. Buka `index.php`, `katalog.php`, `about.php` — perhatiin navbar-nya sama di ketiganya (dari 1 partial), coba resize browser ke ukuran HP, menu-nya otomatis collapse jadi hamburger icon (`navbar-toggler`) — ini otomatis dari Bootstrap, gak perlu nulis media query sendiri.
2. Bandingin `login.php`/`register.php` sekarang vs Sesi 7 — struktur HTML-nya lebih ringkas (`form-control`, `mb-3`, dst), gak perlu nulis CSS manual.
3. Login sebagai admin, buka `admin/buku.php`, klik tombol "Hapus" — perhatiin modal muncul (bukan popup `confirm()` browser).
4. Coba resize browser di halaman `katalog.php` — grid kartu buku otomatis nyesuain jumlah kolom (`col-md-4` = 3 kolom di layar gede, numpuk 1 kolom di HP) tanpa nulis CSS responsive manual.

## Update: Homepage & About Dirombak Jadi Lebih Proper

`index.php` dan `about.php` awalnya cuma hero polos + list buku. Sekarang dirombak jadi landing page beneran, sambil masukin **Bootstrap Icons** (paket ikon terpisah, CDN sendiri):
```html
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
```
Pemakaiannya tinggal `<i class="bi bi-book"></i>` — cari nama ikonnya di situs Bootstrap Icons.

**`index.php`** sekarang urutannya: Hero (gradient background) &rarr; Statistik singkat (jumlah buku/user/favorit, query `COUNT()`) &rarr; 3 alasan "Kenapa Toko Buku" (card icon) &rarr; Buku Terbaru &rarr; CTA "Daftar Sekarang" (cuma nongol kalau belum login) &rarr; Footer 3 kolom.

**`about.php`** sekarang isinya cerita beneran (bukan dummy 2 baris doang): section cerita "Awalnya Cuma Latihan CRUD", 4 kartu fitur (cari, favorit, login aman, panel admin), statistik + disclaimer bahwa ini bukan toko sungguhan.

Footer 3 kolom (Toko Buku / Navigasi / Kontak dummy) disamain juga di `katalog.php` biar konsisten di semua halaman publik.

## Update: Halaman Admin Dirapihin

Sidebar & halaman admin awalnya polos (teks doang, gak ada ikon/badge). Dirapihin:

- **`includes/admin-sidebar.php`** — nambah Bootstrap Icons di tiap menu, link "Kembali ke Home" (sempet ketinggalan pas awal bikin sidebar), sama kartu profil kecil (avatar inisial + username) di atas tombol Logout.
- **`admin/dashboard.php`** — banner sambutan gradient, stat card sekarang ada ikon dalam lingkaran warna (bukan angka polos), tombol "Kelola Buku"/"Kelola Users" diganti jadi kartu "Aksi Cepat" yang bisa diklik.
- **`admin/buku.php`** — badge warna buat stok (merah kalau habis, kuning kalau tinggal sedikit), empty state kalau belum ada buku sama sekali, tombol Edit/Hapus diganti icon-only biar tabel gak sesak.
- **`admin/users.php`** — avatar bulat inisial username, badge "Kamu" di baris akun sendiri, empty state kalau belum ada user.
- **`admin/buku-tambah.php` & `admin/buku-edit.php`** — link "Kembali ke Kelola Buku", ikon di tiap label field, `form-text` (petunjuk kecil abu-abu) di field URL Gambar.

Semua ikon pakai **Bootstrap Icons**, CDN terpisah dari Bootstrap inti:
```html
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
```

## Update: Nomor Urut Tampilan + Pagination di Kelola Buku & Kelola Users

Kolom "ID" sebelumnya nampilin `id` mentah dari database (`#7`, `#11`, dst) — begitu ada data yang dihapus, angkanya jadi bolong (7, 9, 10 — nomor 8 ilang selamanya karena AUTO_INCREMENT gak pernah mundur). Sekarang diganti kolom "No" yang isinya nomor urut TAMPILAN, bukan id:

```php
$nomor = $offset + 1;
foreach ($daftar as $row) {
    echo $nomor++ . "."; // 1. 2. 3. dst, selalu rapi jalan
}
```

`$offset` dari pagination bikin nomornya nyambung antar halaman (halaman 2 lanjut dari 11, bukan mulai dari 1 lagi). Nomor urut ini cuma buat tampilan — `id` asli tetep dipakai di balik layar (link Edit, tombol Hapus, dst), soalnya itu yang dibutuhin buat query database.

Sekalian ditambahin **pagination 10 baris per halaman** di `admin/buku.php` dan `admin/users.php`, pola sama kayak di `katalog.php` (`LIMIT`/`OFFSET` + komponen `pagination` Bootstrap).

## Update: Dashboard User Dirapihin + Halaman Wishlist Terpisah

`dashboard.php` awalnya cuma 2 kartu polos (info akun + list favorit). Sekarang:

- **Tombol "Kembali ke Home"** eksplisit di atas halaman (sebelumnya cuma ngandelin link Home di navbar).
- **Welcome banner** gradient, sama gayanya kayak dashboard admin.
- **Stat card** jumlah favorit, dengan ikon.
- **2 kartu Aksi Cepat**: "Jelajahi Buku" (ke `katalog.php`) dan "Buku Favorit Saya" (ke halaman baru `favorit-saya.php`).
- **Preview 3 favorit terbaru** doang di dashboard, biar gak numpuk kalau favoritnya udah banyak.

**`favorit-saya.php`** (baru total) — halaman wishlist lengkap, kartu buku persis kayak `katalog.php` (gambar, judul, penulis, stok), tombol "Hapus dari Favorit" di tiap kartu (submit ke `favorit-toggle.php`, redirect balik ke halaman ini lewat `kembali`). ini yang beda dari dashboard: dashboard cuma preview 3 buku, halaman ini nampilin SEMUA favorit user.

## Update: Login & Register Jadi Split-Screen

`login.php` dan `register.php` awalnya cuma kartu kecil di tengah layar. Diganti jadi layout split-screen (umum dipakai di aplikasi modern):

- **Panel kiri** — gradient background + ikon buku besar + teks ajakan, disembunyiin di layar kecil (`d-none d-lg-flex`, HP cuma nampilin form-nya doang, gak ada ruang buat panel dekoratif).
- **Panel kanan** — form, sekarang pakai `input-group` (ikon nempel di dalem kotak input, bukan `form-label` polos doang):
```php
<div class="input-group">
    <span class="input-group-text bg-white"><i class="bi bi-person"></i></span>
    <input type="text" name="username" class="form-control" placeholder="Username kamu">
</div>
```
- Tombol "Kembali ke Home" tetep ada di pojok kiri atas form.

## Update: Dashboard User Sekarang Pakai Sidebar (Bukan Navbar)

`dashboard.php` dan `favorit-saya.php` sebelumnya masih pakai `includes/navbar.php` (sama kayak halaman publik `index.php`/`katalog.php`/`about.php`). Sekarang diganti pola sidebar, sama persis kayak halaman admin, biar keliatan jelas "ini area akun kamu", bukan halaman publik.

**File baru: `includes/user-sidebar.php`** — polanya sama kayak `includes/admin-sidebar.php`, isinya beda:
```php
<a href="dashboard.php">Dashboard</a>
<a href="katalog.php">Jelajahi Buku</a>
<a href="favorit-saya.php">Favorit Saya</a>
<a href="about.php">About</a>
```
Plus link "Kembali ke Home" di paling atas, dan kartu profil kecil (avatar inisial) di atas tombol Logout — persis kayak sidebar admin.

`dashboard.php` dan `favorit-saya.php` sekarang pakai layout `d-flex` (sidebar + `<main>`), bukan `<div class="container">` doang. Halaman publik (`index.php`, `katalog.php`, `about.php`) TETEP pakai navbar biasa — cuma halaman yang butuh login (dashboard & favorit) yang pindah ke sidebar.

## Update: Edit Profile — Ganti Username & Password

Halaman baru **`profil.php`**, linknya ada di sidebar user (`includes/user-sidebar.php`, menu "Edit Profile"). Sekalian dirapihin: kartu "Jelajahi Buku" di dashboard dihapus (udah kecover sama link "Kembali ke Home" + "Jelajahi Buku" di sidebar, gak perlu dobel), link "About" di sidebar user juga dihapus (about itu halaman publik, gak perlu nongol di area akun).

`profil.php` isinya 2 card terpisah, masing-masing form sendiri (`<input type="hidden" name="form" value="username">` / `"password"` buat bedain form mana yang di-submit):

**Ganti username** — cek dulu gak bentrok sama user lain:
```php
$query = "SELECT id FROM users WHERE username = ? AND id != ?";
// ... kalau ketemu -> error "sudah dipakai"

$query = "UPDATE users SET username = ? WHERE id = ?";
// ...
$_SESSION['username'] = $usernameBaru; // WAJIB, session gak auto-sync ke DB
```

**Ganti password** — wajib verifikasi password lama dulu sebelum ganti (biar bukan sembarang orang yang kebetulan session-nya aktif bisa ambil alih akun):
```php
$query = "SELECT password FROM users WHERE id = ?";
// ambil hash asli dari database

if (!password_verify($passwordLama, $user['password'])) {
    $errors[] = "Password lama salah.";
}
// baru lanjut validasi password baru + UPDATE kalau password lama bener
```

## Checkpoint Sebelum Lanjut ke Sesi 9

Sebelum lanjut ke `9-Tailwind-Upgrade/`, pastikan bisa jawab/lakuin ini tanpa buka catatan/AI:
- Jelasin beda Bootstrap CDN vs Bootstrap lokal.
- Jelasin kenapa `navbar.php` sekarang layak dipisah jadi partial, padahal di Sesi 7 sengaja dibiarin inline.
- Jelasin cara kerja modal Bootstrap — kenapa tombol "Hapus" gak langsung ngirim form.
- Restyle 1 halaman baru (misal `about.php`) pake komponen Bootstrap yang belum dipake di sesi ini (coba cari di dokumentasi Bootstrap sendiri — accordion, badge, atau breadcrumb).
