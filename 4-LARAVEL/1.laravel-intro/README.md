# Laravel Intro — Catatan Belajar

Project ini buat latihan belajar Laravel. Catatan di bawah dibuat buat pemula yang udah paham PHP native + pola MVC manual, biar gampang nyambungin ke "kosakata" Laravel.

## Daftar Isi

- [Apa itu Laravel?](#apa-itu-laravel)
- [Pola MVC di Laravel](#pola-mvc-di-laravel)
- [Eloquent ORM](#eloquent-orm)
- [Cara Setup Laravel](#cara-setup-laravel)
- [Struktur Folder Laravel (Catatan Belajar)](#struktur-folder-laravel-catatan-belajar)
- [Praktik Pertama: Jalankan Website & Bikin Route "Hello World"](#praktik-pertama-jalankan-website--bikin-route-hello-world)
- [Praktik Kedua: Pindah Logic ke Controller + Route Dinamis](#praktik-kedua-pindah-logic-ke-controller--route-dinamis)
- [Praktik Ketiga: Blade Layout (Master Template)](#praktik-ketiga-blade-layout-master-template)
- [Praktik Keempat: Model, Migration & Database (Eloquent)](#praktik-keempat-model-migration--database-eloquent)
- [Praktik Kelima: CRUD Lengkap (Create, Update, Delete via Web)](#praktik-kelima-crud-lengkap-create-update-delete-via-web)
- [Praktik Keenam: Rapihin Tampilan pakai Tailwind CSS](#praktik-keenam-rapihin-tampilan-pakai-tailwind-css)
- [Praktik Ketujuh: Pindah dari SQLite ke MySQL (phpMyAdmin)](#praktik-ketujuh-pindah-dari-sqlite-ke-mysql-phpmyadmin)
- [Praktik Kedelapan: Login/Register pakai Laravel Breeze](#praktik-kedelapan-loginregister-pakai-laravel-breeze)
- [Praktik Kesembilan: Relasi Post-User](#praktik-kesembilan-relasi-post-user)
- [Praktik Kesepuluh: Role Admin vs User](#praktik-kesepuluh-role-admin-vs-user)
- [Praktik Kesebelas: Lengkapi CRUD User Management (Tambah & Hapus User)](#praktik-kesebelas-lengkapi-crud-user-management-tambah--hapus-user)
- [Praktik Kedua Belas: Slug + Single Post View](#praktik-kedua-belas-slug--single-post-view)
- [Praktik Ketiga Belas: Upload/Link Gambar](#praktik-ketiga-belas-uploadlink-gambar)
- [Praktik Keempat Belas: Database Seeder](#praktik-keempat-belas-database-seeder)
- [Praktik Kelima Belas: Search & Filter](#praktik-kelima-belas-search--filter)
- [Praktik Keenam Belas: Pagination & Card Grid](#praktik-keenam-belas-pagination--card-grid)
- [Praktik Ketujuh Belas: Form Request](#praktik-ketujuh-belas-form-request)
- [Praktik Kedelapan Belas: Policy](#praktik-kedelapan-belas-policy)
- [Praktik Kesembilan Belas: API Resource + Testing pakai Thunder Client](#praktik-kesembilan-belas-api-resource--testing-pakai-thunder-client)
- [Roadmap Belajar Selanjutnya](#roadmap-belajar-selanjutnya)

## Apa itu Laravel?

Laravel adalah framework PHP buat bikin web app, pakai pola **MVC (Model-View-Controller)**. Tujuannya: bikin tugas-tugas umum (routing, koneksi database, autentikasi, dll) jadi lebih cepat & rapi, tanpa nulis semuanya dari nol.

## Pola MVC di Laravel

MVC = cara misahin tanggung jawab kode jadi 3 bagian:

- **Model** — ngurus data & logic ke database. Di Laravel pakai **Eloquent ORM**, taruh di `app/Models/`. Tiap model biasanya mewakili 1 tabel.
- **View** — ngurus tampilan (HTML) yang dilihat user. Di Laravel pakai **Blade template** (`.blade.php`), taruh di `resources/views/`.
- **Controller** — jembatan antara Model & View. Nerima request, ambil/olah data lewat Model, lalu kirim ke View. Taruh di `app/Http/Controllers/`.

Alur request di Laravel:

```
User buka URL → routes/web.php (cocokin URL ke Controller)
             → Controller (ambil data via Model, proses logic)
             → Model (Eloquent, query ke database)
             → Controller kirim data ke View
             → View (Blade) render HTML
             → Response dikirim balik ke user
```

Mapping ke yang udah dipelajari di PHP native:

| Konsep MVC       | PHP native (manual)              | Laravel                                   |
| ---------------- | -------------------------------- | ----------------------------------------- |
| Front controller | `index.php` + router manual      | `public/index.php` (otomatis)             |
| Router           | `switch` / array routing         | `routes/web.php`                          |
| Controller       | `controllers/UserController.php` | `app/Http/Controllers/`                   |
| Model / akses DB | class PDO manual                 | `app/Models/` (Eloquent ORM)              |
| View             | `views/*.php` + `include`        | `resources/views/*.blade.php`             |
| Config           | `config.php`                     | `.env` + `config/*.php`                   |
| Autoload class   | `require` manual                 | Composer autoload (`vendor/autoload.php`) |

## Eloquent ORM

Eloquent adalah ORM (Object-Relational Mapping) bawaan Laravel. Intinya: tiap tabel database direpresentasikan pakai class PHP (Model), jadi query database bisa ditulis pakai method PHP, bukan SQL mentah.

### Bikin Model

```bash
php artisan make:model Post
```

Hasilnya muncul `app/Models/Post.php`. Secara default, Eloquent nebak nama tabel = bentuk plural dari nama model (model `Post` → tabel `posts`).

### Konvensi penting

- Nama tabel = plural, lowercase, snake_case (`Post` → `posts`, `OrderItem` → `order_items`).
- Primary key default = `id`.
- Kolom `created_at` & `updated_at` otomatis dikelola Eloquent (timestamps).

### Contoh query dasar (CRUD)

```php
// Create
Post::create(['title' => 'Judul', 'body' => 'Isi konten']);

// Read - ambil semua
$posts = Post::all();

// Read - ambil 1 by id
$post = Post::find(1);

// Read - filter
$posts = Post::where('title', 'like', '%laravel%')->get();

// Update
$post = Post::find(1);
$post->title = 'Judul Baru';
$post->save();

// Delete
Post::find(1)->delete();
```

Perbandingan dengan PDO manual:

| PDO manual                                          | Eloquent              |
| --------------------------------------------------- | --------------------- |
| `$stmt = $pdo->query("SELECT * FROM posts")`        | `Post::all()`         |
| `$pdo->prepare("SELECT * FROM posts WHERE id = ?")` | `Post::find($id)`     |
| `INSERT INTO posts ...`                             | `Post::create([...])` |
| `UPDATE posts SET ... WHERE id = ...`               | `$post->save()`       |
| `DELETE FROM posts WHERE id = ...`                  | `$post->delete()`     |

### Relasi antar tabel

Eloquent bisa definisikan relasi antar model langsung di class-nya:

```php
// di Model Post
public function comments()
{
    return $this->hasMany(Comment::class);
}

// di Model Comment
public function post()
{
    return $this->belongsTo(Post::class);
}
```

Pakainya:

```php
$post = Post::find(1);
$comments = $post->comments; // ambil semua comment milik post ini
```

Jenis relasi umum: `hasOne`, `hasMany`, `belongsTo`, `belongsToMany` (many-to-many).

## Cara Setup Laravel

### 1. Yang perlu di-install dulu

| Kebutuhan                                                                                                    | Kenapa perlu                                 | Cara cek             |
| ------------------------------------------------------------------------------------------------------------ | -------------------------------------------- | -------------------- |
| PHP 8.2+                                                                                                     | minimum requirement Laravel 11/12            | `php -v`             |
| Composer                                                                                                     | manajer dependency PHP, buat install Laravel | `composer -V`        |
| Ekstensi PHP wajib (`mbstring`, `openssl`, `pdo`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`) | dibutuhin Laravel core                       | `php -m`             |
| Node.js + npm                                                                                                | compile asset frontend (Vite, CSS/JS)        | `node -v` / `npm -v` |
| Git (opsional)                                                                                               | version control                              | `git -v`             |

> Kalau pakai **Laragon**, PHP, Composer, MySQL, dan Node biasanya udah include. Tinggal pastiin versi PHP minimal 8.2 (klik kanan Laragon → PHP → pilih versi).

Kalau Composer belum ada, download dari [getcomposer.org](https://getcomposer.org/download/), arahkan ke `php.exe` Laragon saat install.

### 2. Bikin project Laravel baru

```bash
cd D:/laragon/www
composer create-project laravel/laravel nama-project
```

Atau pakai Laravel Installer:

```bash
composer global require laravel/installer
laravel new nama-project
```

Ini bakal download:

- Core framework Laravel (folder `vendor/`)
- Struktur awal `app/`, `routes/`, `resources/`, dll
- File config contoh `.env.example`

### 3. Setup environment (`.env`)

```bash
copy .env.example .env
php artisan key:generate
```

Atur koneksi database di `.env` (default Laragon MySQL):

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database
DB_USERNAME=root
DB_PASSWORD=
```

Lalu buat database-nya lewat HeidiSQL/phpMyAdmin (bawaan Laragon).

### 4. Jalankan project

```bash
php artisan serve
```

Buka `http://127.0.0.1:8000`.

Atau kalau pakai Laragon virtual host: buka `http://nama-project.test` (Laragon otomatis arahin ke folder `public/`).

### Cheatsheet Troubleshooting

| Masalah                                               | Solusi                                               |
| ----------------------------------------------------- | ---------------------------------------------------- |
| Error `Class not found`                               | `composer dump-autoload`                             |
| Perubahan `.env` tidak ke-apply                       | `php artisan config:clear`                           |
| Error 500 / halaman blank                             | cek `storage/logs/laravel.log`                       |
| Permission error di `storage/` atau `bootstrap/cache` | pastikan folder writable                             |
| `php artisan` command not found                       | jalankan dari root project, pastikan PHP ada di PATH |

## Struktur Folder Laravel (Catatan Belajar)

Catatan ini buat pemula yang baru belajar Laravel, biar paham fungsi tiap folder utama di project ini.

- **`app/`** — Tempat "otak" aplikasi. Isi logic utama:
    - `app/Http/Controllers/` — Controller, tempat nulis logic buat handle request (misal ambil data, proses form, return view).
    - `app/Models/` — Model, representasi tabel database (Eloquent ORM). Tiap model biasanya mewakili satu tabel.
    - `app/Providers/` — Service provider, tempat daftar/konfigurasi service saat aplikasi start.

- **`bootstrap/`** — File buat "menyalakan" framework Laravel (jarang diutak-atik pemula).

- **`config/`** — Semua file konfigurasi aplikasi (database, mail, cache, dll). Biasanya nilai-nilainya ambil dari `.env`.

- **`database/`** — Urusan database:
    - `migrations/` — Blueprint struktur tabel (bikin/ubah tabel pakai kode, bukan manual lewat phpMyAdmin).
    - `seeders/` — Script buat isi data dummy/awal ke database.
    - `factories/` — Template buat generate data palsu (dummy) untuk testing/seeding.

- **`public/`** — Folder yang diakses publik dari browser. Ada `index.php` (entry point semua request) dan asset hasil build (CSS/JS).

- **`resources/`** — Bahan mentah tampilan & asset:
    - `views/` — File Blade (`.blade.php`), template HTML buat ditampilkan ke user.
    - `css/` & `js/` — Source CSS/JS sebelum diproses Vite.

- **`routes/`** — Daftar "rute" / URL aplikasi:
    - `web.php` — Rute buat halaman web biasa.
    - `console.php` — Rute buat command artisan custom.

- **`storage/`** — Tempat nyimpen file log, cache, file upload, dan hasil compile view. Biasanya nggak ikut di-commit.

- **`tests/`** — Tempat nulis automated test (unit test, feature test) buat mastiin aplikasi jalan sesuai harapan.

- **`vendor/`** — Library/package PHP dari Composer (auto-generate, jangan diedit manual).

- **`.env`** — File konfigurasi rahasia/lokal (database credential, app key, dll). Jangan di-commit ke git.

- **`artisan`** — Command line tool Laravel. Contoh: `php artisan serve`, `php artisan migrate`, `php artisan make:controller`.

- **`composer.json`** — Daftar dependency PHP (kayak `package.json` tapi buat PHP).

- **`package.json`** — Daftar dependency JS (buat Vite, Tailwind, dll).

## Simpanan

**Topik: Controller + Blade Layout + Dynamic Route**

1. Pindah logic ke Controller (~20 min) — php artisan make:controller HelloController, pindah closure /hello ke method index(). Konsep: route cuma "alamat", controller yg kerja.

2. Route parameter / dynamic route (~20 min) — Route::get('/hello/{name}', [HelloController::class, 'show']). Murid coba akses /hello/Budi → muncul "Halo, Budi!". Ngajarin konsep request data dari URL.

3. Blade layout & komponen (~30 min) — bikin layouts/app.blade.php pakai @yield/@section, terus hello.blade.php extend layout itu. Konsep: DRY, reuse template (mirip header/footer include manual).

4. Eloquent + migration + database pertama (~40 min) — php artisan make:migration create_posts_table, php artisan migrate, php artisan make:model Post, tampilin data dari DB di view via controller. Ini momen "MVC lengkap" pertama: Route → Controller → Model (Eloquent) → View.

5. (kalau waktu sisa) form input sederhana — Route::post, terus simpan ke DB pakai Post::create().

## Praktik Pertama: Jalankan Website & Bikin Route "Hello World"

Step by step paling dasar, biar langsung lihat hasil.

### 1. Jalankan server Laravel

Buka terminal di folder project, jalankan:

```bash
php artisan serve
```

Output-nya kira-kira:

```
INFO  Server running on [http://127.0.0.1:8000].
```

### 2. Buka di browser

Buka `http://127.0.0.1:8000`. Harus muncul halaman welcome bawaan Laravel (logo Laravel + link dokumentasi).

> Project ini default-nya pakai database **SQLite** (`database/database.sqlite`), jadi nggak perlu setting MySQL dulu buat lihat halaman ini.

### 3. Tambah route "Hello World"

Buka file `routes/web.php`. Isinya sekarang:

```php
<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
```

Tambahin route baru di bawahnya:

```php
Route::get('/hello', function () {
    return 'Hello World!';
});
```

Jadi file lengkapnya:

```php
<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/hello', function () {
    return 'Hello World!';
});
```

### 4. Cek hasilnya

Buka `http://127.0.0.1:8000/hello` — harus muncul teks `Hello World!`.

> Nggak perlu restart `php artisan serve`, perubahan route langsung kebaca tiap request.

### 5. (Opsional) Hello World pakai View Blade

Biar kerasa "Laravel-nya", coba pakai view + variabel:

1. Bikin file `resources/views/hello.blade.php`:

    ```blade
    <h1>Halo, {{ $name }}!</h1>
    ```

2. Ubah route `/hello` jadi:

    ```php
    Route::get('/hello', function () {
        return view('hello', ['name' => 'Pemula Laravel']);
    });
    ```

3. Refresh `http://127.0.0.1:8000/hello` — harus muncul `Halo, Pemula Laravel!`.

Ini udah nunjukin alur **Route → (Controller/Closure) → View** yang dibahas di bagian [Pola MVC di Laravel](#pola-mvc-di-laravel).

## Praktik Kedua: Pindah Logic ke Controller + Route Dinamis

Di Praktik Pertama, logic-nya masih nempel langsung di `routes/web.php` (pakai closure/function). Sekarang dipindah ke **Controller**, biar route cuma jadi "alamat" dan controller yang kerja — sesuai pola MVC.

### 1. Bikin Controller

```bash
php artisan make:controller HelloController
```

Ini bikin file `app/Http/Controllers/HelloController.php` isinya class kosong.

### 2. Pindah logic `/hello` ke Controller

Buka `app/Http/Controllers/HelloController.php`, tambah method `index()`:

```php
<?php

namespace App\Http\Controllers;

class HelloController extends Controller
{
    public function index()
    {
        return view('hello', ['name' => 'Pemula Laravel']);
    }
}
```

### 3. Update route biar manggil Controller

Buka `routes/web.php`, ganti route `/hello` yang pakai closure jadi manggil controller:

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HelloController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/hello', [HelloController::class, 'index']);
```

### 4. Cek hasilnya

Refresh `http://127.0.0.1:8000/hello` — hasilnya harus sama kayak sebelumnya (`Halo, Pemula Laravel!`), cuma sekarang logic-nya udah pindah ke Controller.

### 5. Tambah route dinamis (route parameter)

Sekarang bikin `/hello/{name}` — nama-nya diambil dari URL.

Tambah method baru di `HelloController`:

```php
public function show($name)
{
    return view('hello', ['name' => $name]);
}
```

Tambah route baru di `routes/web.php`:

```php
Route::get('/hello/{name}', [HelloController::class, 'show']);
```

### 6. Cek hasilnya

Buka `http://127.0.0.1:8000/hello/Budi` — harus muncul `Halo, Budi!`.
Coba ganti `Budi` jadi nama lain di URL, hasilnya ikut berubah.

> Ini konsep **route parameter**: bagian `{name}` di URL otomatis dikirim jadi argumen ke method controller. Mirip kayak `$_GET['name']` di PHP native, tapi lebih rapi & jadi bagian dari struktur URL.

### Ringkasan Praktik Kedua

| Sebelum (Praktik 1)                        | Sesudah (Praktik 2)                 |
| ------------------------------------------ | ----------------------------------- |
| Logic nempel di `routes/web.php` (closure) | Logic dipindah ke `HelloController` |
| URL statis `/hello`                        | URL dinamis `/hello/{name}`         |
| Data hardcode (`'Pemula Laravel'`)         | Data dari URL (`$name`)             |

## Praktik Ketiga: Blade Layout (Master Template)

Sekarang file `hello.blade.php` cuma berisi 1 baris `<h1>`, belum ada HTML lengkap (`<html>`, `<head>`, `<body>`). Kalau nanti ada banyak halaman, nulis `<html>...</html>` di tiap file itu boros & susah maintenance. Solusinya: bikin **1 layout utama**, halaman lain tinggal "isi konten"-nya aja.

Fokus praktik ini: **View / tampilan** — pakai `@extends`, `@section`, `@yield`.

### 1. Bikin folder & file layout

Bikin folder baru `resources/views/layouts/`, lalu file `resources/views/layouts/app.blade.php`:

```blade
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Laravel Intro')</title>
</head>
<body>

    @yield('content')

</body>
</html>
```

Penjelasan:

- `@yield('title', 'Laravel Intro')` — "lubang" buat title halaman, kalau halaman anak nggak isi, pakai default `'Laravel Intro'`.
- `@yield('content')` — "lubang" buat konten utama halaman.

### 2. Ubah `hello.blade.php` jadi "anak" dari layout

Buka `resources/views/hello.blade.php`, ganti isinya jadi:

```blade
@extends('layouts.app')

@section('title', 'Halaman Hello')

@section('content')
    <h1>Halo, {{ $name }}!</h1>
@endsection
```

Penjelasan:

- `@extends('layouts.app')` — bilang "halaman ini pakai layout `layouts/app.blade.php`".
- `@section('title', 'Halaman Hello')` — isi "lubang" `title` di layout dengan teks ini.
- `@section('content') ... @endsection` — isi "lubang" `content` di layout dengan HTML di dalamnya.

### 3. Cek hasilnya

Refresh dua-duanya:

- `http://127.0.0.1:8000/hello`
- `http://127.0.0.1:8000/hello/Budi`

Tampilan `<h1>` harus tetap muncul kayak sebelumnya, tapi sekarang dibungkus struktur HTML lengkap dari layout (cek "View Page Source" di browser — ada `<html>`, `<head>`, `<title>Halaman Hello</title>`, `<body>`).

### 4. (Opsional) Tambah navigasi di layout

Coba tambah sedikit di `layouts/app.blade.php`, sebelum `@yield('content')`:

```blade
<nav>
    <a href="/hello">Hello</a> |
    <a href="/hello/Budi">Hello Budi</a>
</nav>
```

Refresh halaman `/hello` & `/hello/Budi` — nav ini harus muncul di **kedua** halaman, padahal cuma ditulis 1x di layout. Ini inti dari "reuse template".

### Ringkasan Praktik Ketiga

| Sebelum (Praktik 2)                                             | Sesudah (Praktik 3)                                                          |
| --------------------------------------------------------------- | ---------------------------------------------------------------------------- |
| `hello.blade.php` cuma potongan HTML (`<h1>` aja)               | `hello.blade.php` jadi "isi konten" dari layout                              |
| Belum ada struktur HTML lengkap                                 | Struktur `<html><head><body>` ada di `layouts/app.blade.php`, dipakai bareng |
| Kalau nambah halaman baru, harus tulis ulang `<html>...</html>` | Halaman baru tinggal `@extends('layouts.app')` + isi `@section('content')`   |

> Analogi PHP native: ini mirip `include 'header.php'` + `include 'footer.php'` di tiap halaman — tapi versi Laravel lebih rapi karena pakai sistem "lubang" (`@yield`/`@section`) bukan asal `include`.

## Praktik Keempat: Model, Migration & Database (Eloquent)

Praktik 1-3 datanya masih hardcode/dari URL. Sekarang data diambil dari **database asli** pakai Eloquent — ini melengkapi alur MVC penuh: `Route → Controller → Model → View`.

Fokus praktik ini: **Model** — `migration` (bikin tabel) + `Eloquent` (query tabel).

### 1. Bikin migration buat tabel `posts`

```bash
php artisan make:migration create_posts_table
```

File baru muncul di `database/migrations/`, isinya udah ada method `up()` & `down()`. Edit method `up()` jadi:

```php
public function up(): void
{
    Schema::create('posts', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->text('body');
        $table->timestamps();
    });
}
```

### 2. Jalankan migration

```bash
php artisan migrate
```

Ini bikin tabel `posts` beneran di `database/database.sqlite` sesuai schema di atas.

### 3. Bikin Model `Post`

```bash
php artisan make:model Post
```

Hasilnya `app/Models/Post.php` — class kosong yang otomatis "nyambung" ke tabel `posts` (sesuai konvensi yang dibahas di [Eloquent ORM](#eloquent-orm)).

### 4. Isi data dummy lewat Tinker

`php artisan tinker` itu console interaktif buat coba-coba kode Laravel langsung:

```bash
php artisan tinker
```

Di dalam tinker, jalankan:

```php
use App\Models\Post;
Post::create(['title' => 'Post Pertama', 'body' => 'Ini isi post pertama dari database.']);
Post::create(['title' => 'Post Kedua', 'body' => 'Ini isi post kedua dari database.']);
exit
```

nanti hasilnya :

```bash
Vanya@LAPTOP-RA740P9P MINGW64 /d/laragon/www/laravel-intro
$ php artisan tinker
Psy Shell v0.12.23 (PHP 8.3.16 — cli) by Justin Hileman
New PHP manual is available (latest: 3.1.0). Update with `doc --update-manual`
> use App\Models\Post;

> Post::create(['title' => 'Post Pertama', 'body' => 'Ini isi post pertama dari database.']);

= App\Models\Post {#7874
    title: "Post Pertama",
    body: "Ini isi post pertama dari database.",
    updated_at: "2026-06-12 06:50:29",
    created_at: "2026-06-12 06:50:29",
    id: 1,
  }

> Post::create(['title' => 'Post Kedua', 'body' => 'Ini isi post kedua dari database.']);

= App\Models\Post {#7434
    title: "Post Kedua",
    body: "Ini isi post kedua dari database.",
    updated_at: "2026-06-12 06:50:37",
    created_at: "2026-06-12 06:50:37",
    id: 2,
  }
```

### 5. Bikin Controller buat Post

```bash
php artisan make:controller PostController
```

Isi `app/Http/Controllers/PostController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Post;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::all();

        return view('posts.index', ['posts' => $posts]);
    }
}
```

### 6. Bikin View buat list Post

Bikin folder `resources/views/posts/`, lalu file `resources/views/posts/index.blade.php`:

```blade
@extends('layouts.app')

@section('title', 'Daftar Post')

@section('content')
    <h1>Daftar Post</h1>

    <ul>
        @foreach ($posts as $post)
            <li>
                <strong>{{ $post->title }}</strong>
                <p>{{ $post->body }}</p>
            </li>
        @endforeach
    </ul>
@endsection
```

`@foreach` di Blade = `foreach` PHP biasa, cuma syntax-nya lebih ringkas (gak perlu `<?php ?>`).

### 7. Tambah Route

Di `routes/web.php`:

```php
use App\Http\Controllers\PostController;

Route::get('/posts', [PostController::class, 'index']);
```

### 8. Cek hasilnya

Buka `http://127.0.0.1:8000/posts` — harus muncul list 2 post yang ditambah lewat tinker tadi, dibungkus layout yang sama kayak `/hello`.

> (Opsional) Tambah link `/posts` di navbar `layouts/app.blade.php`, biar bisa pindah-pindah halaman dari nav.

### Ringkasan Praktik Keempat

| Sebelum (Praktik 1-3)                  | Sesudah (Praktik 4)                                 |
| -------------------------------------- | --------------------------------------------------- |
| Data hardcode di Controller / dari URL | Data dari tabel `posts` di database                 |
| Belum ada Model                        | Ada `app/Models/Post.php` (Eloquent)                |
| Alur: Route → Controller → View        | Alur lengkap: Route → Controller → **Model** → View |

> Ini titik penting: sekarang kamu udah bikin alur MVC **lengkap** ala Laravel — sama persis konsepnya kayak yang dulu dibikin manual (router → controller → query PDO → view), cuma "kosakata" & toolingnya beda.

## Praktik Kelima: CRUD Lengkap (Create, Update, Delete via Web)

Praktik 4 udah ada **Read** (`/posts` nampilin data). Sekarang lengkapin jadi CRUD penuh — **C**reate, **R**ead, **U**pdate, **D**elete — semua lewat form di browser, gak pakai tinker lagi.

Konsep baru yang dibahas: **form HTTP method** (`POST`/`PUT`/`DELETE`), **validasi**, **route model binding**.

### 0. Konsep dulu: HTTP method & form

Form HTML cuma bisa kirim `GET` atau `POST` secara native. Laravel "nge-trick" ini pakai `@method('PUT')` / `@method('DELETE')` (hidden input), biar browser tetep kirim `POST` tapi Laravel baca sebagai `PUT`/`DELETE`. Plus tiap form **wajib** ada `@csrf` (token anti-pemalsuan request — keamanan dasar Laravel).

### 1. Tambah Route

Buka `routes/web.php`, tambah di bawah route `/posts` yang udah ada:

```php
Route::get('/posts/create', [PostController::class, 'create']);
Route::post('/posts', [PostController::class, 'store']);
Route::get('/posts/{post}/edit', [PostController::class, 'edit']);
Route::put('/posts/{post}', [PostController::class, 'update']);
Route::delete('/posts/{post}', [PostController::class, 'destroy']);
```

> `{post}` di sini pakai **route model binding** — Laravel otomatis cariin `Post` berdasarkan id di URL, terus dikirim langsung jadi objek `$post` ke controller. Gak perlu manual `Post::find($id)` lagi.

### 2. Update Controller — tambah 5 method baru

Buka `app/Http/Controllers/PostController.php`, jadi:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::all();

        return view('posts.index', ['posts' => $posts]);
    }

    public function create()
    {
        return view('posts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        Post::create($validated);

        return redirect('/posts');
    }

    public function edit(Post $post)
    {
        return view('posts.edit', ['post' => $post]);
    }

    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $post->update($validated);

        return redirect('/posts');
    }

    public function destroy(Post $post)
    {
        $post->delete();

        return redirect('/posts');
    }
}
```

Penjelasan:
- `$request->validate([...])` — kalau input kosong/salah format, Laravel otomatis redirect balik + simpan error message. Gak perlu `if (empty(...))` manual.
- `Post::create($validated)` / `$post->update($validated)` — `$validated` itu array data yang udah lolos validasi.
- `redirect('/posts')` — abis create/update/delete, balik ke halaman list.

### 3. Bikin Form Create

File `resources/views/posts/create.blade.php`:

```blade
@extends('layouts.app')

@section('title', 'Tambah Post')

@section('content')
    <h1>Tambah Post</h1>

    @if ($errors->any())
        <ul style="color: red;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="/posts">
        @csrf

        <label>Title:</label><br>
        <input type="text" name="title"><br>

        <label>Body:</label><br>
        <textarea name="body"></textarea><br>

        <button type="submit">Simpan</button>
    </form>
@endsection
```

### 4. Bikin Form Edit

File `resources/views/posts/edit.blade.php` — mirip `create`, bedanya: isi data lama + pakai `@method('PUT')`:

```blade
@extends('layouts.app')

@section('title', 'Edit Post')

@section('content')
    <h1>Edit Post</h1>

    @if ($errors->any())
        <ul style="color: red;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="/posts/{{ $post->id }}">
        @csrf
        @method('PUT')

        <label>Title:</label><br>
        <input type="text" name="title" value="{{ $post->title }}"><br>

        <label>Body:</label><br>
        <textarea name="body">{{ $post->body }}</textarea><br>

        <button type="submit">Update</button>
    </form>
@endsection
```

### 5. Update Halaman List — tambah link Create, Edit, Delete

Ubah `resources/views/posts/index.blade.php` jadi:

```blade
@extends('layouts.app')

@section('title', 'Daftar Post')

@section('content')
    <h1>Daftar Post</h1>

    <a href="/posts/create">+ Tambah Post</a>

    <ul>
        @foreach ($posts as $post)
            <li>
                <strong>{{ $post->title }}</strong>
                <p>{{ $post->body }}</p>

                <a href="/posts/{{ $post->id }}/edit">Edit</a>

                <form method="POST" action="/posts/{{ $post->id }}" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Yakin hapus?')">Delete</button>
                </form>
            </li>
        @endforeach
    </ul>
@endsection
```

> Delete pakai `<form>` (bukan `<a>`), soalnya `DELETE` cuma bisa dikirim lewat form, gak bisa lewat link biasa.

### 6. Cek hasilnya

1. Buka `http://127.0.0.1:8000/posts` — ada tombol **+ Tambah Post**.
2. Klik, isi form, submit kosong dulu → harus muncul error validasi.
3. Isi bener, submit → balik ke `/posts`, post baru muncul.
4. Klik **Edit** di salah satu post → form udah keisi data lama → ubah → Update → cek perubahan masuk.
5. Klik **Delete** → konfirmasi → post ilang dari list.

### Ringkasan Praktik Kelima

| Sebelum (Praktik 4) | Sesudah (Praktik 5) |
|---|---|
| Cuma **Read** (`/posts` doang) | **CRUD lengkap**: Create, Read, Update, Delete |
| Data ditambah manual lewat tinker | Data ditambah/diubah/dihapus lewat form web |
| Belum ada validasi | Ada validasi (`$request->validate()`) |
| `Post::find($id)` manual (kalau ada) | Route model binding (`Post $post` otomatis) |

> Ini CRUD pertama yang beneran "app", bukan cuma latihan. Murid yang udah ngerti PDO manual bakal langsung relate: `$_POST` → `$request`, `if (empty(...))` → `$request->validate()`, `header('Location: ...')` → `redirect(...)`.

## Praktik Keenam: Rapihin Tampilan pakai Tailwind CSS

Selama ini tampilan masih HTML polos. Laravel 13 (default project ini) udah **otomatis include Tailwind CSS v4 + Vite** — cek `package.json` udah ada `tailwindcss` & `@tailwindcss/vite`, dan `resources/css/app.css` udah `@import 'tailwindcss';`. Jadi gak perlu install apa-apa, cuma kurang 2 langkah: **load CSS-nya** + **jalanin Vite**.

### 1. Install dependency JS (kalau belum)

```bash
npm install
```

Ini download `node_modules/` (Vite, Tailwind, dll) sesuai `package.json`.

### 2. Load CSS di Layout

Buka `resources/views/layouts/app.blade.php`, tambah `@vite(...)` di dalam `<head>`:

```blade
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Laravel Intro')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 text-gray-800">
    ...
```

> `@vite(...)` itu directive khusus Laravel buat "nyambungin" Vite — di mode development, Vite nyuntik CSS/JS secara live (hot reload); di production, otomatis pakai file hasil `npm run build`.

### 3. Jalanin Vite dev server

Buka terminal **baru** (biarin `php artisan serve` tetep nyala di terminal lain), jalankan:

```bash
npm run dev
```

Biarin jalan terus selama development (mirip `php artisan serve`, dua-duanya jalan bareng).

### 4. Tambah class Tailwind di Navbar

Update navbar di `layouts/app.blade.php` biar lebih rapi:

```blade
<nav class="bg-white shadow px-6 py-4 flex gap-4">
    <a href="/hello" class="text-blue-600 hover:underline">Hello</a>
    <a href="/hello/Budi" class="text-blue-600 hover:underline">Hello Budi</a>
    <a href="/posts" class="text-blue-600 hover:underline">Posts</a>
</nav>

<main class="max-w-2xl mx-auto p-6">
    @yield('content')
</main>
```

### 5. Rapihin Halaman List Post

Update `resources/views/posts/index.blade.php`:

```blade
@extends('layouts.app')

@section('title', 'Daftar Post')

@section('content')
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Daftar Post</h1>
        <a href="/posts/create" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            + Tambah Post
        </a>
    </div>

    <div class="space-y-4">
        @foreach ($posts as $post)
            <div class="bg-white border rounded-lg p-4 shadow-sm">
                <h2 class="font-semibold text-lg">{{ $post->title }}</h2>
                <p class="text-gray-600">{{ $post->body }}</p>

                <div class="mt-2 flex gap-2">
                    <a href="/posts/{{ $post->id }}/edit" class="text-sm text-blue-600 hover:underline">Edit</a>

                    <form method="POST" action="/posts/{{ $post->id }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Yakin hapus?')"
                            class="text-sm text-red-600 hover:underline">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@endsection
```

### 6. Rapihin Form Create & Edit

Update `resources/views/posts/create.blade.php`:

```blade
@extends('layouts.app')

@section('title', 'Tambah Post')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Tambah Post</h1>

    @if ($errors->any())
        <ul class="bg-red-50 text-red-600 text-sm rounded p-3 mb-4">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="/posts" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium mb-1">Title</label>
            <input type="text" name="title" class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Body</label>
            <textarea name="body" rows="4" class="w-full border rounded px-3 py-2"></textarea>
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Simpan
        </button>
    </form>
@endsection
```

Update `resources/views/posts/edit.blade.php` — sama kayak `create`, bedanya: ada `@method('PUT')`, `action` pakai id post, dan input udah terisi data lama (`value="{{ $post->title }}"`):

```blade
@extends('layouts.app')

@section('title', 'Edit Post')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Edit Post</h1>

    @if ($errors->any())
        <ul class="bg-red-50 text-red-600 text-sm rounded p-3 mb-4">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="/posts/{{ $post->id }}" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium mb-1">Title</label>
            <input type="text" name="title" value="{{ $post->title }}" class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Body</label>
            <textarea name="body" rows="4" class="w-full border rounded px-3 py-2">{{ $post->body }}</textarea>
        </div>

        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
            Update
        </button>
    </form>
@endsection
```

### 7. Cek hasilnya

Refresh `http://127.0.0.1:8000/posts` — harus udah ada warna, spacing, button rapi (bukan HTML polos lagi). Pastiin `npm run dev` masih jalan di terminal — kalau dimatiin, CSS gak ke-load.

> Production nanti (kalau di-deploy): ganti `npm run dev` jadi `npm run build` sekali, hasil compile-nya dipakai langsung, gak perlu Vite server jalan terus.

### Ringkasan Praktik Keenam

| Sebelum (Praktik 1-5) | Sesudah (Praktik 6) |
|---|---|
| HTML polos, gak ada styling | Pakai Tailwind utility classes |
| `php artisan serve` doang | `php artisan serve` + `npm run dev` jalan bareng |
| `layouts/app.blade.php` belum load CSS | Ada `@vite(...)` di `<head>` |

> Tailwind cuma nambah `class="..."` di HTML/Blade — Controller, Model, Route sama sekali gak berubah. Ini nunjukin View itu independen dari logic (M & C).

## Praktik Ketujuh: Pindah dari SQLite ke MySQL (phpMyAdmin)

Sejauh ini database pakai **SQLite** (file `database/database.sqlite`) — enak buat belajar karena gak perlu setup server database. Sekarang pindah ke **MySQL** (lebih umum dipakai di kerjaan beneran), pakai **phpMyAdmin** bawaan Laragon.

Poin penting: kita cuma ganti **konfigurasi**, kode Controller/Model/Migration **gak ada yang berubah sama sekali**. Ini bukti Eloquent itu "database agnostic" — sama Model, bisa nyambung ke database apa aja.

### 1. Nyalain MySQL di Laragon

Buka Laragon → pastiin tombol **Start All** udah aktif (ijo), atau minimal service **MySQL** nyala. Cek di tray Laragon, klik kanan → harus ada centang di MySQL.

### 2. Bikin database baru lewat phpMyAdmin

1. Klik kanan tray Laragon → **Database** → phpMyAdmin bakal kebuka di browser (atau buka manual `http://localhost/phpmyadmin`).
2. Login (default Laragon: username `root`, password kosong).
3. Klik tab **Databases** di menu atas.
4. Di field "Create database", isi nama database, misal `novani_laravel`.
5. Klik **Create**. Database baru muncul di sidebar kiri (masih kosong, tabelnya nanti dibuat otomatis lewat migration).

### 3. Ubah konfigurasi `.env`

Buka file `.env` di root project. Cari baris ini:

```env
DB_CONNECTION=sqlite
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=laravel
# DB_USERNAME=root
# DB_PASSWORD=
```

Ganti (hapus `#` dan sesuaikan value) jadi:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=novani_laravel
DB_USERNAME=root
DB_PASSWORD=
```

> Ganti `novani_laravel` sesuai nama database yang dibuat di step 2. `DB_PASSWORD` dikosongin karena default MySQL Laragon gak pakai password.

### 4. Bersihin cache config

Laravel nge-cache config biar cepet, jadi perubahan `.env` kadang belum langsung kebaca. Jalankan:

```bash
php artisan config:clear
```

### 5. Jalankan migration ke database baru

```bash
php artisan migrate
```

Ini bakal bikin ulang semua tabel (`posts`, `users`, dll) di database MySQL yang baru, sesuai file-file di `database/migrations/`.

> Data lama yang ada di SQLite **gak ikut pindah otomatis** — database MySQL mulai dari kosong. Wajar buat konteks belajar. Kalau nanti pengen migrasi data beneran (bukan cuma struktur tabel), itu topik terpisah (`database seeding` / export-import manual).

### 6. Cek hasilnya

1. Refresh phpMyAdmin, klik database `novani_laravel` di sidebar — harus muncul tabel `posts`, `users`, `migrations`, dll.
2. Buka `http://127.0.0.1:8000/posts` — datanya kosong (wajar, tabel baru). Coba tambah post baru lewat form `/posts/create`.
3. Balik ke phpMyAdmin, klik tabel `posts` → tab **Browse** — data yang baru ditambah harus muncul di sini juga.

### Cheatsheet Troubleshooting

| Masalah | Solusi |
|---|---|
| `SQLSTATE[HY000] [1049] Unknown database` | Nama `DB_DATABASE` di `.env` gak sama persis kayak nama database di phpMyAdmin — cek typo |
| `SQLSTATE[HY000] [2002] No connection could be made` | MySQL di Laragon belum nyala — buka Laragon, klik Start All |
| Perubahan `.env` kayak belum kepakai | Jalankan `php artisan config:clear` |
| Tabel gak muncul di phpMyAdmin | Belum jalanin `php artisan migrate` setelah ganti `.env` |

### Ringkasan Praktik Ketujuh

| Sebelum (SQLite) | Sesudah (MySQL) |
|---|---|
| `DB_CONNECTION=sqlite`, data di file `database.sqlite` | `DB_CONNECTION=mysql`, data di server MySQL Laragon |
| Cek data lewat DB Browser for SQLite / tinker | Cek data lewat phpMyAdmin (Browse tabel) |
| Model, Controller, Migration | **Sama persis, gak berubah** |

> Ini nunjukin kekuatan Eloquent ORM: kode `Post::all()`, `Post::create()`, dll gak peduli database-nya SQLite, MySQL, atau PostgreSQL — tinggal ganti config `.env`, semua kode lain tetap jalan.

## Praktik Kedelapan: Login/Register pakai Laravel Breeze

Sejauh ini semua route `/posts` **public** — siapa aja bisa akses tanpa login. Sekarang tambah **Authentication** (Login/Register) pakai **Laravel Breeze**, paket resmi Laravel yang auto-generate halaman login/register + logic-nya, siap pakai.

Konsep baru: **Authentication** (siapa kamu?) vs **Authorization** (kamu boleh ngapain?) — Breeze ngurus yang pertama.

### 1. Install Breeze lewat Composer

```bash
composer require laravel/breeze --dev
```

Ini nambahin package Breeze ke project (`--dev` karena cuma dipakai pas development/scaffolding, bukan runtime).

### 2. Jalankan installer Breeze

```bash
php artisan breeze:install
```

Bakal muncul pilihan interaktif di terminal:

```
Which Breeze stack would you like to install?
> Blade with Alpine
  Livewire ...
  React ...
  Vue ...
  API only
```

Pilih **Blade with Alpine** (sesuai apa yang udah dipelajari — Blade template, bukan React/Vue). Tekan Enter.

Nanti ditanya lagi:
- **Dark mode** — pilih sesuai selera (default gapapa).
- **Testing framework: PHPUnit atau Pest** — pilih **PHPUnit**. Project ini udah default pakai PHPUnit (cek `phpunit.xml` & `composer.json`), jadi biar konsisten, gak usah pilih Pest.

Breeze otomatis bikin:
- Route login/register di `routes/auth.php`
- Controller di `app/Http/Controllers/Auth/`
- View di `resources/views/auth/` (`login.blade.php`, `register.blade.php`, dll) — udah pakai Tailwind
- Middleware `auth` siap pakai
- `resources/views/layouts/navigation.blade.php` — navbar bawaan Breeze (ada link Dashboard, Profile, Logout)

> ⚠️ **Penting:** installer Breeze juga **menimpa** file layout yang udah dipakai dari Praktik Ketiga. Lanjut ke step 3 di bawah buat benerin ini SEBELUM lanjut cek Login/Register — kalau dilewatin, nanti kena error `Undefined variable $slot`.

### 3. Perbaiki layout yang ketimpa Breeze

Installer Breeze ngubah `resources/views/layouts/app.blade.php` jadi versi baru yang pakai `{{ $slot }}` (gaya Blade component `<x-app-layout>`), beda sama layout `@yield('content')` yang udah dipakai sejak Praktik Ketiga. Kalau dibiarin, semua halaman lama (`hello`, `posts/*`) bakal error `Undefined variable $slot`.

**📁 File: `resources/views/layouts/app.blade.php`** — timpa seluruh isinya jadi:

```blade
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Laravel Intro')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 font-sans antialiased">
    @include('layouts.navigation')

    <main class="max-w-2xl mx-auto p-6">
        @yield('content')
    </main>
</body>

</html>
```

> Semua view yang udah ada (`hello.blade.php`, `posts/*.blade.php`) tetep pakai `@extends('layouts.app')` + `@section('content')` kayak biasa, **gak perlu diubah** — cuma layout-nya yang dibenerin.

Tapi ada 2 file bawaan Breeze yang didesain khusus buat layout lama (`<x-app-layout>` + `<x-slot>`), jadi ikut kena error juga. Convert dua-duanya ke gaya `@extends`/`@section`:

**📁 File: `resources/views/dashboard.blade.php`** — timpa jadi:

```blade
@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-4">
        {{ __('Dashboard') }}
    </h2>

    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
        <div class="p-6 text-gray-900">
            {{ __("You're logged in!") }}
        </div>
    </div>
@endsection
```

**📁 File: `resources/views/profile/edit.blade.php`** — timpa jadi:

```blade
@extends('layouts.app')

@section('title', 'Profile')

@section('content')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-4">
        {{ __('Profile') }}
    </h2>

    <div class="space-y-6">
        <div class="p-4 sm:p-8 bg-white shadow rounded-lg">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="p-4 sm:p-8 bg-white shadow rounded-lg">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="p-4 sm:p-8 bg-white shadow rounded-lg">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
@endsection
```

> Isi `@include(...)` di dalamnya (form update profile, password, delete account) gak perlu diubah — itu file partial terpisah (`resources/views/profile/partials/`), otomatis nempel ke `@section('content')` manapun yang manggil.

### 4. Install ulang dependency & migrate

Breeze nambah file baru, jadi perlu install ulang:

```bash
npm install
npm run dev
```

```bash
php artisan migrate
```

> Migration ini bakal jalanin migration `users` (kalau belum) — pastiin ini dijalanin setelah setup MySQL di Praktik Ketujuh.

### 5. Cek halaman Login & Register

Pastiin `php artisan serve` masih jalan, buka:

- `http://127.0.0.1:8000/register` — coba daftar akun baru.
- `http://127.0.0.1:8000/login` — coba login pakai akun yang baru dibuat.

Kalau berhasil, biasanya diarahin ke `/dashboard` (halaman bawaan Breeze).

> Cek juga di phpMyAdmin — buka tabel `users`, akun yang baru daftar harus muncul di sana (password otomatis di-hash, bukan disimpan plain text).

### 6. Kunci route `/posts` biar wajib login

> ⚠️ **Penting:** installer Breeze (step 2) otomatis nambah beberapa route bawaan ke `routes/web.php`: `dashboard` dan `profile.edit`/`profile.update`/`profile.destroy` (dipakai navbar Breeze buat halaman "Edit Profile"). **Jangan replace seluruh isi file**, tinggal tambahin route Post & middleware `auth` di bawahnya — kalau route-route itu ikut kehapus, nanti abis register/login bakal error `Route [dashboard] not defined` atau `Route [profile.edit] not defined`.

Sekarang lindungi CRUD Post — cuma user yang login yang boleh akses.

**📁 File: `routes/web.php`** — bungkus route `/posts/*` pakai middleware `auth`. Contoh isi lengkap file (kalau route bawaan Breeze udah kehapus, tambahin balik kayak di bawah):

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HelloController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/hello', [HelloController::class, 'index']);
Route::get('/hello/{name}', [HelloController::class, 'show']);

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/posts', [PostController::class, 'index']);
    Route::get('/posts/create', [PostController::class, 'create']);
    Route::post('/posts', [PostController::class, 'store']);
    Route::get('/posts/{post}/edit', [PostController::class, 'edit']);
    Route::put('/posts/{post}', [PostController::class, 'update']);
    Route::delete('/posts/{post}', [PostController::class, 'destroy']);
});

require __DIR__ . '/auth.php';
```

Penjelasan:
- `Route::middleware('auth')->group(function () { ... })` — semua route di dalam `{ }` ini wajib login dulu.
- Kalau belum login terus akses `/posts`, otomatis di-redirect ke `/login`.
- `ProfileController` udah otomatis dibuat sama installer Breeze di `app/Http/Controllers/ProfileController.php`, tinggal daftarin route-nya aja.
- `require __DIR__ . '/auth.php'` — Breeze naruh route login/register di file terpisah (`routes/auth.php`), baris ini yang "nyambungin" ke `web.php`. (Biasanya baris ini udah otomatis ke-tambah sama installer Breeze — cek dulu sebelum nambah manual, jangan sampai dobel.)

### 7. Cek hasilnya

1. **Logout dulu** (kalau lagi login) — cari tombol logout di navbar bawaan Breeze, atau buka `http://127.0.0.1:8000/logout` lewat form.
2. Coba akses `http://127.0.0.1:8000/posts` dalam keadaan logout — harus otomatis ke-redirect ke `/login`.
3. Login lagi — sekarang `/posts` bisa diakses normal.

### 8. Tambah link "Posts" di navbar bawaan Breeze

Breeze udah otomatis bikinin navbar sendiri di `resources/views/layouts/navigation.blade.php` (ada Dashboard, nama user, Profile, Logout). Layout kita (`layouts/app.blade.php`) manggil navbar ini lewat `@include('layouts.navigation')`. Tambah link **Posts** di situ, biar bisa pindah ke `/posts` langsung dari navbar.

**📁 File: `resources/views/layouts/navigation.blade.php`** — cari bagian **Navigation Links** (desktop menu):

```blade
<div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
        {{ __('Dashboard') }}
    </x-nav-link>
</div>
```

Tambah `<x-nav-link>` baru buat Posts, jadi:

```blade
<div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
        {{ __('Dashboard') }}
    </x-nav-link>

    <x-nav-link href="/posts" :active="request()->is('posts*')">
        {{ __('Posts') }}
    </x-nav-link>
</div>
```

Terus cari bagian **Responsive Navigation Menu** (buat tampilan mobile/hamburger), pola-nya mirip tapi pakai `<x-responsive-nav-link>`:

```blade
<div class="pt-2 pb-3 space-y-1">
    <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
        {{ __('Dashboard') }}
    </x-responsive-nav-link>
</div>
```

Tambah juga di sini:

```blade
<div class="pt-2 pb-3 space-y-1">
    <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
        {{ __('Dashboard') }}
    </x-responsive-nav-link>

    <x-responsive-nav-link href="/posts" :active="request()->is('posts*')">
        {{ __('Posts') }}
    </x-responsive-nav-link>
</div>
```

Penjelasan:
- `route('dashboard')` dipakai buat route `/posts` gak bisa, soalnya route `/posts` gak dikasih `->name(...)`. Makanya pakai `href="/posts"` langsung (URL manual).
- `:active="request()->is('posts*')"` — nge-cek apakah URL sekarang diawali `/posts` (termasuk `/posts/create`, `/posts/5/edit`, dll), buat kasih highlight/active state di link.
- `request()->routeIs('dashboard')` beda cara — itu ngecek berdasarkan **nama route** (`->name('dashboard')`), bukan URL. Dua-duanya valid, tinggal pilih sesuai ada/gaknya nama route.

### 9. Cek hasilnya

Refresh halaman manapun yang lagi login (`/dashboard`, `/posts`, dll) — navbar atas harus nampilin link **Posts** di samping **Dashboard**. Klik, harus pindah ke `/posts` dengan benar.

### Ringkasan File yang Ditambah/Diubah di Praktik Kedelapan

| File | Perubahan |
|---|---|
| `resources/views/layouts/app.blade.php` | Ditimpa balik ke gaya `@yield('content')` (step 3) |
| `resources/views/dashboard.blade.php` | Dikonversi dari `<x-app-layout>` ke `@extends`/`@section` (step 3) |
| `resources/views/profile/edit.blade.php` | Dikonversi dari `<x-app-layout>` ke `@extends`/`@section` (step 3) |
| `routes/web.php` | Tambah middleware `auth` buat route `/posts/*`, pastiin route `dashboard` & `profile.*` tetep ada (step 6) |
| `resources/views/layouts/navigation.blade.php` | Tambah link "Posts" di desktop & mobile nav (step 8) |

### Cheatsheet Troubleshooting

| Masalah | Solusi |
|---|---|
| `Class "Laravel\Breeze\BreezeServiceProvider" not found` | Jalankan `composer dump-autoload`, lalu ulang `php artisan breeze:install` |
| Halaman login/register tampil polos (gak ada style) | Pastiin `npm install` & `npm run dev` udah dijalanin ulang setelah install Breeze |
| Akses `/posts` gak ke-redirect ke login | Cek lagi `routes/web.php` — route harus dibungkus `Route::middleware('auth')->group(...)` |
| Error `Route [login] not defined` | Baris `require __DIR__ . '/auth.php';` belum ada / kehapus dari `routes/web.php` |
| Error `Undefined variable $slot` di halaman manapun | `resources/views/layouts/app.blade.php` ketimpa installer Breeze jadi versi `<x-app-layout>` — perbaiki sesuai step 3 |
| `Route [dashboard] not defined` / `Route [profile.edit] not defined` abis register/login | Route bawaan Breeze ikut kehapus pas edit `routes/web.php` — cek lagi contoh lengkap di step 6 |

### Ringkasan Praktik Kedelapan

| Sebelum (Praktik 1-7) | Sesudah (Praktik 8) |
|---|---|
| `/posts` bisa diakses siapa aja | `/posts` wajib login dulu |
| Belum ada konsep user/akun | Ada Register, Login, Logout (tabel `users`) |
| Semua route "flat" di `web.php` | Route dikelompokkin pakai `middleware('auth')->group()` |

> Ini pondasi buat topik selanjutnya: **role admin vs user** (nambah kolom `role` di tabel `users`) dan **relasi Post-User** (`Post belongsTo User`, biar user cuma bisa edit post miliknya sendiri) — dua-duanya baru masuk akal setelah ada Authentication.

## Praktik Kesembilan: Relasi Post-User

Sekarang siapa aja yang login bisa Edit/Delete **post siapapun**, bukan cuma post miliknya sendiri. Praktik ini benerin itu — tiap Post "dimiliki" oleh satu User (`Post belongsTo User`), dan tiap User bisa punya banyak Post (`User hasMany Post`).

Konsep baru: **relasi database** (`belongsTo`/`hasMany`, udah disinggung di [Eloquent ORM](#eloquent-orm)) dipraktikin beneran, plus dasar **Authorization** (ngecek kepemilikan data sebelum ngizinin aksi).

### 1. Bikin migration buat nambah kolom `user_id`

```bash
php artisan make:migration add_user_id_to_posts_table --table=posts
```

**📁 File: `database/migrations/..._add_user_id_to_posts_table.php`** — edit method `up()` & `down()`:

```php
public function up(): void
{
    Schema::table('posts', function (Blueprint $table) {
        $table->foreignId('user_id')->after('id')->constrained()->cascadeOnDelete();
    });
}

public function down(): void
{
    Schema::table('posts', function (Blueprint $table) {
        $table->dropForeign(['user_id']);
        $table->dropColumn('user_id');
    });
}
```

Penjelasan:
- `$table->foreignId('user_id')` — bikin kolom `user_id` (integer), otomatis nyambung ke tabel `users` kolom `id`.
- `->constrained()` — bikin ini jadi **foreign key** beneran (database bakal nolak insert kalau `user_id`-nya gak ada di tabel `users`).
- `->cascadeOnDelete()` — kalau User dihapus, semua Post miliknya ikut kehapus otomatis (biar gak ada Post "yatim" tanpa pemilik).

### 2. Jalankan migration

```bash
php artisan migrate
```

> Kalau ada Post lama yang udah dibuat sebelum kolom `user_id` ada, kolom itu bakal kosong (`NULL`) buat data lama. Kalau `constrained()` nolak karena ada data lama, gampangnya hapus dulu data lama: buka phpMyAdmin → tabel `posts` → Empty, baru migrate ulang.
>
> ⚠️ Kalau tetep ada Post lama yang `user_id`-nya `NULL` (gak dihapus), nanti pas buka `/posts` bakal error `Attempt to read property "name" on null` — soalnya `$post->user` jadi `null` buat post itu. Solusi cepat: hapus post lama itu di phpMyAdmin. Solusi lebih aman (dipakai di kode step 5 di bawah): pakai `{{ $post->user->name ?? 'Tidak diketahui' }}` biar gak crash walau ada post tanpa pemilik.

**Troubleshooting umum di step ini** (urutan dari yang paling sering ke jarang):

**A. `SQLSTATE[42S22]: Column not found: 1054 Unknown column 'user_id'`** — muncul pas nge-test Create Post, bukan pas migrate. Artinya migration belum kejalanin sama sekali, atau isi `up()` di file migration masih kosong (`//`, belum diisi kode step 1). Cek dulu isi filenya udah bener, baru `php artisan migrate` lagi.

**B. `php artisan migrate` bilang "Nothing to migrate"** padahal kolom `user_id` belum ada — ini kejadian kalau migration sempet kejalanin **pas isinya masih kosong** (kejadian di kasus A di atas terus buru-buru migrate duluan sebelum isi kode). Laravel udah nyatet migration ini "selesai" di tabel `migrations`, padahal gak ngapa-ngapain. Fix: buka phpMyAdmin → tabel **`migrations`** → cari row `migration` = nama file migration kamu (misal `2026_07_07_035917_add_user_id_to_posts_table`) → **hapus row itu** → jalankan `php artisan migrate` lagi.

**C. `SQLSTATE[23000]: Cannot add or update a child row: a foreign key constraint fails`** — ini muncul kalau tabel `posts` **masih ada data lama** (post yang dibuat sebelum kolom `user_id` ada). MySQL nyoba isi kolom baru itu otomatis pakai `0`, tapi gak ada User dengan id `0`, jadi foreign key nolak. Fix: kosongin tabel `posts` dulu (Empty di phpMyAdmin), baru migrate ulang.

**D. Kalau bingung / kejebak berulang** — paling gampang reset total pakai:

```bash
php artisan migrate:fresh
```

Ini drop **semua** tabel (termasuk `users`) terus bikin ulang dari nol sesuai migration yang ada. Konsekuensinya: **semua akun & data lama ilang**, jadi harus `/register` ulang. Buat konteks belajar ini aman dilakuin, tapi di project beneran (production) jangan pernah pakai `migrate:fresh` — itu bakal ngilangin data user asli.

### 3. Update Model — tambah relasi

**📁 File: `app/Models/Post.php`**:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = ['title', 'body', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

**📁 File: `app/Models/User.php`** — tambah method `posts()`:

```php
public function posts()
{
    return $this->hasMany(Post::class);
}
```

> `belongsTo` ditaruh di sisi yang "punya" foreign key (`Post` punya `user_id`). `hasMany` ditaruh di sisi sebaliknya (`User` "punya banyak" Post). Ini kebalikan dari kolomnya sendiri — gampang keliru, hafalin: **yang nyimpen id-nya = `belongsTo`**.

### 4. Update Controller — otomatis isi `user_id` pas create

**📁 File: `app/Http/Controllers/PostController.php`** — ubah method `store()`:

```php
public function store(Request $request)
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'body' => 'required|string',
    ]);

    $validated['user_id'] = auth()->id();

    Post::create($validated);

    return redirect('/posts');
}
```

`auth()->id()` — ambil id User yang lagi login. Ini ditambahin manual ke `$validated` (bukan dari form), soalnya user gak boleh bisa "milih" jadi siapa lewat form — harus otomatis dari sesi login.

### 5. Tampilin nama pemilik + sembunyiin tombol Edit/Delete kalau bukan pemilik

**📁 File: `resources/views/posts/index.blade.php`** — update jadi:

```blade
@extends('layouts.app')

@section('title', 'Daftar Post')

@section('content')
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Daftar Post</h1>
        <a href="/posts/create" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            + Tambah Post
        </a>
    </div>

    <div class="space-y-4">
        @foreach ($posts as $post)
            <div class="bg-white border rounded-lg p-4 shadow-sm">
                <h2 class="font-semibold text-lg">{{ $post->title }}</h2>
                <p class="text-gray-600">{{ $post->body }}</p>
                <p class="text-xs text-gray-400 mt-1">Ditulis oleh {{ $post->user->name ?? 'Tidak diketahui' }}</p>

                @if ($post->user_id === auth()->id())
                    <div class="mt-2 flex gap-2">
                        <a href="/posts/{{ $post->id }}/edit"
                            class="text-sm bg-blue-100 text-blue-700 px-3 py-1 rounded hover:bg-blue-200">
                            Edit
                        </a>

                        <form method="POST" action="/posts/{{ $post->id }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Yakin hapus?')"
                                class="text-sm bg-red-100 text-red-700 px-3 py-1 rounded hover:bg-red-200">
                                Hapus
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
@endsection
```

Penjelasan:
- `{{ $post->user->name }}` — ini **eager loading otomatis** Eloquent: manggil `$post->user` langsung nge-query tabel `users` ambil data pemiliknya, berkat relasi `belongsTo` di step 3.
- `@if ($post->user_id === auth()->id())` — tombol Edit/Delete cuma muncul kalau `user_id` di post itu sama dengan id user yang lagi login.

### 6. Proteksi juga di Controller (jangan cuma sembunyiin tombol!)

Sembunyiin tombol doang **gak cukup** — orang iseng masih bisa langsung akses `/posts/5/edit` walau itu bukan post-nya. Tambah pengecekan di Controller.

**📁 File: `app/Http/Controllers/PostController.php`** — update `edit()`, `update()`, `destroy()`:

```php
public function edit(Post $post)
{
    if ($post->user_id !== auth()->id()) {
        abort(403);
    }

    return view('posts.edit', ['post' => $post]);
}

public function update(Request $request, Post $post)
{
    if ($post->user_id !== auth()->id()) {
        abort(403);
    }

    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'body' => 'required|string',
    ]);

    $post->update($validated);

    return redirect('/posts');
}

public function destroy(Post $post)
{
    if ($post->user_id !== auth()->id()) {
        abort(403);
    }

    $post->delete();

    return redirect('/posts');
}
```

`abort(403)` — nampilin halaman error "403 Forbidden" bawaan Laravel, nolak akses tanpa nge-crash aplikasi. Ini contoh **Authorization** paling dasar: Authentication udah jawab "siapa kamu", ini jawab "kamu boleh ngapain".

> Cara ini (`if` manual di tiap method) namanya cukup buat belajar, tapi di project beneran biasanya dirapikan pakai **Laravel Policy** (`php artisan make:policy PostPolicy --model=Post`) — biar logic "siapa boleh ngapain" satu tempat aja, gak diulang di 3 method. Ini topik lanjutan kalau mau didalami lagi nanti.

### 7. Cek hasilnya

1. Buka `/posts` — tiap post sekarang nampilin **"Ditulis oleh [nama]"**.
2. Post yang kamu buat sendiri — ada tombol Edit/Hapus.
3. Coba bikin akun baru (`/register`), login pakai akun itu — post dari akun pertama tadi **gak ada tombol Edit/Hapus**-nya lagi.
4. Coba akses langsung `http://127.0.0.1:8000/posts/1/edit` pakai akun kedua (ganti `1` sesuai id post akun pertama) — harus muncul halaman **403 Forbidden**.

### Ringkasan File yang Ditambah/Diubah di Praktik Kesembilan

| File | Perubahan |
|---|---|
| `database/migrations/..._add_user_id_to_posts_table.php` | Migration baru, tambah kolom `user_id` + foreign key |
| `app/Models/Post.php` | Tambah `user_id` ke `$fillable`, tambah method `user()` (`belongsTo`) |
| `app/Models/User.php` | Tambah method `posts()` (`hasMany`) |
| `app/Http/Controllers/PostController.php` | `store()` isi `user_id` otomatis; `edit()`, `update()`, `destroy()` dikasih pengecekan kepemilikan |
| `resources/views/posts/index.blade.php` | Tampilin nama penulis, sembunyiin tombol Edit/Hapus kalau bukan pemilik |

### Ringkasan Praktik Kesembilan

| Sebelum (Praktik 1-8) | Sesudah (Praktik 9) |
|---|---|
| Post gak punya pemilik | Tiap Post `belongsTo` satu User |
| Siapa aja login bisa edit/hapus post siapapun | Cuma pemilik yang bisa edit/hapus post-nya |
| Belum ada Authorization | Ada pengecekan kepemilikan (`abort(403)`) |

> Ini jadi fondasi buat **role admin vs user** — admin nanti bisa "skip" pengecekan `user_id` ini dan boleh edit/hapus post siapa aja, user biasa tetep kebatas post miliknya sendiri.

## Praktik Kesepuluh: Role Admin vs User

Sekarang semua user "setara" — cuma bisa edit/hapus post miliknya sendiri. Praktik ini nambah 2 level: **admin** (bisa edit/hapus post siapa aja + kelola daftar user) dan **user** (default, tetap kebatas post sendiri kayak Praktik 9).

Konsep baru: **middleware custom** (buat ngeblok akses berdasarkan role) dan **route grouping dengan prefix** (`/admin/...`).

### 1. Migration — tambah kolom `role` ke tabel `users`

```bash
php artisan make:migration add_role_to_users_table --table=users
```

**📁 File: `database/migrations/..._add_role_to_users_table.php`**:

```php
public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('role')->default('user')->after('email');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('role');
    });
}
```

Jalankan:

```bash
php artisan migrate
```

Semua user (lama & baru) otomatis dapet `role = 'user'` (default). Belum ada admin sama sekali.

> ⚠️ **Penting soal keamanan:** kolom `role` **sengaja gak ditambahin** ke daftar `Fillable` di `app/Models/User.php`. Kalau ditambahin, orang jahat bisa nyelundupin `role=admin` lewat form register (mass assignment exploit) dan langsung jadi admin sendiri! Nanti buat ubah role, kita set manual pakai `$user->role = '...'; $user->save();` — bukan lewat `update($request->all())`.

### 2. Jadiin akun kamu sendiri admin (lewat Tinker)

Karena belum ada UI buat ini, jadiin akun pertama kamu admin manual dulu:

```bash
php artisan tinker
```

```php
use App\Models\User;
$user = User::find(1);
$user->role = 'admin';
$user->save();
exit
```

> Ganti `1` sesuai id akun kamu (cek di phpMyAdmin, tabel `users`, kolom `id`).

### 3. Bikin Middleware `EnsureUserIsAdmin`

Middleware ini nge-cek: kalau yang akses bukan admin, tolak (`403`).

```bash
php artisan make:middleware EnsureUserIsAdmin
```

**📁 File: `app/Http/Middleware/EnsureUserIsAdmin.php`**:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->role !== 'admin') {
            abort(403, 'Halaman ini khusus admin.');
        }

        return $next($request);
    }
}
```

### 4. Daftarin middleware-nya

**📁 File: `bootstrap/app.php`** — cari bagian `->withMiddleware(...)`, tambah alias:

```php
use App\Http\Middleware\EnsureUserIsAdmin;

->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'admin' => EnsureUserIsAdmin::class,
    ]);
})
```

> Laravel 11/12 (versi project ini) gak pakai `app/Http/Kernel.php` lagi kayak versi lama — semua konfigurasi middleware didaftarin di `bootstrap/app.php`.

### 5. Bikin Controller buat User Management

```bash
php artisan make:controller Admin/UserController
```

**📁 File: `app/Http/Controllers/Admin/UserController.php`**:

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();

        return view('admin.users.index', ['users' => $users]);
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:user,admin',
        ]);

        $user->role = $request->role;
        $user->save();

        return redirect('/admin/users');
    }
}
```

### 6. Tambah Route khusus admin

**📁 File: `routes/web.php`** — tambah di bawah group `auth` yang udah ada:

```php
use App\Http\Controllers\Admin\UserController as AdminUserController;

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::patch('/users/{user}', [AdminUserController::class, 'updateRole'])->name('users.updateRole');
});
```

Penjelasan:
- `->middleware(['auth', 'admin'])` — dua lapis: harus login DULU (`auth`), baru dicek harus admin (`admin`, dari middleware yang dibikin di step 3).
- `->prefix('admin')` — semua URL di dalam group ini otomatis diawali `/admin/...` (jadi `/admin/users`, bukan cuma `/users`).
- `->name('admin.')` — semua route dikasih nama diawali `admin.` (jadi `admin.users.index`, `admin.users.updateRole`), biar gampang dipanggil di Blade pakai `route(...)`.

### 7. Bikin View User Management

**📁 File: `resources/views/admin/users/index.blade.php`** (bikin folder `admin/users/` dulu kalau belum ada):

```blade
@extends('layouts.app')

@section('title', 'Kelola User')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Kelola User</h1>

    <div class="bg-white border rounded-lg overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-2">Nama</th>
                    <th class="px-4 py-2">Email</th>
                    <th class="px-4 py-2">Role</th>
                    <th class="px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ $user->name }}</td>
                        <td class="px-4 py-2">{{ $user->email }}</td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 rounded text-xs {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td class="px-4 py-2">
                            <form method="POST" action="/admin/users/{{ $user->id }}" class="flex gap-2 items-center">
                                @csrf
                                @method('PATCH')

                                <select name="role" class="border rounded text-sm px-2 py-1">
                                    <option value="user" @selected($user->role === 'user')>user</option>
                                    <option value="admin" @selected($user->role === 'admin')>admin</option>
                                </select>

                                <button type="submit" class="text-sm bg-blue-100 text-blue-700 px-3 py-1 rounded hover:bg-blue-200">
                                    Simpan
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
```

`@selected(...)` — Blade directive buat nge-set `selected` di `<option>` otomatis kalau kondisinya `true`. Jadi dropdown role langsung nunjukin role yang sekarang aktif.

### 8. Tambah link "User Management" di navbar (khusus admin)

**📁 File: `resources/views/layouts/navigation.blade.php`** — di bagian **Navigation Links** (desktop), tambah link yang cuma keliatan buat admin:

```blade
<div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
        {{ __('Dashboard') }}
    </x-nav-link>

    <x-nav-link href="/posts" :active="request()->is('posts*')">
        {{ __('Posts') }}
    </x-nav-link>

    @if (auth()->user()->role === 'admin')
        <x-nav-link href="/admin/users" :active="request()->is('admin/users*')">
            {{ __('User Management') }}
        </x-nav-link>
    @endif
</div>
```

Tambah juga versi mobile-nya di bagian **Responsive Navigation Menu**, pola sama tapi pakai `<x-responsive-nav-link>`.

### 9. Kasih admin akses edit/hapus SEMUA post

Sekarang admin masih kebatas kayak Praktik 9 (cuma bisa edit post sendiri). Update pengecekan di Controller & View biar admin bisa bypass.

**📁 File: `app/Http/Controllers/PostController.php`** — update method `edit()`, `update()`, `destroy()`:

```php
public function edit(Post $post)
{
    if ($post->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
        abort(403);
    }

    return view('posts.edit', ['post' => $post]);
}

public function update(Request $request, Post $post)
{
    if ($post->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
        abort(403);
    }

    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'body' => 'required|string',
    ]);

    $post->update($validated);

    return redirect('/posts');
}

public function destroy(Post $post)
{
    if ($post->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
        abort(403);
    }

    $post->delete();

    return redirect('/posts');
}
```

**📁 File: `resources/views/posts/index.blade.php`** — update kondisi nampilin tombol Edit/Hapus:

```blade
@if ($post->user_id === auth()->id() || auth()->user()->role === 'admin')
    <div class="mt-2 flex gap-2">
        <a href="/posts/{{ $post->id }}/edit"
            class="text-sm bg-blue-100 text-blue-700 px-3 py-1 rounded hover:bg-blue-200">
            Edit
        </a>

        <form method="POST" action="/posts/{{ $post->id }}">
            @csrf
            @method('DELETE')
            <button type="submit" onclick="return confirm('Yakin hapus?')"
                class="text-sm bg-red-100 text-red-700 px-3 py-1 rounded hover:bg-red-200">
                Hapus
            </button>
        </form>
    </div>
@endif
```

### 10. Cek hasilnya

1. Login pakai akun admin (yang di-set di step 2) — navbar harus nampilin link **User Management**.
2. Buka `/admin/users` — harus muncul tabel semua user, ada dropdown role tiap baris.
3. Ubah role salah satu user jadi `admin`, klik Simpan — refresh, badge role-nya harus berubah.
4. Login pakai user **biasa** (bukan admin) — link User Management **gak keliatan** di navbar.
5. Coba akses langsung `http://127.0.0.1:8000/admin/users` pakai akun biasa — harus muncul **403 Forbidden**.
6. Balik login sebagai admin, buka `/posts` — tombol Edit/Hapus harus muncul di **semua** post, termasuk punya user lain.

### Cheatsheet Troubleshooting

| Masalah | Solusi |
|---|---|
| `Call to a member function role() on null` / error soal `auth()->user()` | Pastiin route dibungkus middleware `auth` dulu sebelum `admin` — urutannya `['auth', 'admin']`, bukan `['admin']` doang |
| Route `/admin/users` gak ke-block buat user biasa | Cek `bootstrap/app.php` — alias `admin` harus kedaftar bener, dan route di `web.php` harus pakai `->middleware(['auth', 'admin'])` |
| Ubah role gak nyimpen / gak ada efek | Pastiin form pakai `@method('PATCH')` dan route-nya `Route::patch(...)`, bukan `Route::put(...)` atau `Route::post(...)` |
| Semua user ke-set jadi admin abis daftar (mass assignment exploit) | Jangan pernah tambahin `role` ke `Fillable`/`$fillable` di `User.php` — set role manual pakai `$user->role = ...; $user->save();` |
| Link User Management error `Attempt to read property "role" on null` di navbar | Pastiin `@if (auth()->user()->role === 'admin')` cuma dipanggil di halaman yang udah pasti user login (di dalam layout yang di-`@include` cuma buat halaman ber-auth) |

### Ringkasan File yang Ditambah/Diubah di Praktik Kesepuluh

| File | Perubahan |
|---|---|
| `database/migrations/..._add_role_to_users_table.php` | Migration baru, tambah kolom `role` (default `user`) |
| `app/Http/Middleware/EnsureUserIsAdmin.php` | Middleware baru, cek `role === 'admin'` |
| `bootstrap/app.php` | Daftarin alias middleware `admin` |
| `app/Http/Controllers/Admin/UserController.php` | Controller baru — `index()` (list user), `updateRole()` (ubah role) |
| `routes/web.php` | Tambah group route `/admin/users` pakai middleware `['auth', 'admin']` |
| `resources/views/admin/users/index.blade.php` | View baru — tabel user + form ubah role |
| `resources/views/layouts/navigation.blade.php` | Tambah link "User Management", cuma keliatan buat admin |
| `app/Http/Controllers/PostController.php` | `edit()`, `update()`, `destroy()` — admin bisa bypass pengecekan kepemilikan |
| `resources/views/posts/index.blade.php` | Tombol Edit/Hapus muncul kalau pemilik **atau** admin |

### Ringkasan Praktik Kesepuluh

| Sebelum (Praktik 1-9) | Sesudah (Praktik 10) |
|---|---|
| Semua user setara, cuma bisa edit post sendiri | Ada 2 role: `admin` (bisa semua) dan `user` (kebatas post sendiri) |
| Belum ada middleware custom | Ada `EnsureUserIsAdmin` buat proteksi route khusus admin |
| Belum ada halaman kelola user | Ada `/admin/users` — admin bisa liat & ubah role semua user |

> Ini nutup topik Authentication & Authorization dasar. Next yang udah direncanain: **slug + halaman single post** (`/posts/judul-post` ganti `/posts/{id}`), lalu **upload/link gambar** buat tiap Post.

## Praktik Kesebelas: Lengkapi CRUD User Management (Tambah & Hapus User)

Praktik 10 baru bikin **Read** (list user) dan **Update** (ubah role). Sekarang lengkapin jadi CRUD penuh — admin bisa **Create** (tambah user baru manual) dan **Delete** (hapus akun user).

### 1. Tambah Route

**📁 File: `routes/web.php`** — tambah 2 route baru di dalam group `admin`:

```php
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::patch('/users/{user}', [AdminUserController::class, 'updateRole'])->name('users.updateRole');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
});
```

### 2. Update Controller — tambah `create()`, `store()`, `destroy()`

**📁 File: `app/Http/Controllers/Admin/UserController.php`**:

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();

        return view('admin.users.index', ['users' => $users]);
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|in:user,admin',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect('/admin/users');
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:user,admin',
        ]);

        $user->role = $request->role;
        $user->save();

        return redirect('/admin/users');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect('/admin/users')->with('error', 'Gak bisa hapus akun sendiri.');
        }

        $user->delete();

        return redirect('/admin/users');
    }
}
```

Penjelasan:
- `Hash::make($validated['password'])` — password **wajib** di-hash sebelum disimpan, jangan pernah simpan password mentah/plain text ke database.
- `'email' => 'required|email|unique:users,email'` — validasi `unique` mastiin gak ada 2 user pakai email yang sama.
- Method `store()` di sini pakai `User::create($validated)` langsung, termasuk `role`. Ini **beda** dari form `/register` biasa (yang gak boleh terima `role` dari user biasa) — di sini aman karena yang akses cuma admin (udah dijaga middleware `admin`), jadi khusus di Controller ini boleh set `role` manual.
- `destroy()` ada pengecekan `$user->id === auth()->id()` — biar admin gak bisa gak sengaja hapus akunnya sendiri (bisa bikin gak ada admin sama sekali).

> ⚠️ Ingat dari Praktik 9: kolom `user_id` di tabel `posts` pakai `->cascadeOnDelete()`. Artinya kalau User dihapus di sini, **semua Post miliknya ikut kehapus otomatis**. Ini perilaku yang disengaja (biar gak ada Post "yatim"), tapi penting buat dipahami sebelum klik Hapus.

### 3. Bikin View Form Tambah User

**📁 File: `resources/views/admin/users/create.blade.php`**:

```blade
@extends('layouts.app')

@section('title', 'Tambah User')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Tambah User</h1>

    @if ($errors->any())
        <ul class="bg-red-50 text-red-600 text-sm rounded p-3 mb-4">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="/admin/users" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium mb-1">Nama</label>
            <input type="text" name="name" value="{{ old('name') }}" class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Password</label>
            <input type="password" name="password" class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Role</label>
            <select name="role" class="w-full border rounded px-3 py-2">
                <option value="user">user</option>
                <option value="admin">admin</option>
            </select>
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Simpan
        </button>
    </form>
@endsection
```

`old('name')` / `old('email')` — kalau validasi gagal (misal email udah kepake), form ke-render ulang dengan **data yang tadi diketik tetep ada** (Laravel otomatis nyimpen input lama), jadi admin gak perlu ngetik ulang dari nol. (Sengaja gak dipakai buat `password`, alasan keamanan — password gak pernah "diinget balik".)

### 4. Update View List — tambah tombol Tambah User & Hapus

**📁 File: `resources/views/admin/users/index.blade.php`** — update jadi:

```blade
@extends('layouts.app')

@section('title', 'Kelola User')

@section('content')
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Kelola User</h1>
        <div class="flex items-center gap-4">
            <span class="text-sm text-gray-500">{{ $users->count() }} user terdaftar</span>
            <a href="/admin/users/create" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                + Tambah User
            </a>
        </div>
    </div>

    @if (session('error'))
        <div class="bg-red-50 text-red-600 text-sm rounded p-3 mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white border rounded-lg shadow-sm overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wide">
                <tr>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">Role</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $user->name }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $user->email }}</td>
                        <td class="px-4 py-3">
                            <span
                                class="inline-block px-2 py-1 rounded-full text-xs font-medium {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2 items-center">
                                <form method="POST" action="/admin/users/{{ $user->id }}"
                                    class="flex gap-2 items-center">
                                    @csrf
                                    @method('PATCH')

                                    <select name="role"
                                        class="border rounded text-sm pl-2 pr-6 py-1.5 bg-white min-w-[90px]">
                                        <option value="user" @selected($user->role === 'user')>user</option>
                                        <option value="admin" @selected($user->role === 'admin')>admin</option>
                                    </select>

                                    <button type="submit"
                                        class="text-sm bg-blue-600 text-white px-3 py-1.5 rounded hover:bg-blue-700">
                                        Simpan
                                    </button>
                                </form>

                                @if ($user->id !== auth()->id())
                                    <form method="POST" action="/admin/users/{{ $user->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Yakin hapus user ini? Semua post miliknya ikut kehapus.')"
                                            class="text-sm bg-red-100 text-red-700 px-3 py-1.5 rounded hover:bg-red-200">
                                            Hapus
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
```

Penjelasan:
- `@if ($user->id !== auth()->id())` — tombol Hapus disembunyiin buat baris akun diri sendiri (selaras sama pengecekan di Controller step 2 — dua lapis proteksi, sama kayak pola di Praktik 9).
- `session('error')` — nampilin pesan error dari `->with('error', ...)` di Controller (misal pas gagal hapus akun sendiri).
- `confirm('Yakin hapus...')` — dialog konfirmasi browser sebelum submit form Delete, mencegah klik gak sengaja.

### 5. Cek hasilnya

1. Buka `/admin/users` — ada tombol **+ Tambah User** di kanan atas.
2. Klik, isi form (nama, email, password, role), submit — user baru harus muncul di list.
3. Coba submit form kosong / email yang udah kepake — harus muncul pesan error validasi.
4. Klik **Hapus** di salah satu user (bukan akun sendiri) — konfirmasi — user hilang dari list.
5. Cek baris akun kamu sendiri — tombol **Hapus** gak ada di situ.
6. (Opsional) Coba bikin user baru dari akun ini punya beberapa Post, terus hapus usernya — cek di phpMyAdmin tabel `posts`, post-post itu harus ikut hilang (`cascadeOnDelete`).

### Ringkasan File yang Ditambah/Diubah di Praktik Kesebelas

| File | Perubahan |
|---|---|
| `routes/web.php` | Tambah route `GET /admin/users/create`, `POST /admin/users`, `DELETE /admin/users/{user}` |
| `app/Http/Controllers/Admin/UserController.php` | Tambah method `create()`, `store()`, `destroy()` |
| `resources/views/admin/users/create.blade.php` | View baru — form tambah user |
| `resources/views/admin/users/index.blade.php` | Tambah tombol "+ Tambah User" dan tombol "Hapus" per baris |

### Ringkasan Praktik Kesebelas

| Sebelum (Praktik 10) | Sesudah (Praktik 11) |
|---|---|
| User Management cuma Read + Update | User Management jadi CRUD penuh (Create, Read, Update, Delete) |
| User baru cuma bisa masuk lewat `/register` | Admin bisa tambah user manual, termasuk langsung set role |
| Gak ada cara hapus akun dari UI | Admin bisa hapus user (kecuali akunnya sendiri) |

## Praktik Kedua Belas: Slug + Single Post View

Sekarang `/posts` cuma nampilin list, klik judulnya gak ngapa-ngapain. URL detail kalau ada juga bakal pakai angka (`/posts/5`). Praktik ini nambah halaman detail 1 post, dengan URL yang lebih enak dibaca pakai **slug** (`/posts/belajar-laravel-itu-seru`).

Konsep baru: **slug** (versi URL-friendly dari judul, huruf kecil + strip), dan **route model binding custom** (`{post:slug}` — Laravel resolve Model berdasarkan kolom `slug`, bukan `id`).

### 1. Migration — tambah kolom `slug`

```bash
php artisan make:migration add_slug_to_posts_table --table=posts
```

**📁 File: `database/migrations/..._add_slug_to_posts_table.php`**:

```php
public function up(): void
{
    Schema::table('posts', function (Blueprint $table) {
        $table->string('slug')->unique()->after('title');
    });
}

public function down(): void
{
    Schema::table('posts', function (Blueprint $table) {
        $table->dropColumn('slug');
    });
}
```

`->unique()` — mastiin gak ada 2 post dengan slug yang sama (soalnya slug dipakai buat cari post di URL, harus unik).

### 2. Jalankan migration

```bash
php artisan migrate
```

> ⚠️ Kalau ada Post lama yang udah ada sebelum kolom `slug` ditambah, migration ini bisa gagal (mirip kejadian di Praktik 9) karena MySQL nyoba isi slug kosong buat semua baris, tapi `unique()` nolak kalau lebih dari 1 baris sama-sama kosong. Kalau gagal: kosongin tabel `posts` di phpMyAdmin dulu, baru migrate ulang. Atau paling gampang `php artisan migrate:fresh` (inget: ini reset semua tabel, harus `/register` ulang).

### 3. Update Model — auto-generate slug dari title

**📁 File: `app/Models/Post.php`**:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Post extends Model
{
    protected $fillable = ['title', 'body', 'user_id'];

    protected static function booted(): void
    {
        static::creating(function (Post $post) {
            $post->slug = static::generateUniqueSlug($post->title);
        });
    }

    protected static function generateUniqueSlug(string $title): string
    {
        $slug = Str::slug($title);
        $original = $slug;
        $count = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = $original . '-' . $count;
            $count++;
        }

        return $slug;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

Penjelasan:
- `static::creating(function (Post $post) { ... })` — ini **Model Event**: kode di dalamnya otomatis kejalan tiap kali ada Post baru mau disimpan (`Post::create(...)`), sebelum data masuk database. Gak perlu manggil manual di Controller.
- `Str::slug($title)` — helper Laravel, ubah teks jadi format URL: lowercase, spasi jadi strip. Contoh: `"Belajar Laravel Itu Seru!"` → `"belajar-laravel-itu-seru"`.
- `generateUniqueSlug()` — ngecek slug udah dipakai apa belum. Kalau ada 2 post judulnya sama persis (misal 2x "Hello World"), slug kedua otomatis jadi `hello-world-1`, bukan dobel `hello-world`.

### 4. Tambah Route buat halaman detail

**📁 File: `routes/web.php`** — tambah route baru **setelah** `/posts/create` (biar gak ketuker sama route lain):

```php
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/posts', [PostController::class, 'index']);
    Route::get('/posts/create', [PostController::class, 'create']);
    Route::post('/posts', [PostController::class, 'store']);
    Route::get('/posts/{post:slug}', [PostController::class, 'show']);
    Route::get('/posts/{post}/edit', [PostController::class, 'edit']);
    Route::put('/posts/{post}', [PostController::class, 'update']);
    Route::delete('/posts/{post}', [PostController::class, 'destroy']);
});
```

Penjelasan:
- `{post:slug}` — sintaks route model binding custom. Biasanya `{post}` nyari Post berdasarkan `id` (primary key default), tapi `{post:slug}` bilang ke Laravel "cari berdasarkan kolom `slug`, bukan `id`".
- Route ini **harus** ditaruh setelah `/posts/create` (URL statis) — kalau kebalik, Laravel bakal nganggep `create` itu isi dari `{post:slug}` dan coba nyari Post dengan slug `"create"` (gak ketemu, error 404). Route `{post}/edit` aman di posisi manapun karena punya 2 segment URL, beda pola sama `{post:slug}` yang cuma 1 segment.

### 5. Tambah Controller method `show()`

**📁 File: `app/Http/Controllers/PostController.php`** — tambah method baru:

```php
public function show(Post $post)
{
    return view('posts.show', ['post' => $post]);
}
```

Karena route-nya udah pakai `{post:slug}`, Laravel otomatis nyariin Post yang slug-nya cocok sama URL, terus dikirim jadi `$post` — gak perlu nulis query manual.

### 6. Bikin View halaman detail

**📁 File: `resources/views/posts/show.blade.php`**:

```blade
@extends('layouts.app')

@section('title', $post->title)

@section('content')
    <a href="/posts" class="text-sm text-blue-600 hover:underline">&larr; Kembali ke Daftar Post</a>

    <div class="bg-white border rounded-lg p-6 shadow-sm mt-4">
        <h1 class="text-2xl font-bold">{{ $post->title }}</h1>
        <p class="text-xs text-gray-400 mt-1">Ditulis oleh {{ $post->user->name ?? 'Tidak diketahui' }}</p>

        <p class="text-gray-700 mt-4 whitespace-pre-line">{{ $post->body }}</p>

        @if ($post->user_id === auth()->id() || auth()->user()->role === 'admin')
            <div class="mt-6 flex gap-2 border-t pt-4">
                <a href="/posts/{{ $post->id }}/edit"
                    class="text-sm bg-blue-100 text-blue-700 px-3 py-1 rounded hover:bg-blue-200">
                    Edit
                </a>

                <form method="POST" action="/posts/{{ $post->id }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Yakin hapus?')"
                        class="text-sm bg-red-100 text-red-700 px-3 py-1 rounded hover:bg-red-200">
                        Hapus
                    </button>
                </form>
            </div>
        @endif
    </div>
@endsection
```

`whitespace-pre-line` — class Tailwind biar baris baru (enter) yang diketik di textarea `body` tetep kebaca sebagai baris baru pas ditampilin (biasanya HTML "meratain" semua whitespace jadi 1 spasi).

### 7. Update Halaman List — judul post jadi link ke detail

**📁 File: `resources/views/posts/index.blade.php`** — ubah `<h2>` biasa jadi link:

```blade
<h2 class="font-semibold text-lg">
    <a href="/posts/{{ $post->slug }}" class="hover:underline hover:text-blue-600">
        {{ $post->title }}
    </a>
</h2>
```

### 8. Cek hasilnya

1. Bikin post baru lewat `/posts/create`, judul misal "Belajar Laravel Itu Seru".
2. Buka `/posts` — klik judul post itu.
3. URL harus berubah jadi `/posts/belajar-laravel-itu-seru` (bukan `/posts/6` atau semacamnya).
4. Halaman detail harus nampilin judul, nama penulis, isi lengkap, dan tombol Edit/Hapus (kalau kamu pemiliknya).
5. Coba bikin post baru lagi dengan judul **persis sama** — slug-nya harus otomatis jadi `belajar-laravel-itu-seru-1`.

### Cheatsheet Troubleshooting

| Masalah | Solusi |
|---|---|
| Error pas migrate: `Duplicate entry '' for key 'posts_slug_unique'` | Ada post lama dengan slug kosong. Kosongin tabel `posts` di phpMyAdmin, atau `php artisan migrate:fresh` |
| Klik judul post, muncul 404 | Pastiin route `{post:slug}` ditaruh **sebelum** `{post}/edit` tapi **setelah** `/posts/create` di `routes/web.php` |
| Slug kosong / `NULL` pas bikin post baru | Cek `app/Models/Post.php` — method `booted()` harus ada dan `static::creating(...)` gak typo |
| Akses `/posts/create` malah kena error "Post not found" | Route `{post:slug}` ketaruh **sebelum** route `/posts/create` — balik urutannya |

### Ringkasan File yang Ditambah/Diubah di Praktik Kedua Belas

| File | Perubahan |
|---|---|
| `database/migrations/..._add_slug_to_posts_table.php` | Migration baru, tambah kolom `slug` (unique) |
| `app/Models/Post.php` | Tambah `booted()` + `generateUniqueSlug()`, auto-generate slug dari title |
| `routes/web.php` | Tambah route `GET /posts/{post:slug}` |
| `app/Http/Controllers/PostController.php` | Tambah method `show()` |
| `resources/views/posts/show.blade.php` | View baru — halaman detail 1 post |
| `resources/views/posts/index.blade.php` | Judul post jadi link ke halaman detail |

### Ringkasan Praktik Kedua Belas

| Sebelum (Praktik 1-11) | Sesudah (Praktik 12) |
|---|---|
| Belum ada halaman detail post | Ada `/posts/{slug}` — halaman detail lengkap |
| URL bakal pakai angka (`/posts/5`) | URL pakai slug (`/posts/judul-post`) |
| Judul post di list gak bisa diklik | Judul jadi link ke halaman detail |

> Slug ini juga jadi dasar penting buat SEO kalau nanti project di-deploy beneran — URL yang pakai kata-kata jelas lebih gampang di-index Google dibanding URL angka doang.

## Praktik Ketiga Belas: Upload/Link Gambar

Sekarang tiap Post cuma punya title + body, belum ada gambar. Praktik ini nambah gambar buat tiap Post, dengan **dua cara ngisi**: upload file dari komputer, ATAU tempel link URL (misal dari Unsplash) — user pilih salah satu.

Gambar bakal muncul di dua tempat: **thumbnail** di list `/posts`, dan **full-size** di halaman detail `/posts/{slug}` (yang dibikin di Praktik 12).

Konsep baru: **file upload** (`$request->file(...)`), **Laravel Storage** (nyimpen file ke disk), dan **symbolic link** (biar file yang disimpen bisa diakses dari browser).

### 1. Migration — tambah kolom `image`

```bash
php artisan make:migration add_image_to_posts_table --table=posts
```

**📁 File: `database/migrations/..._add_image_to_posts_table.php`**:

```php
public function up(): void
{
    Schema::table('posts', function (Blueprint $table) {
        $table->string('image')->nullable()->after('body');
    });
}

public function down(): void
{
    Schema::table('posts', function (Blueprint $table) {
        $table->dropColumn('image');
    });
}
```

`->nullable()` — kolom ini **boleh kosong** (beda dari `title`/`body` yang wajib diisi), soalnya gambar bersifat opsional.

Jalankan:

```bash
php artisan migrate
```

### 2. Bikin Symbolic Link buat Storage

File yang di-upload user nanti disimpen di `storage/app/public/`, tapi folder itu **gak bisa diakses langsung** dari browser (di luar `public/`, demi keamanan). Laravel punya solusi: bikin "jembatan" (symbolic link) dari `public/storage` ke `storage/app/public`.

```bash
php artisan storage:link
```

Jalankan **sekali aja** (gak perlu diulang tiap ganti kode). Kalau berhasil, muncul folder baru `public/storage` (ini cuma shortcut, bukan folder asli).

> Kalau nanti pindah project (misal `git clone` di komputer lain), command ini harus dijalanin ulang — command ini gak ikut ke-commit di git.

### 3. Update Model — tambah `image` ke fillable + helper URL

**📁 File: `app/Models/Post.php`**:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Post extends Model
{
    protected $fillable = ['title', 'body', 'image', 'user_id'];

    protected static function booted(): void
    {
        static::creating(function (Post $post) {
            $post->slug = static::generateUniqueSlug($post->title);
        });
    }

    protected static function generateUniqueSlug(string $title): string
    {
        $slug = Str::slug($title);
        $original = $slug;
        $count = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = $original . '-' . $count;
            $count++;
        }

        return $slug;
    }

    public function imageUrl(): ?string
    {
        if (! $this->image) {
            return null;
        }

        return Str::startsWith($this->image, ['http://', 'https://'])
            ? $this->image
            : asset('storage/' . $this->image);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

Penjelasan `imageUrl()`:
- Kolom `image` di database nyimpen 2 kemungkinan isi: **path file lokal** (misal `posts/abc123.jpg`, hasil upload) atau **link URL penuh** (misal `https://images.unsplash.com/...`, hasil user tempel link).
- Method ini yang mutusin cara nampilinnya: kalau udah `http://`/`https://` (link luar), pakai apa adanya. Kalau bukan (path lokal), bungkus pakai `asset('storage/...')` biar jadi URL lengkap yang bisa diakses browser.
- View (`index.blade.php`, `show.blade.php`) tinggal manggil `$post->imageUrl()`, gak perlu mikirin logic-nya lagi.

### 4. Update Controller — handle upload & link URL

**📁 File: `app/Http/Controllers/PostController.php`** — update `store()` dan `update()`:

```php
public function store(Request $request)
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'body' => 'required|string',
        'image' => 'nullable|image|max:2048',
        'image_url' => 'nullable|url',
    ]);

    if ($request->hasFile('image')) {
        $validated['image'] = $request->file('image')->store('posts', 'public');
    } elseif ($request->filled('image_url')) {
        $validated['image'] = $request->image_url;
    }

    unset($validated['image_url']);
    $validated['user_id'] = auth()->id();

    Post::create($validated);

    return redirect('/posts');
}
```

```php
public function update(Request $request, Post $post)
{
    if ($post->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
        abort(403);
    }

    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'body' => 'required|string',
        'image' => 'nullable|image|max:2048',
        'image_url' => 'nullable|url',
    ]);

    if ($request->hasFile('image')) {
        $validated['image'] = $request->file('image')->store('posts', 'public');
    } elseif ($request->filled('image_url')) {
        $validated['image'] = $request->image_url;
    } else {
        unset($validated['image']);
    }

    unset($validated['image_url']);

    $post->update($validated);

    return redirect('/posts');
}
```

Penjelasan:
- `'image' => 'nullable|image|max:2048'` — validasi: kalau diisi, harus file gambar beneran (jpg/png/dll), maksimal 2MB (`2048` KB).
- `'image_url' => 'nullable|url'` — validasi: kalau diisi, harus format URL yang valid.
- `$request->hasFile('image')` — true kalau user pilih file lewat `<input type="file">`.
- `->store('posts', 'public')` — simpen file ke `storage/app/public/posts/`, otomatis kasih nama file unik, return path-nya (misal `posts/abc123.jpg`).
- `$request->filled('image_url')` — true kalau field `image_url` diisi teks (gak kosong).
- Urutan prioritas: kalau user upload file **dan** isi link bareng-bareng, file yang dipakai (upload menang).
- Di `update()`, ada `else { unset($validated['image']); }` — kalau edit post tapi gak ganti gambar sama sekali (gak upload, gak isi link baru), field `image` dibuang dari `$validated` biar gambar lama **gak ketimpa jadi kosong**.

### 5. Update Form Create — tambah input gambar

**📁 File: `resources/views/posts/create.blade.php`** — tambah `enctype` di `<form>` (wajib buat upload file) + 2 field baru:

```blade
@extends('layouts.app')

@section('title', 'Tambah Post')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Tambah Post</h1>

    @if ($errors->any())
        <ul class="bg-red-50 text-red-600 text-sm rounded p-3 mb-4">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="/posts" enctype="multipart/form-data" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium mb-1">Title</label>
            <input type="text" name="title" class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Body</label>
            <textarea name="body" rows="4" class="w-full border rounded px-3 py-2"></textarea>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Upload Gambar (opsional)</label>
            <input type="file" name="image" accept="image/*" class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Atau isi Link Gambar (opsional)</label>
            <input type="url" name="image_url" placeholder="https://images.unsplash.com/..."
                class="w-full border rounded px-3 py-2">
        </div>

        <p class="text-xs text-gray-400">Isi salah satu aja — upload file ATAU link. Kalau dua-duanya diisi, file upload yang dipakai.</p>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Simpan
        </button>
    </form>
@endsection
```

> `enctype="multipart/form-data"` **wajib** ditambah di `<form>` kalau ada `<input type="file">` — tanpa ini, file gak bakal ke-upload sama sekali (form cuma ngirim nama filenya doang, bukan isi file-nya).

### 6. Update Form Edit — sama, plus preview gambar lama

**📁 File: `resources/views/posts/edit.blade.php`**:

```blade
@extends('layouts.app')

@section('title', 'Edit Post')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Edit Post</h1>

    @if ($errors->any())
        <ul class="bg-red-50 text-red-600 text-sm rounded p-3 mb-4">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="/posts/{{ $post->id }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium mb-1">Title</label>
            <input type="text" name="title" value="{{ $post->title }}" class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Body</label>
            <textarea name="body" rows="4" class="w-full border rounded px-3 py-2">{{ $post->body }}</textarea>
        </div>

        @if ($post->imageUrl())
            <div>
                <label class="block text-sm font-medium mb-1">Gambar saat ini</label>
                <img src="{{ $post->imageUrl() }}" alt="{{ $post->title }}" class="w-40 rounded border">
            </div>
        @endif

        <div>
            <label class="block text-sm font-medium mb-1">Ganti Upload Gambar (opsional)</label>
            <input type="file" name="image" accept="image/*" class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Atau ganti Link Gambar (opsional)</label>
            <input type="url" name="image_url" placeholder="https://images.unsplash.com/..."
                class="w-full border rounded px-3 py-2">
        </div>

        <p class="text-xs text-gray-400">Kosongin dua-duanya kalau gambar lama gak mau diganti.</p>

        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
            Update
        </button>
    </form>
@endsection
```

### 7. Tampilin Gambar di Halaman List

**📁 File: `resources/views/posts/index.blade.php`** — tambah thumbnail:

```blade
@foreach ($posts as $post)
    <div class="bg-white border rounded-lg p-4 shadow-sm flex gap-4">
        @if ($post->imageUrl())
            <img src="{{ $post->imageUrl() }}" alt="{{ $post->title }}"
                class="w-24 h-24 object-cover rounded shrink-0">
        @endif

        <div class="flex-1">
            <h2 class="font-semibold text-lg">
                <a href="/posts/{{ $post->slug }}" class="hover:underline hover:text-blue-600">
                    {{ $post->title }}
                </a>
            </h2>
            <p class="text-gray-600">{{ $post->body }}</p>
            <p class="text-xs text-gray-400 mt-1">Ditulis oleh {{ $post->user->name ?? 'Tidak diketahui' }}</p>

            @if ($post->user_id === auth()->id() || auth()->user()->role === 'admin')
                <div class="mt-2 flex gap-2">
                    <a href="/posts/{{ $post->id }}/edit"
                        class="text-sm bg-blue-100 text-blue-700 px-3 py-1 rounded hover:bg-blue-200">
                        Edit
                    </a>

                    <form method="POST" action="/posts/{{ $post->id }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Yakin hapus?')"
                            class="text-sm bg-red-100 text-red-700 px-3 py-1 rounded hover:bg-red-200">
                            Hapus
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
@endforeach
```

`object-cover` — class Tailwind biar gambar gak gepeng/melar, tetep proporsional walau dipaksa jadi kotak `w-24 h-24`.

### 8. Tampilin Gambar di Halaman Detail (Single Post)

**📁 File: `resources/views/posts/show.blade.php`** — tambah gambar full-size di atas judul:

```blade
@extends('layouts.app')

@section('title', $post->title)

@section('content')
    <a href="/posts" class="text-sm text-blue-600 hover:underline">&larr; Kembali ke Daftar Post</a>

    <div class="bg-white border rounded-lg p-6 shadow-sm mt-4">
        @if ($post->imageUrl())
            <img src="{{ $post->imageUrl() }}" alt="{{ $post->title }}"
                class="w-full max-h-96 object-cover rounded mb-4">
        @endif

        <h1 class="text-2xl font-bold">{{ $post->title }}</h1>
        <p class="text-xs text-gray-400 mt-1">Ditulis oleh {{ $post->user->name ?? 'Tidak diketahui' }}</p>

        <p class="text-gray-700 mt-4 whitespace-pre-line">{{ $post->body }}</p>

        @if ($post->user_id === auth()->id() || auth()->user()->role === 'admin')
            <div class="mt-6 flex gap-2 border-t pt-4">
                <a href="/posts/{{ $post->id }}/edit"
                    class="text-sm bg-blue-100 text-blue-700 px-3 py-1 rounded hover:bg-blue-200">
                    Edit
                </a>

                <form method="POST" action="/posts/{{ $post->id }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Yakin hapus?')"
                        class="text-sm bg-red-100 text-red-700 px-3 py-1 rounded hover:bg-red-200">
                        Hapus
                    </button>
                </form>
            </div>
        @endif
    </div>
@endsection
```

### 9. Cek hasilnya

1. Buka `/posts/create`, isi title/body, **upload file gambar** dari komputer, submit.
2. Cek `/posts` — thumbnail gambar harus muncul di sebelah kiri post itu.
3. Klik judul post itu — di halaman detail (`/posts/{slug}`), gambar full-size harus muncul di atas judul.
4. Bikin post baru lagi, kali ini **isi link URL** (misal dari [unsplash.com](https://unsplash.com), klik kanan gambar → copy image address) di field "Link Gambar", jangan upload file. Submit — gambar dari link itu harus muncul juga.
5. Edit salah satu post, coba **ganti** gambarnya (upload baru / link baru) — gambar lama harus keganti.
6. Edit post lain, **kosongin** dua-duanya (jangan upload, jangan isi link) — gambar lama harus **tetep ada**, gak ilang.

### Cheatsheet Troubleshooting

| Masalah | Solusi |
|---|---|
| File ke-upload tapi gambar gak muncul (404/broken image) | Belum jalanin `php artisan storage:link` |
| Error `The image failed to upload` / validasi gagal terus | Cek ekstensi file (harus jpg/png/gif/dll) dan ukuran (maks 2MB sesuai `max:2048`) |
| Field `image` kebawa kosong padahal udah pilih file, atau muncul error validasi **"The image field must be an image"** walau file yang di-upload jelas jpg/png asli | `<form>` lupa ditambah `enctype="multipart/form-data"`. Tanpa ini, browser gak beneran ngirim isi file-nya, jadi Laravel nganggep field itu bukan file gambar valid. Cek di `create.blade.php` **dan** `edit.blade.php` — dua-duanya harus ada `enctype` |
| Gambar dari link URL gak muncul, padahal link-nya valid | Cek `imageUrl()` di Model — pastiin `Str::startsWith` ngecek `http://`/`https://`, dan link yang ditempel emang link gambar langsung (bukan link halaman web biasa) |
| Edit post tapi gambar lama malah ilang padahal gak diganti | Cek logic `update()` di Controller — bagian `else { unset($validated['image']); }` harus ada |

### Ringkasan File yang Ditambah/Diubah di Praktik Ketiga Belas

| File | Perubahan |
|---|---|
| `database/migrations/..._add_image_to_posts_table.php` | Migration baru, tambah kolom `image` (nullable) |
| `app/Models/Post.php` | Tambah `image` ke `$fillable`, tambah method `imageUrl()` |
| `app/Http/Controllers/PostController.php` | `store()` & `update()` — handle upload file dan link URL |
| `resources/views/posts/create.blade.php` | Tambah `enctype`, input upload file + input link URL |
| `resources/views/posts/edit.blade.php` | Sama kayak create, plus preview gambar lama |
| `resources/views/posts/index.blade.php` | Tambah thumbnail gambar di tiap baris post |
| `resources/views/posts/show.blade.php` | Tambah gambar full-size di halaman detail |

### Ringkasan Praktik Ketiga Belas

| Sebelum (Praktik 1-12) | Sesudah (Praktik 13) |
|---|---|
| Post cuma punya title + body | Post bisa punya gambar (opsional) |
| Belum ada file upload | Ada upload file (`storage/app/public/posts/`) |
| — | Bisa juga isi link URL gambar langsung (misal Unsplash) |

> Ini nutup fitur inti CRUD Post. Sisa roadmap (search, pagination, Form Request, Policy, dst) lebih ke **rapiin & scale-up**, bukan fitur baru yang keliatan di UI.

## Praktik Keempat Belas: Database Seeder

Selama ini tiap kali `migrate:fresh`, kamu harus `/register` ulang + isi post manual satu-satu. **Seeder** benerin ini — sekali command, database otomatis keisi data dummy (user + post) lengkap.

Konsep baru: **Seeder** (script isi data), **Factory** (template generate data palsu/dummy, udah disinggung sekilas di [Struktur Folder](#struktur-folder-laravel-catatan-belajar)), dan `recycle()` (numpang data yang udah ada, biar gak generate berlebihan).

### 1. Bikin Seeder buat User

```bash
php artisan make:seeder UserSeeder
```

**📁 File: `database/seeders/UserSeeder.php`**:

```php
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);

        User::factory(5)->create();
    }
}
```

Penjelasan:
- `User::factory()->create([...])` — bikin 1 user, tapi field yang dikasih manual (`name`, `email`, `role`) nimpa nilai random dari Factory. Field lain (`password`, dll) tetep pakai default Factory.
- `User::factory(5)->create()` — bikin 5 user random sekaligus, semua field (nama, email) di-generate otomatis pakai [Faker](https://fakerphp.github.io/) (library data dummy).
- `UserFactory.php` (`database/factories/`) udah otomatis dibuat sama Breeze — cek isinya, default password semua user hasil factory adalah `password` (plain text, di-hash otomatis).

### 2. Bikin Factory buat Post

Beda dari User (udah ada Factory-nya dari Breeze), Post belum punya. Sebelum bikin Factory-nya, cek dulu **📁 File: `app/Models/Post.php`** — kalau belum ada `use HasFactory;`, tambahin dulu:

```php
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'body', 'image', 'user_id'];

    // ...method lain yang udah ada tetap sama
```

> ⚠️ Trait `HasFactory` ini yang ngasih Model method `::factory()`. Kalau kelewat, nanti pas `Post::factory(...)` dipanggil di Seeder bakal error `Call to undefined method App\Models\Post::factory()`. `User.php` (bawaan Breeze) udah otomatis punya trait ini dari awal, tapi `Post.php` (yang kamu bikin sendiri dari Praktik 4) enggak — makanya harus ditambah manual di sini.

Setelah itu, bikin Factory-nya:

```bash
php artisan make:factory PostFactory --model=Post
```

**📁 File: `database/factories/PostFactory.php`**:

```php
<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    protected array $images = [
        'https://images.unsplash.com/photo-1499750310107-5fef28a66643?w=640&q=80',
        'https://images.unsplash.com/photo-1517842645767-c639042777db?w=640&q=80',
        'https://images.unsplash.com/photo-1499951360447-b19be8fe80f5?w=640&q=80',
        'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=640&q=80',
        'https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=640&q=80',
        'https://images.unsplash.com/photo-1488590528505-98d2b5aba04b?w=640&q=80',
        'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=640&q=80',
        'https://images.unsplash.com/photo-1461749280684-dccba630e2f6?w=640&q=80',
    ];

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'body' => fake()->paragraphs(3, true),
            'image' => fake()->randomElement($this->images),
            'user_id' => User::factory(),
        ];
    }
}
```

Penjelasan:
- `fake()->sentence(4)` — bikin kalimat random 4 kata, dipakai buat title.
- `fake()->paragraphs(3, true)` — bikin 3 paragraf random, digabung jadi 1 string (`true` = return string, bukan array), dipakai buat body.
- `protected array $images` — daftar link foto asli dari [Unsplash](https://unsplash.com), bukan placeholder abstrak. `fake()->randomElement($this->images)` milih 1 secara acak dari daftar ini buat tiap post, jadi **selalu ada gambar** (gak ada lagi kemungkinan kosong).
- Gak perlu isi `slug` di sini — inget dari [Praktik Kedua Belas](#praktik-kedua-belas-slug--single-post-view), slug otomatis ke-generate lewat Model Event (`static::creating(...)`) di `Post.php`, jadi Factory/Seeder otomatis kebagian efeknya juga.
- `'user_id' => User::factory()` — **wajib diisi di sini**, walau nanti nilainya bakal ditimpa `recycle()` di Seeder (step 3). Tanpa baris ini, `recycle()` gak punya "relasi" yang bisa disubstitusi, jadi `user_id` bakal kosong pas insert dan bikin error `Field 'user_id' doesn't have a default value` (kolom itu NOT NULL, dari foreign key di Praktik Kesembilan).

### 3. Bikin Seeder buat Post

```bash
php artisan make:seeder PostSeeder
```

**📁 File: `database/seeders/PostSeeder.php`**:

```php
<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        Post::factory(15)->recycle($users)->create();
    }
}
```

Penjelasan:
- `$users = User::all()` — ambil semua user yang udah ke-seed dari `UserSeeder` (step 1).
- `->recycle($users)` — biar tiap Post yang dibikin "numpang" salah satu User yang udah ada (dipilih random), bukan bikin User baru lagi buat tiap Post. Tanpa ini, Laravel defaultnya bikin 1 User baru per Post yang butuh relasi (boros & gak realistis).
- `Post::factory(15)->create()` — bikin 15 Post dummy.

### 4. Daftarin Seeder ke `DatabaseSeeder`

**📁 File: `database/seeders/DatabaseSeeder.php`** — ini "pintu masuk" utama, dipanggil kalau jalanin `php artisan db:seed`:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            PostSeeder::class,
        ]);
    }
}
```

> Urutan di `$this->call([...])` penting — `UserSeeder` **harus** duluan, soalnya `PostSeeder` butuh data User yang udah ada (buat `recycle()`).
>
> File asli `DatabaseSeeder.php` bawaan Laravel ada baris `User::factory()->create([...])` langsung di situ — boleh dihapus/diganti kayak di atas, soalnya sekarang urusan bikin User udah dipindah ke `UserSeeder`.

### 5. Jalankan Seeder

Ada 2 cara:

**Cara A — reset database + seed sekaligus** (paling umum dipakai pas development):

```bash
php artisan migrate:fresh --seed
```

**Cara B — seed doang, tanpa reset tabel** (kalau tabel udah ada, cuma mau nambah data):

```bash
php artisan db:seed
```

### 6. Cek hasilnya

1. Buka phpMyAdmin — tabel `users` harus ada 6 baris (1 admin + 5 random), tabel `posts` harus ada 15 baris.
2. Login pakai `admin@example.com` / password `password` — harus masuk sebagai admin (cek navbar ada link **User Management**).
3. Buka `/posts` — harus langsung ada 15 post dummy, semuanya ada gambar (dari daftar link Unsplash di `$images`).
4. Klik salah satu post — halaman detail (`/posts/{slug}`) harus tampil normal, slug-nya otomatis ke-generate dari title random.
5. Buka `/admin/users` — cek 5 user random tadi, role-nya default `user` (kecuali yang di-set manual `admin`).

### Cheatsheet Troubleshooting

| Masalah | Solusi |
|---|---|
| Error `Class "Database\Factories\PostFactory" not found` | Jalankan ulang `composer dump-autoload`, atau pastiin nama file & class-nya `PostFactory` (persis) |
| Semua Post bikin User baru, jumlah `users` meledak jadi ratusan | `->recycle($users)` kelewat / typo — cek lagi `PostSeeder.php` |
| `SQLSTATE... role` gak ke-set pas seed admin | Pastiin `role` udah ada di `#[Fillable(...)]` di `User.php` (fix dari [Praktik Kesebelas](#praktik-kesebelas-lengkapi-crud-user-management-tambah--hapus-user)) |
| Password login admin salah terus | Default password Factory adalah `password` (bukan `Password123` dll) — cek `UserFactory.php` kalau lupa |
| Gambar gak muncul di kartu post hasil seeder | Wajar kalau lagi offline — link Unsplash di `$images` butuh internet buat nge-load gambarnya. Cek juga link-nya masih aktif (buka manual di browser) |
| Semua post gambarnya sama persis | Wajar kalau `$images` isinya cuma 1-2 link, atau data yang di-seed dikit — coba tambah lebih banyak link di array `$images`, atau perbesar `Post::factory(15)` di `PostSeeder.php` |

### Ringkasan File yang Ditambah/Diubah di Praktik Keempat Belas

| File | Perubahan |
|---|---|
| `database/seeders/UserSeeder.php` | Seeder baru — bikin 1 admin + 5 user random |
| `database/factories/PostFactory.php` | Factory baru — template data dummy Post |
| `database/seeders/PostSeeder.php` | Seeder baru — bikin 15 Post dummy, numpang User yang ada |
| `database/seeders/DatabaseSeeder.php` | Update — panggil `UserSeeder` & `PostSeeder` lewat `$this->call([...])` |

### Ringkasan Praktik Keempat Belas

| Sebelum (Praktik 1-13) | Sesudah (Praktik 14) |
|---|---|
| Isi data dummy manual (register + isi form satu-satu) | `php artisan migrate:fresh --seed` — sekali command, semua keisi |
| Belum ada Factory buat Post | Ada `PostFactory`, bisa generate berapa aja post dummy |
| Testing app butuh isi data manual tiap reset | Reset + isi data cuma 1 command, testing jadi jauh lebih cepat |

> Ini bukan cuma buat belajar — di kerjaan beneran, Seeder & Factory juga dipakai buat **automated testing** (nanti di poin 19 Roadmap), biar test gak perlu database production asli.

## Praktik Kelima Belas: Search & Filter

Sekarang `/posts` nampilin semua post sekaligus, gak ada cara nyari post tertentu berdasarkan judul. Praktik ini nambah kotak pencarian — ketik kata kunci, list otomatis ke-filter.

Konsep baru: **query string** (`?search=...` di URL) dan **conditional query builder** (`when()` — nambah kondisi query cuma kalau syaratnya kepenuhi).

### 1. Update Controller — tambah logic search

**📁 File: `app/Http/Controllers/PostController.php`** — update method `index()`:

```php
public function index(Request $request)
{
    $posts = Post::when($request->search, function ($query, $search) {
        $query->where('title', 'like', '%' . $search . '%');
    })->latest()->get();

    return view('posts.index', [
        'posts' => $posts,
        'search' => $request->search,
    ]);
}
```

Penjelasan:
- `Post::when($request->search, function ($query, $search) { ... })` — `when()` cuma jalanin closure di dalamnya **kalau** `$request->search` ada isinya (gak `null`/kosong). Kalau user belum ngetik apa-apa di kotak search, query jalan normal kayak biasa (`Post::all()`-nya setara).
- `$query->where('title', 'like', '%' . $search . '%')` — cari post yang **title-nya mengandung** kata kunci, di posisi manapun (`%` di depan & belakang = wildcard, artinya "apa aja boleh sebelum/sesudah kata kunci").
- `->latest()` — urutin post terbaru duluan (`ORDER BY created_at DESC`), biar makin masuk akal daripada urutan acak.
- `'search' => $request->search` — dikirim balik ke view, biar kotak input search **tetep nunjukin kata kunci yang diketik** setelah submit (gak ke-reset kosong).

### 2. Update Route

**📁 File: `routes/web.php`** — cek route `/posts` udah otomatis nerima query string tanpa perlu diubah:

```php
Route::get('/posts', [PostController::class, 'index']);
```

> Gak perlu diubah! Query string (`?search=laravel`) itu bagian dari URL yang sama, Laravel otomatis nangkep lewat `$request` di Controller — beda konsep sama **route parameter** (`{id}`/`{slug}`) yang udah dipelajari di Praktik 2 & 12.

### 3. Tambah Form Search di View

**📁 File: `resources/views/posts/index.blade.php`** — tambah form pencarian di atas list:

```blade
@extends('layouts.app')

@section('title', 'Daftar Post')

@section('content')
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Daftar Post</h1>
        <a href="/posts/create" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            + Tambah Post
        </a>
    </div>

    <form method="GET" action="/posts" class="mb-4 flex gap-2">
        <input type="text" name="search" value="{{ $search }}" placeholder="Cari judul post..."
            class="flex-1 border rounded px-3 py-2">
        <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-900">
            Cari
        </button>
        @if ($search)
            <a href="/posts" class="text-sm text-gray-500 px-3 py-2 hover:underline">Reset</a>
        @endif
    </form>

    <div class="space-y-4">
        @forelse ($posts as $post)
            <div class="bg-white border rounded-lg p-4 shadow-sm flex gap-4">
                @if ($post->imageUrl())
                    <img src="{{ $post->imageUrl() }}" alt="{{ $post->title }}"
                        class="w-24 h-24 object-cover rounded shrink-0">
                @endif

                <div class="flex-1">
                    <h2 class="font-semibold text-lg">
                        <a href="/posts/{{ $post->slug }}" class="hover:underline hover:text-blue-600">
                            {{ $post->title }}
                        </a>
                    </h2>
                    <p class="text-gray-600">{{ $post->body }}</p>
                    <p class="text-xs text-gray-400 mt-1">Ditulis oleh {{ $post->user->name ?? 'Tidak diketahui' }}</p>

                    @if ($post->user_id === auth()->id() || auth()->user()->role === 'admin')
                        <div class="mt-2 flex gap-2">
                            <a href="/posts/{{ $post->id }}/edit"
                                class="text-sm bg-blue-100 text-blue-700 px-3 py-1 rounded hover:bg-blue-200">
                                Edit
                            </a>

                            <form method="POST" action="/posts/{{ $post->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Yakin hapus?')"
                                    class="text-sm bg-red-100 text-red-700 px-3 py-1 rounded hover:bg-red-200">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <p class="text-gray-500 text-sm">Gak ada post yang cocok dengan pencarian "{{ $search }}".</p>
        @endforelse
    </div>
@endsection
```

Penjelasan:
- `<form method="GET" action="/posts">` — pakai `GET`, bukan `POST`. Search itu sifatnya "baca data" doang, bukan ubah data, dan `GET` otomatis nyimpen kata kunci di URL (`?search=laravel`), jadi bisa di-bookmark/share.
- `name="search"` di `<input>` — nama ini **harus sama persis** dengan yang dibaca di Controller (`$request->search`).
- `@forelse ... @empty ... @endforelse` — versi `@foreach` yang punya "fallback" kalau data-nya kosong. Kalau `$posts` kosong (gak ada hasil cocok), otomatis tampilin pesan di `@empty` — gak perlu `@if (count($posts) > 0)` manual.
- Tombol **Reset** cuma muncul kalau lagi ada pencarian aktif (`@if ($search)`), link-nya balik ke `/posts` polos (tanpa query string).

### 4. Cek hasilnya

1. Buka `/posts` — semua post tampil, kotak search kosong.
2. Ketik sebagian judul salah satu post (misal "Belajar"), klik **Cari**.
3. URL harus berubah jadi `/posts?search=Belajar`, list otomatis ke-filter cuma post yang judulnya mengandung "Belajar".
4. Kotak input harus tetep nunjukin "Belajar" (gak kosong lagi) setelah submit.
5. Cari kata yang gak ada di judul manapun — harus muncul pesan "Gak ada post yang cocok...".
6. Klik **Reset** — balik nampilin semua post.

### Cheatsheet Troubleshooting

| Masalah | Solusi |
|---|---|
| Search gak ngefilter apa-apa, selalu nampilin semua post | Cek `name="search"` di `<input>` sama persis kayak `$request->search` di Controller |
| Kotak search jadi kosong lagi setelah submit | Pastiin `value="{{ $search }}"` ada di `<input>`, dan `'search' => $request->search` dikirim dari Controller ke view |
| Error `Undefined variable $search` | View dipanggil tanpa key `search` di `view('posts.index', [...])` — cek Controller step 1 |
| Search "nemplok" gak reset pas klik link lain di navbar | Wajar — search cuma nempel di URL `/posts?search=...`, pindah ke halaman lain otomatis ilang karena beda URL |

### Ringkasan File yang Ditambah/Diubah di Praktik Kelima Belas

| File | Perubahan |
|---|---|
| `app/Http/Controllers/PostController.php` | `index()` — tambah `Request $request`, logic `when()` buat search, `->latest()` |
| `resources/views/posts/index.blade.php` | Tambah form search (`GET`), ganti `@foreach` jadi `@forelse`/`@empty` |

### Ringkasan Praktik Kelima Belas

| Sebelum (Praktik 1-14) | Sesudah (Praktik 15) |
|---|---|
| `/posts` selalu nampilin semua data | Bisa difilter lewat kotak search |
| Urutan post gak jelas/acak | Post terbaru tampil duluan (`->latest()`) |
| `@foreach` polos | `@forelse`/`@empty` — ada pesan kalau hasil kosong |

> `when()` ini pola yang sering banget dipakai di Laravel buat query yang "kondisional" — nanti kalau nambah filter lain (misal filter by kategori, filter by tanggal), tinggal tambah `->when(...)` lagi berantai, gak perlu `if-else` bertingkat manual.

## Praktik Keenam Belas: Pagination & Card Grid

Sekarang `/posts` nampilin **semua** post sekaligus dalam 1 halaman panjang — kalau datanya udah puluhan/ratusan (apalagi abis pakai Seeder), scroll-nya jadi kepanjangan. Praktik ini nambah **pagination** (potong per halaman), sekalian dandanin tampilan card-nya jadi **grid** (gambar di atas, rapi kayak kartu blog).

Konsep baru: **pagination** (`->paginate()`), dan `withQueryString()` (biar filter search gak ilang pas pindah halaman).

### 1. Update Controller — ganti `get()` jadi `paginate()`

**📁 File: `app/Http/Controllers/PostController.php`** — update method `index()`:

```php
public function index(Request $request)
{
    $posts = Post::when($request->search, function ($query, $search) {
        $query->where('title', 'like', '%' . $search . '%');
    })->latest()->paginate(6)->withQueryString();

    return view('posts.index', [
        'posts' => $posts,
        'search' => $request->search,
    ]);
}
```

Penjelasan:
- `->paginate(6)` — ganti `->get()`. Angka `6` artinya 6 post per halaman (pas buat grid 3 kolom, 2 baris). Laravel otomatis ngitung total halaman, nentuin data mana yang ditampilin sesuai `?page=2`, `?page=3`, dst di URL.
- `->withQueryString()` — biar query string lain (kayak `?search=laravel` dari Praktik 15) **tetep kebawa** pas pindah ke halaman berikutnya. Tanpa ini, klik "Next" bakal ilangin hasil pencarian.
- `$posts` sekarang bukan `Collection` biasa lagi, tapi objek `LengthAwarePaginator` — tetep bisa di-`@foreach`/`@forelse` kayak biasa di view, cuma punya method tambahan kayak `->links()` (buat nampilin tombol Next/Previous).

### 2. Update View — Card Grid + Tombol Pagination

**📁 File: `resources/views/posts/index.blade.php`** — rombak total jadi grid layout:

```blade
@extends('layouts.app')

@section('title', 'Daftar Post')

@section('container', 'max-w-6xl')

@section('content')
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Daftar Post</h1>
        <a href="/posts/create" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            + Tambah Post
        </a>
    </div>

    <form method="GET" action="/posts" class="mb-6 flex gap-2">
        <input type="text" name="search" value="{{ $search }}" placeholder="Cari judul post..."
            class="flex-1 border rounded px-3 py-2">
        <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-900">
            Cari
        </button>
        @if ($search)
            <a href="/posts" class="text-sm text-gray-500 px-3 py-2 hover:underline">Reset</a>
        @endif
    </form>

    <div class="flex justify-end mb-6">
        {{ $posts->links() }}
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($posts as $post)
            <div class="bg-white border rounded-lg shadow-sm overflow-hidden flex flex-col">
                @if ($post->imageUrl())
                    <img src="{{ $post->imageUrl() }}" alt="{{ $post->title }}" class="w-full h-48 object-cover">
                @else
                    <div class="w-full h-48 bg-gray-100 flex items-center justify-center text-gray-400 text-sm">
                        Tanpa gambar
                    </div>
                @endif

                <div class="p-4 flex flex-col flex-1">
                    <h2 class="font-semibold text-lg mb-1">
                        <a href="/posts/{{ $post->slug }}" class="hover:underline hover:text-blue-600">
                            {{ $post->title }}
                        </a>
                    </h2>

                    <p class="text-gray-600 text-sm line-clamp-3 flex-1">{{ $post->body }}</p>

                    <p class="text-xs text-gray-400 mt-3">Ditulis oleh {{ $post->user->name ?? 'Tidak diketahui' }}</p>

                    @if ($post->user_id === auth()->id() || auth()->user()->role === 'admin')
                        <div class="mt-3 flex gap-2">
                            <a href="/posts/{{ $post->id }}/edit"
                                class="text-sm bg-blue-100 text-blue-700 px-3 py-1 rounded hover:bg-blue-200">
                                Edit
                            </a>

                            <form method="POST" action="/posts/{{ $post->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Yakin hapus?')"
                                    class="text-sm bg-red-100 text-red-700 px-3 py-1 rounded hover:bg-red-200">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <p class="text-gray-500 text-sm col-span-full">Gak ada post yang cocok dengan pencarian "{{ $search }}".</p>
        @endforelse
    </div>

    <div class="flex justify-end mt-6">
        {{ $posts->links() }}
    </div>
@endsection
```

Penjelasan:
- `@section('container', 'max-w-6xl')` — pola yang sama kayak dipakai di `/admin/users` sebelumnya, biar halaman ini lebih lebar dari default `max-w-2xl` (muat 3 kolom kartu).
- `grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6` — 1 kolom di layar HP, 2 kolom di tablet, 3 kolom di desktop. Ini **responsive design** dasar pakai Tailwind.
- Gambar sekarang `w-full h-48 object-cover` — full lebar kartu, tinggi tetap 48 (192px), gak gepeng.
- Post tanpa gambar dikasih placeholder abu-abu ("Tanpa gambar"), biar tinggi kartu tetep konsisten sebaris sama yang ada gambar.
- `line-clamp-3` — potong teks body maksimal 3 baris, sisanya otomatis kasih `...`. Class ini bawaan Tailwind v4 (gak perlu plugin tambahan).
- `flex flex-col flex-1` di wrapper konten — biar tombol Edit/Hapus selalu nempel di **bawah** kartu, walau body-nya pendek/panjang beda-beda antar kartu (card tetep sejajar rapi).
- `{{ $posts->links() }}` — ini yang nampilin tombol Previous/Next + nomor halaman. Laravel otomatis generate HTML-nya lengkap dengan style Tailwind, gak perlu bikin manual.
- Pagination sengaja ditaruh **2 kali** (atas, tepat di bawah search bar, dan bawah, di akhir grid) — biar user gak perlu scroll balik ke atas buat pindah halaman kalau lagi di bawah.
- `flex justify-end` — nge-dorong tombol pagination rata ke **kanan** (bukan tengah), pola umum di banyak web/dashboard biar mata gampang nemuinnya konsisten di satu sisi.

### 3. Publish & Cek Warna Pagination Bawaan Laravel

Laravel udah nyiapin tampilan pagination siap pakai (`{{ $posts->links() }}`), **default-nya udah putih/abu-abu** (`bg-white`, border abu-abu, cuma halaman aktif dikasih `bg-gray-200`) — jadi otomatis nyatu sama tema web ini tanpa perlu bikin manual atau dibungkus card tambahan.

Kalau nanti mau kustomisasi lebih jauh (misal ganti warna halaman aktif, ubah bentuk tombol), publish dulu file view-nya biar bisa diedit:

```bash
php artisan vendor:publish --tag=laravel-pagination
```

Ini bikin file baru **📁 File: `resources/views/vendor/pagination/tailwind.blade.php`** — isinya salinan tampilan pagination bawaan Laravel. Karena file ini sekarang ada di project kamu (bukan di dalam `vendor/`), otomatis dipakai gantiin punya Laravel, dan bisa diedit bebas (misal ganti `bg-gray-200` di halaman aktif jadi `bg-blue-600 text-white` biar senada sama tombol-tombol lain).

> Gak wajib di-publish kalau warna default-nya udah cocok — cukup dijalanin sekali kalau suatu saat mau utak-atik tampilannya lebih detail.

### 4. Cek hasilnya

1. Refresh `/posts` — tampilan sekarang jadi grid 3 kolom (di layar desktop), tiap kartu ada gambar di atas.
2. Kalau jumlah post lebih dari 6, harus muncul tombol pagination — di **atas** (bawah search bar) dan di **bawah** (akhir grid), rata kanan, warna putih/abu-abu nyatu tema.
3. Klik halaman 2 — URL berubah jadi `/posts?page=2`, isi grid ganti ke 6 post berikutnya.
4. Coba search dulu (misal ketik "Belajar"), abis itu klik halaman 2 (kalau hasilnya lebih dari 6) — cek URL harus jadi `/posts?search=Belajar&page=2` (dua-duanya kebawa, berkat `withQueryString()`).
5. Coba resize browser sempit (atau buka dari HP) — grid harus otomatis jadi 1 kolom.

### Cheatsheet Troubleshooting

| Masalah | Solusi |
|---|---|
| Tombol pagination gak muncul sama sekali | Total post kamu kurang dari 6 (angka di `paginate(6)`) — pagination cuma muncul kalau datanya lebih dari 1 halaman. Coba turunin ke `paginate(3)` buat testing |
| Klik halaman 2, hasil search ilang balik ke semua post | `->withQueryString()` kelewat gak ditambah di Controller |
| Card tinggi-nya gak sejajar antar kolom | Cek `flex flex-col flex-1` ada di wrapper konten (div dalam kartu) |
| `line-clamp-3` gak motong teks (teks panjang tetep penuh) | Pastiin Tailwind versi 4 (cek `package.json` — kalau Tailwind v3 ke bawah, butuh install plugin `@tailwindcss/line-clamp` terpisah) |
| Pagination keliatan rata tengah padahal mau rata kanan | Cek class `flex justify-end` (bukan `justify-center`) di div pembungkus `{{ $posts->links() }}` |
| Udah publish view tapi perubahan warna gak kepakai | Cek file ada di `resources/views/vendor/pagination/tailwind.blade.php` (bukan di folder lain), dan `npm run dev` masih jalan buat compile ulang class Tailwind baru |

### Ringkasan File yang Ditambah/Diubah di Praktik Keenam Belas

| File | Perubahan |
|---|---|
| `app/Http/Controllers/PostController.php` | `index()` — `->get()` jadi `->paginate(6)->withQueryString()` |
| `resources/views/posts/index.blade.php` | Rombak jadi grid card (gambar atas), tambah `{{ $posts->links() }}` di atas & bawah, rata kanan (`flex justify-end`) |
| `resources/views/vendor/pagination/tailwind.blade.php` | (Opsional) Hasil publish, buat kustomisasi warna/tampilan pagination lebih lanjut |

### Ringkasan Praktik Keenam Belas

| Sebelum (Praktik 1-15) | Sesudah (Praktik 16) |
|---|---|
| Semua post tampil sekaligus, list vertikal | Dibagi per halaman (6 post/halaman), tampilan grid |
| Card horizontal (gambar kiri, teks kanan) | Card vertikal (gambar atas, konten bawah) — kayak kartu blog umum |
| Belum responsive khusus (cuma 1 kolom) | Grid 1/2/3 kolom tergantung lebar layar |
| Belum ada pagination | Pagination di atas & bawah, warna nyatu tema putih website |

> Pagination itu bukan cuma soal tampilan — ini juga soal **performa**. Tanpa pagination, `Post::all()` bakal narik SEMUA baris dari database sekaligus walau cuma mau nampilin 9. Makin banyak data, makin lambat. `paginate()` cuma narik data secukupnya per halaman.

### Perubahan Tambahan Setelah Praktik 16 (Fine-tuning)

Beberapa penyesuaian kecil yang dilakuin setelah versi awal Praktik 16, biar tampilan & data dummy makin pas:

| # | Perubahan | File | Detail |
|---|---|---|---|
| 1 | Jumlah post per halaman diturunin | `app/Http/Controllers/PostController.php` | `paginate(9)` → `paginate(6)` |
| 2 | Pagination ditaruh 2 kali | `resources/views/posts/index.blade.php` | `{{ $posts->links() }}` muncul di **atas** (bawah search bar) dan di **bawah** (akhir grid) |
| 3 | Pagination diratain ke kanan | `resources/views/posts/index.blade.php` | Class `flex justify-center` diganti jadi `flex justify-end` |
| 4 | Wrapper card putih di pagination dihapus | `resources/views/posts/index.blade.php` | Div `bg-white rounded-lg shadow-sm p-3` dihapus — tombol pagination bawaan Laravel udah putih/abu-abu dari sananya, jadi udah nyatu sama tema tanpa perlu dibungkus card lagi |
| 5 | View pagination di-publish (opsional) | `resources/views/vendor/pagination/tailwind.blade.php` | Dijalanin `php artisan vendor:publish --tag=laravel-pagination` — biar bisa kustomisasi warna/tampilan pagination lebih lanjut kalau perlu nanti |
| 6 | Gambar dummy Seeder diganti | `database/factories/PostFactory.php` | `fake()->imageUrl()` (Picsum, 30% kemungkinan kosong) diganti jadi `protected array $images` isi 8 link foto [Unsplash](https://unsplash.com) asli + `fake()->randomElement($this->images)` — sekarang **semua** post hasil seeder pasti punya gambar, gak ada lagi yang kosong |

> Perubahan #6 sebenernya di file punya [Praktik Keempat Belas](#praktik-keempat-belas-database-seeder) (Seeder), tapi dicatat di sini juga soalnya dilakuin bareng sesi perbaikan tampilan Praktik 16 ini.

**📁 File: `app/Http/Controllers/PostController.php`** — copy-paste seluruh isinya:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    // method untuk menampilkan semua post dan searching post
    public function index(Request $request)
    {
        $posts = Post::when($request->search, function ($query, $search) {
            $query->where('title', 'like', '%' . $search . '%');
        })->latest()->paginate(6)->withQueryString();

        return view('posts.index', [
            'posts' => $posts,
            'search' => $request->search,
        ]);
    }

    // slug post
    public function show(Post $post)
    {
        return view('posts.show', ['post' => $post]);
    }

    // method untuk buat post atau create post baru
    public function create()
    {
        return view('posts.create');
    }

    // method untuk simpen data atau store
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'image_url' => 'nullable|url',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('posts', 'public');
        } elseif ($request->filled('image_url')) {
            $validated['image'] = $request->image_url;
        }

        unset($validated['image_url']);
        $validated['user_id'] = auth()->id();

        Post::create($validated);

        return redirect('/posts');
    }

    // method untuk edit post
    public function edit(Post $post)
    {
        if ($post->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403);
        }

        return view('posts.edit', ['post' => $post]);
    }

    // method untuk update post
    public function update(Request $request, Post $post)
    {
        if ($post->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'image_url' => 'nullable|url',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('posts', 'public');
        } elseif ($request->filled('image_url')) {
            $validated['image'] = $request->image_url;
        } else {
            unset($validated['image']);
        }

        unset($validated['image_url']);

        $post->update($validated);

        return redirect('/posts');
    }

    // method untuk delete post
    public function destroy(Post $post)
    {
        if ($post->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403);
        }

        $post->delete();

        return redirect('/posts');
    }
}
```

**📁 File: `resources/views/posts/index.blade.php`** — copy-paste seluruh isinya:

```blade
@extends('layouts.app')

@section('title', 'Daftar Post')

@section('container', 'max-w-6xl')

@section('content')
    {{-- tambah post --}}
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Daftar Post</h1>
        <a href="/posts/create" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            + Tambah Post
        </a>
    </div>

    {{-- search --}}
    <form method="GET" action="/posts" class="mb-6 flex gap-2">
        <input type="text" name="search" value="{{ $search }}" placeholder="Cari judul post..."
            class="flex-1 border rounded px-3 py-2">
        <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-900">
            Cari
        </button>
        @if ($search)
            <a href="/posts" class="text-sm text-gray-500 px-3 py-2 hover:underline">Reset</a>
        @endif
    </form>

    <div class="flex justify-end mb-6">
        {{ $posts->links() }}
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($posts as $post)
            <div class="bg-white border rounded-lg shadow-sm overflow-hidden flex flex-col">
                @if ($post->imageUrl())
                    <img src="{{ $post->imageUrl() }}" alt="{{ $post->title }}" class="w-full h-48 object-cover">
                @else
                    <div class="w-full h-48 bg-gray-100 flex items-center justify-center text-gray-400 text-sm">
                        Tanpa gambar
                    </div>
                @endif

                <div class="p-4 flex flex-col flex-1">
                    <h2 class="font-semibold text-lg mb-1">
                        <a href="/posts/{{ $post->slug }}" class="hover:underline hover:text-blue-600">
                            {{ $post->title }}
                        </a>
                    </h2>

                    <p class="text-gray-600 text-sm line-clamp-3 flex-1">{{ $post->body }}</p>

                    <p class="text-xs text-gray-400 mt-3">Ditulis oleh {{ $post->user->name ?? 'Tidak diketahui' }}</p>

                    @if ($post->user_id === auth()->id() || auth()->user()->role === 'admin')
                        <div class="mt-3 flex gap-2">
                            <a href="/posts/{{ $post->id }}/edit"
                                class="text-sm bg-blue-100 text-blue-700 px-3 py-1 rounded hover:bg-blue-200">
                                Edit
                            </a>

                            <form method="POST" action="/posts/{{ $post->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Yakin hapus?')"
                                    class="text-sm bg-red-100 text-red-700 px-3 py-1 rounded hover:bg-red-200">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <p class="text-gray-500 text-sm col-span-full">Gak ada post yang cocok dengan pencarian "{{ $search }}".</p>
        @endforelse
    </div>

    <div class="flex justify-end mt-6">
        {{ $posts->links() }}
    </div>
@endsection
```

**Perubahan #5** — dijalankan sekali di terminal, bukan edit manual:

```bash
php artisan vendor:publish --tag=laravel-pagination
```

**📁 File: `database/factories/PostFactory.php`** — copy-paste seluruh isinya:

```php
<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    protected array $images = [
        'https://images.unsplash.com/photo-1499750310107-5fef28a66643?w=640&q=80',
        'https://images.unsplash.com/photo-1517842645767-c639042777db?w=640&q=80',
        'https://images.unsplash.com/photo-1499951360447-b19be8fe80f5?w=640&q=80',
        'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=640&q=80',
        'https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=640&q=80',
        'https://images.unsplash.com/photo-1488590528505-98d2b5aba04b?w=640&q=80',
        'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=640&q=80',
        'https://images.unsplash.com/photo-1461749280684-dccba630e2f6?w=640&q=80',
    ];

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'body' => fake()->paragraphs(3, true),
            'image' => fake()->randomElement($this->images),
            'user_id' => User::factory(),
        ];
    }
}
```

## Praktik Ketujuh Belas: Form Request

Sekarang tiap method `store()` dan `update()` di `PostController.php` punya blok `$request->validate([...])` yang **isinya sama persis**, keulang di 2 tempat. Praktik ini pindahin logic validasi (dan sekalian otorisasi) ke class terpisah bernama **Form Request**, biar Controller-nya lebih bersih dan gak ada duplikasi.

Konsep baru: **Form Request** (class custom yang extends `FormRequest`, punya 2 method utama: `rules()` buat validasi dan `authorize()` buat ngecek "boleh gak aksi ini dilakuin").

### Kenapa Perlu Form Request? (Teori)

Form Request itu jawaban buat 2 masalah sekaligus yang muncul kalau validasi & otorisasi ditulis manual terus-terusan di Controller:

1. **Data-nya valid gak?** (validasi) — judul gak boleh kosong, gambar harus format gambar, dll. Ini murni soal **format data**, gak ada hubungannya sama siapa yang ngirim.
2. **Orangnya boleh gak?** (otorisasi) — misal cuma pemilik post/admin yang boleh update. Ini soal **izin/permission**.

Dua-duanya sering ditulis bareng di Controller (`$request->validate([...])` + `if (...) abort(403)`), padahal beda tanggung jawab. Form Request misahin keduanya ke satu class, dengan manfaat konkret:

- **Gak ada duplikasi kode.** Sebelumnya, aturan validasi `title`/`body`/`image` ditulis ulang persis sama di `store()` DAN `update()`. Kalau nanti mau ubah aturan (misal `max:255` jadi `max:500`), harus inget ubah di 2 tempat. Sekarang cukup 1 tempat.
- **Controller jadi lebih pendek & fokus.** Method di Controller isinya cuma logic bisnis (simpen data, redirect), bukan campur aduk sama blok validasi yang panjang.
- **Otomatis jalan duluan.** Laravel manggil `authorize()` dan `rules()` **sebelum** kode di dalam method Controller sempat dieksekusi — kalau gagal (data gak valid / gak berhak), method Controller-nya **gak pernah kejalan sama sekali**. Ini beda dari nulis manual, di mana kamu harus inget naruh pengecekan itu di baris paling atas method setiap kali.
- **Reusable & testable.** Class Form Request bisa dites terpisah (unit test) tanpa harus manggil seluruh Controller, dan bisa dipakai ulang kalau ada route lain yang butuh validasi sama persis.
- **Lebih susah kelupaan.** Karena `authorize()` itu wajib ada (bagian dari kontrak `FormRequest`), developer "dipaksa" mikirin izin akses tiap kali bikin Form Request baru — beda sama nulis manual yang gampang kelewat di salah satu method.

> Analoginya: sebelum Form Request, tiap "pintu masuk" (method Controller) punya satpam sendiri-sendiri yang harus diingetin manual jaga di situ. Form Request itu kayak nempatin 1 pos security terpusat di depan gerbang — semua yang mau masuk otomatis diperiksa dulu di situ, sebelum sempat nyampe ke Controller.

### 1. Bikin Form Request buat Create

```bash
php artisan make:request StorePostRequest
```

**📁 File: `app/Http/Requests/StorePostRequest.php`**:

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'image_url' => 'nullable|url',
        ];
    }
}
```

Penjelasan:
- `authorize()` — return `true`/`false`, nentuin apakah request ini **boleh diproses sama sekali**. Di sini cukup cek user udah login (`auth()->check()`) — soalnya route `/posts` udah dibungkus middleware `auth` dari Praktik 8, tapi nulis ulang di sini bikin Form Request-nya "mandiri", gak gantung ke middleware doang.
- `rules()` — isinya persis kayak array yang dulu ditulis manual di `$request->validate([...])`. Sekarang tinggal dipindah ke sini.

### 2. Update Controller — pakai `StorePostRequest`

**📁 File: `app/Http/Controllers/PostController.php`** — ganti method `store()`:

```php
use App\Http\Requests\StorePostRequest;

// ...

public function store(StorePostRequest $request)
{
    $validated = $request->validated();

    if ($request->hasFile('image')) {
        $validated['image'] = $request->file('image')->store('posts', 'public');
    } elseif ($request->filled('image_url')) {
        $validated['image'] = $request->image_url;
    }

    unset($validated['image_url']);
    $validated['user_id'] = auth()->id();

    Post::create($validated);

    return redirect('/posts');
}
```

Penjelasan:
- Type-hint parameter diganti dari `Request $request` jadi `StorePostRequest $request` — ini yang bikin Laravel otomatis manggil `authorize()` dan `rules()` **sebelum** method `store()` sempat jalan. Kalau validasi gagal, otomatis redirect balik ke form + error (perilaku sama kayak sebelumnya). Kalau `authorize()` return `false`, otomatis muncul halaman 403.
- `$request->validated()` — ganti `$request->validate([...])`. Bedanya: `validate()` nge-jalanin validasi DAN return hasilnya sekaligus; `validated()` cuma **ambil hasil validasi** yang udah dijalanin duluan sama Form Request-nya.
- Sisa logic (`hasFile`, `image_url`, dst dari Praktik 13) **gak berubah**, tetep di Controller — Form Request cuma ngurus validasi & otorisasi, bukan logic bisnis.

### 3. Bikin Form Request buat Update

```bash
php artisan make:request UpdatePostRequest
```

**📁 File: `app/Http/Requests/UpdatePostRequest.php`**:

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        $post = $this->route('post');

        return $post->user_id === auth()->id() || auth()->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'image_url' => 'nullable|url',
        ];
    }
}
```

Penjelasan:
- `$this->route('post')` — ambil Post yang udah di-resolve lewat route model binding (`{post}` di URL `/posts/{post}`), sama persis objek yang biasanya jadi parameter kedua di method `update()`.
- `authorize()` di sini **gantiin** pengecekan manual `if ($post->user_id !== auth()->id() && ...) { abort(403); }` yang dulu ditulis di dalam method `update()` — sekarang logic itu pindah ke sini, jalan otomatis sebelum method-nya dieksekusi.

### 4. Update Controller — pakai `UpdatePostRequest`

**📁 File: `app/Http/Controllers/PostController.php`** — ganti method `update()`:

```php
use App\Http\Requests\UpdatePostRequest;

// ...

public function update(UpdatePostRequest $request, Post $post)
{
    $validated = $request->validated();

    if ($request->hasFile('image')) {
        $validated['image'] = $request->file('image')->store('posts', 'public');
    } elseif ($request->filled('image_url')) {
        $validated['image'] = $request->image_url;
    } else {
        unset($validated['image']);
    }

    unset($validated['image_url']);

    $post->update($validated);

    return redirect('/posts');
}
```

Perhatiin: pengecekan `if ($post->user_id !== auth()->id() && ...) { abort(403); }` yang dulu ada di awal method **udah dihapus** — sekarang itu tanggung jawab `UpdatePostRequest::authorize()` di step 3.

> Method `edit()` dan `destroy()` **tetep pakai** pengecekan manual (`abort(403)`) kayak biasa — soalnya dua method itu gak nerima data form (gak butuh validasi), jadi gak perlu Form Request. Form Request cuma dipakai buat request yang bawa data (`store()`/`update()`).

### 5. Cek hasilnya

1. Buka `/posts/create`, submit form kosong — pesan error validasi harus tetep muncul, sama kayak sebelumnya (perilaku gak berubah dari sisi user).
2. Isi form bener, submit — post baru harus kesimpen normal.
3. Edit post **milik sendiri** — harus bisa, sama kayak sebelumnya.
4. Coba akses `/posts/{id}/edit` dari post **milik user lain** (bukan admin) — halaman edit-nya (`GET`) tetep muncul 403 lewat pengecekan manual di `edit()`. Tapi coba submit `PUT` langsung ke `/posts/{id}` post orang lain (misal pakai Thunder Client/Postman) — harus ditolak juga lewat `UpdatePostRequest::authorize()`.
5. Login sebagai admin, coba update post siapa aja — harus tetep bisa (`authorize()` ngecek `role === 'admin'` juga).

### Cheatsheet Troubleshooting

| Masalah | Solusi |
|---|---|
| Error "This action is unauthorized" padahal harusnya boleh | Cek logic `authorize()` — default bawaan `make:request` itu `return false;`, gampang kelupaan diganti |
| Validasi gak jalan sama sekali, form kesimpen walau kosong | Cek type-hint parameter di Controller — harus `StorePostRequest $request` / `UpdatePostRequest $request`, bukan `Request $request` biasa |
| Error `Call to a member function route() on null` di `authorize()` | Typo nama parameter route — cek nama parameter di `routes/web.php` (`{post}`) harus sama persis sama yang dipanggil di `$this->route('post')` |
| Field `image`/`image_url` ilang setelah pindah ke Form Request | Pastiin `rules()` di Form Request masih include field itu — gampang kelewat pas mindahin dari `$request->validate([...])` lama |

### Ringkasan File yang Ditambah/Diubah di Praktik Ketujuh Belas

| File | Perubahan |
|---|---|
| `app/Http/Requests/StorePostRequest.php` | File baru — validasi + otorisasi buat Create |
| `app/Http/Requests/UpdatePostRequest.php` | File baru — validasi + otorisasi buat Update |
| `app/Http/Controllers/PostController.php` | `store()` & `update()` — ganti type-hint, hapus `$request->validate([...])` manual & pengecekan kepemilikan manual di `update()` |

### Ringkasan Praktik Ketujuh Belas

| Sebelum (Praktik 1-16) | Sesudah (Praktik 17) |
|---|---|
| Validasi ditulis manual, dobel di `store()` & `update()` | Validasi satu tempat per Form Request, gak ada duplikasi |
| Otorisasi (`abort(403)`) campur sama logic Controller | Otorisasi `update()` pindah ke `authorize()`, lebih rapi |
| Controller ngurus validasi + logic bisnis sekaligus | Controller fokus logic bisnis doang, validasi didelegasiin |

> Form Request ini langkah awal ke pola **Single Responsibility** — tiap class punya 1 tanggung jawab jelas: Controller ngurus alur, Form Request ngurus "boleh gak & valid gak", Model ngurus data. Makin gede project, makin kerasa manfaatnya.

## Praktik Kedelapan Belas: Policy

Logic otorisasi "boleh edit/hapus kalau pemilik ATAU admin" sekarang tercecer di **3 tempat**: `PostController::edit()`, `PostController::destroy()`, dan `UpdatePostRequest::authorize()` — isinya sama persis, ditulis manual 3x. Praktik ini nyatuin semuanya jadi **1 class Policy**.

Konsep baru: **Policy** (class khusus per-Model yang isinya "siapa boleh ngapain"), `@can` (Blade directive), dan `$this->authorize()` (helper di Controller).

### 1. Bikin Policy buat Post

```bash
php artisan make:policy PostPolicy --model=Post
```

**📁 File: `app/Policies/PostPolicy.php`** — isi method `update()` dan `delete()`:

```php
<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    public function update(User $user, Post $post): bool
    {
        return $post->user_id === $user->id || $user->role === 'admin';
    }

    public function delete(User $user, Post $post): bool
    {
        return $post->user_id === $user->id || $user->role === 'admin';
    }
}
```

Penjelasan:
- Tiap method Policy nerima 2 parameter: `$user` (siapa yang lagi login) dan `$post` (data yang mau diakses), return `true`/`false`.
- Nama method (`update`, `delete`) itu **konvensi** — nanti dipanggil pakai nama yang sama (`$this->authorize('update', $post)`, `@can('update', $post)`), Laravel otomatis nyambungin ke method yang cocok.
- Laravel 11/12 (versi project ini) **otomatis nemuin** Policy ini tanpa perlu didaftarin manual — cukup taruh di `app/Policies/`, namanya `{Model}Policy` (misal `PostPolicy` buat Model `Post`), otomatis "ke-link". Versi Laravel lama (sebelum 11) butuh daftarin manual di `AuthServiceProvider`.

### 2. Aktifin `$this->authorize()` di Controller

**📁 File: `app/Http/Controllers/Controller.php`** — tambah trait:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller
{
    use AuthorizesRequests;
}
```

> Trait ini yang ngasih method `$this->authorize(...)` ke **semua** Controller (karena `PostController` dkk extends `Controller` ini). Tanpa trait ini, `$this->authorize()` bakal error `Call to undefined method`.

### 3. Update Controller — ganti pengecekan manual

**📁 File: `app/Http/Controllers/PostController.php`** — update `edit()` dan `destroy()`:

```php
public function edit(Post $post)
{
    $this->authorize('update', $post);

    return view('posts.edit', ['post' => $post]);
}
```

```php
public function destroy(Post $post)
{
    $this->authorize('delete', $post);

    $post->delete();

    return redirect('/posts');
}
```

Penjelasan:
- `$this->authorize('update', $post)` — otomatis manggil `PostPolicy::update($user, $post)`. Kalau return `false`, Laravel otomatis `abort(403)` — gak perlu nulis manual lagi.
- Baris `if ($post->user_id !== auth()->id() && auth()->user()->role !== 'admin') { abort(403); }` yang lama **udah gak perlu**, dihapus total.

### 4. Update Form Request — pakai Policy juga

**📁 File: `app/Http/Requests/UpdatePostRequest.php`**:

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('post'));
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'image_url' => 'nullable|url',
        ];
    }
}
```

`$this->user()->can('update', $post)` — cara lain manggil Policy yang sama (`->can()` itu method bawaan Model `User`, hasilnya sama kayak `$this->authorize(...)`, cuma return boolean langsung bukan otomatis `abort()`). Aturan `update === user_id cocok || admin` sekarang **cuma ada 1 tempat** (`PostPolicy`), 3 pemanggil (`edit()`, `destroy()` via `authorize()`, dan Form Request via `can()`) tinggal manggil, gak nulis ulang logicnya.

### 5. Update View — pakai `@can` buat nampilin tombol

**📁 File: `resources/views/posts/index.blade.php`** dan **`resources/views/posts/show.blade.php`** — ganti kondisi manual:

```blade
@can('update', $post)
    <div class="mt-3 flex gap-2">
        <a href="/posts/{{ $post->id }}/edit"
            class="text-sm bg-blue-100 text-blue-700 px-3 py-1 rounded hover:bg-blue-200">
            Edit
        </a>

        <form method="POST" action="/posts/{{ $post->id }}">
            @csrf
            @method('DELETE')
            <button type="submit" onclick="return confirm('Yakin hapus?')"
                class="text-sm bg-red-100 text-red-700 px-3 py-1 rounded hover:bg-red-200">
                Hapus
            </button>
        </form>
    </div>
@endcan
```

Ganti `@if ($post->user_id === auth()->id() || auth()->user()->role === 'admin')` ... `@endif` yang lama jadi `@can('update', $post)` ... `@endcan`. Blade directive `@can` otomatis manggil Policy yang sama, sama kayak `$this->authorize()` di Controller.

> Karena tombol Edit & Hapus muncul/hilang bareng (satu blok), cukup 1 pengecekan `@can('update', $post)` buat dua-duanya. Kalau nanti aturan Edit & Hapus dibedain (misal admin cuma boleh Edit, gak boleh Hapus), tinggal pisah jadi `@can('update', $post)` buat tombol Edit dan `@can('delete', $post)` buat tombol Hapus — dan cukup ubah `PostPolicy`, gak perlu sentuh view lagi.

### 6. Cek hasilnya

1. Login sebagai pemilik post — tombol Edit/Hapus tetep muncul di `/posts` dan halaman detail, bisa edit/hapus normal.
2. Login sebagai user lain (bukan pemilik, bukan admin) — tombol Edit/Hapus **hilang** dari post itu, dan akses langsung `/posts/{id}/edit` tetep 403.
3. Login sebagai admin — tombol Edit/Hapus muncul di **semua** post, termasuk punya orang lain.
4. Ulang test dari Praktik 17 (fetch `PUT` via Console) — hasilnya harus tetep `403` buat non-pemilik/non-admin, soalnya `UpdatePostRequest` sekarang manggil Policy yang sama.

### Cheatsheet Troubleshooting

| Masalah | Solusi |
|---|---|
| Error `Call to undefined method PostController::authorize()` | Trait `AuthorizesRequests` belum ditambah ke `app/Http/Controllers/Controller.php` (step 2) |
| `@can('update', $post)` gak pernah nampilin apa-apa, padahal harusnya boleh | Cek nama method di `PostPolicy` — harus persis `update`/`delete`, dan parameter urutannya `(User $user, Post $post)` |
| Policy keliatan gak "nyambung" ke Model Post | Cek lokasi file harus di `app/Policies/PostPolicy.php` dan namanya persis `PostPolicy` — auto-discovery Laravel butuh penamaan konvensi ini |
| Semua orang (termasuk bukan pemilik) masih bisa lolos akses | Cek Controller/Form Request masih manggil `PostPolicy` yang bener, bukan sisa kode lama yang lupa dihapus |

### Ringkasan File yang Ditambah/Diubah di Praktik Kedelapan Belas

| File | Perubahan |
|---|---|
| `app/Policies/PostPolicy.php` | File baru — method `update()` dan `delete()`, pusat logic otorisasi Post |
| `app/Http/Controllers/Controller.php` | Tambah trait `AuthorizesRequests` |
| `app/Http/Controllers/PostController.php` | `edit()` & `destroy()` — ganti `abort(403)` manual jadi `$this->authorize(...)` |
| `app/Http/Requests/UpdatePostRequest.php` | `authorize()` — ganti logic manual jadi `$this->user()->can('update', ...)` |
| `resources/views/posts/index.blade.php`, `show.blade.php` | Ganti `@if (...) @endif` manual jadi `@can('update', $post) @endcan` |

### Ringkasan Praktik Kedelapan Belas

| Sebelum (Praktik 1-17) | Sesudah (Praktik 18) |
|---|---|
| Logic "boleh edit/hapus" ditulis manual di 3+ tempat | Logic terpusat di 1 class `PostPolicy` |
| Tiap tempat manggil `auth()->id()`/`auth()->user()->role` sendiri-sendiri | Semua manggil `PostPolicy` lewat `authorize()`/`can()`/`@can` |
| Ubah aturan = harus edit banyak file | Ubah aturan = cukup edit `PostPolicy` doang |

> Ini pola **DRY (Don't Repeat Yourself)** versi Authorization. Sama kayak Form Request misahin validasi dari Controller (Praktik 17), Policy misahin "siapa boleh ngapain" dari Controller, Form Request, DAN View sekaligus — jadi satu sumber kebenaran (single source of truth).

## Praktik Kesembilan Belas: API Resource + Testing pakai Thunder Client

Semua route yang udah dibikin sejauh ini (`routes/web.php`) itu **web routes** — return HTML/view, didesain buat dibuka langsung di browser pakai session login. Praktik ini bikin jalur baru: **API routes** — return **JSON** doang, didesain buat diakses dari luar (aplikasi mobile, frontend terpisah kayak React, atau di-test manual pakai tool kayak Thunder Client).

Konsep baru: **API Resource** (format JSON yang rapi), **Sanctum** (autentikasi API pakai token, gantiin session), dan **Thunder Client** (extension VS Code buat kirim request manual).

### 1. Install Laravel Sanctum + scaffolding API

Laravel 11/12 (versi project ini) **gak otomatis** nyediain `routes/api.php` — harus di-scaffold dulu:

```bash
php artisan install:api
```

Command ini otomatis:
- Install package `laravel/sanctum` (buat autentikasi token)
- Bikin file baru `routes/api.php`
- Daftarin routing API ke `bootstrap/app.php`
- Bikin migration tabel `personal_access_tokens` (tempat nyimpen token API)

Jalankan migration-nya:

```bash
php artisan migrate
```

**📁 File: `bootstrap/app.php`** — cek otomatis ketambahan baris `api: __DIR__.'/../routes/api.php'`:

```php
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    // ...
```

### 2. Bikin API Resource buat Post

API Resource itu cara **format** data Model jadi JSON yang rapi (kontrol field mana yang mau ditampilin, ganti nama field, dll) — daripada return Model mentah-mentah.

```bash
php artisan make:resource PostResource
```

**📁 File: `app/Http/Resources/PostResource.php`**:

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'body' => $this->body,
            'image_url' => $this->imageUrl(),
            'author' => $this->user->name ?? 'Tidak diketahui',
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
```

Penjelasan:
- `$this->id`, `$this->title`, dst — di dalam Resource, `$this` merujuk ke instance `Post` yang lagi diformat (bukan ke Resource itu sendiri).
- Field yang di-return **cuma yang didaftarin di sini** — kolom lain di database (misal `user_id` mentah) otomatis gak ikut ke-expose ke API, kecuali sengaja ditambahin.
- `'author' => $this->user->name` — bisa manggil relasi Eloquent (`belongsTo` dari Praktik 9) langsung di sini, hasilnya di-embed jadi field biasa di JSON.

### 3. Bikin Controller khusus API

```bash
php artisan make:controller Api/PostController --api
```

**📁 File: `app/Http/Controllers/Api/PostController.php`**:

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::latest()->paginate(6);

        return PostResource::collection($posts);
    }

    public function show(Post $post)
    {
        return new PostResource($post);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $validated['user_id'] = $request->user()->id;

        $post = Post::create($validated);

        return new PostResource($post);
    }

    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $post->update($validated);

        return new PostResource($post);
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        $post->delete();

        return response()->json(['message' => 'Post berhasil dihapus.']);
    }
}
```

Penjelasan:
- `PostResource::collection($posts)` — format banyak data sekaligus (buat `index()`), otomatis nge-loop tiap Post lewat `PostResource`.
- `new PostResource($post)` — format 1 data doang (buat `show()`, `store()`, `update()`).
- `$this->authorize('update', $post)` — **Policy dari Praktik 18 langsung kepakai lagi di sini**, gak perlu nulis ulang logic otorisasi. Ini bukti nyata manfaat Policy: satu aturan, dipakai di web DAN API.
- `$request->user()` — cara ambil user yang lagi login di context API (beda dari `auth()->id()` yang lebih umum dipakai di web, tapi fungsinya sama).

### 4. Bikin Route API — publik & yang butuh login

**📁 File: `routes/api.php`**:

```php
<?php

use App\Http\Controllers\Api\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (! Auth::attempt($request->only('email', 'password'))) {
        return response()->json(['message' => 'Email atau password salah.'], 401);
    }

    $user = Auth::user();
    $token = $user->createToken('api-token')->plainTextToken;

    return response()->json(['token' => $token]);
});

Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{post}', [PostController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/posts', [PostController::class, 'store']);
    Route::put('/posts/{post}', [PostController::class, 'update']);
    Route::delete('/posts/{post}', [PostController::class, 'destroy']);
});
```

Penjelasan:
- `/login` — endpoint khusus API buat login, **beda** dari `/login` versi web (Breeze). Nerima email+password, kalau bener, generate **token** (bukan session) pakai `createToken()` dari Sanctum, token itu yang dipakai buat "login" di request API berikutnya.
- `GET /posts` dan `GET /posts/{post}` — **publik**, gak butuh login (siapa aja boleh baca daftar/detail post).
- `POST`, `PUT`, `DELETE` — dibungkus `middleware('auth:sanctum')`, wajib nyertain token valid di header, kalau enggak otomatis `401 Unauthorized`.

### 5. Install Thunder Client & Test API

1. Buka **Extensions** di VS Code (`Ctrl+Shift+X`), cari **"Thunder Client"**, install.
2. Klik icon Thunder Client di sidebar kiri (ikon petir ⚡).
3. Klik **New Request**.

**Test A — GET daftar post (publik, gak perlu login):**
- Method: `GET`
- URL: `http://127.0.0.1:8000/api/posts`
- Klik **Send** — harus muncul response JSON isi daftar post (format sesuai `PostResource`).

**Test B — Login buat dapetin token:**
- Method: `POST`
- URL: `http://127.0.0.1:8000/api/login`
- Tab **Body** → pilih **JSON** → isi:
  ```json
  {
      "email": "admin@example.com",
      "password": "password"
  }
  ```
- Klik **Send** — response harus ada `{"token": "1|xxxxxxxxxxxxx..."}`. **Copy token itu.**

**Test C — Bikin post baru (butuh token):**
- Method: `POST`
- URL: `http://127.0.0.1:8000/api/posts`
- Tab **Headers** → tambah:
  - `Authorization` → `Bearer <token yang di-copy tadi>`
  - `Accept` → `application/json`
- Tab **Body** → JSON:
  ```json
  {
      "title": "Post dari Thunder Client",
      "body": "Ini dibikin lewat API, bukan lewat form."
  }
  ```
- Klik **Send** — harus muncul response JSON post yang baru dibikin.

**Test D — Coba tanpa token (harus ditolak):**
- Ulangi Test C, tapi **hapus** header `Authorization`.
- Klik **Send** — harus muncul `401 Unauthorized`.

### 6. Cek hasilnya

1. `GET /api/posts` berhasil tanpa login, response berupa JSON array.
2. `POST /api/login` pakai kredensial bener → dapet token; pakai kredensial salah → `401`.
3. `POST /api/posts` pakai token valid → post baru kesimpen, cek juga muncul di `/posts` versi web (satu database yang sama).
4. `POST /api/posts` tanpa token → `401`.
5. Coba `PUT /api/posts/{id}` buat post **bukan punya token itu** (pakai token user biasa, target post orang lain) → harus `403`, bukti Policy dari Praktik 18 tetep berlaku di jalur API.

### Cheatsheet Troubleshooting

| Masalah | Solusi |
|---|---|
| Route `/api/posts` muncul 404 | Cek `bootstrap/app.php` udah ada baris `api: __DIR__.'/../routes/api.php'` — kalau belum, jalankan ulang `php artisan install:api` |
| Error `Class "Laravel\Sanctum\HasApiTokens" not found` di Model `User` | Cek `app/Models/User.php` — trait `HasApiTokens` harus ditambah otomatis sama `install:api`, kalau kelewat tambah manual: `use Laravel\Sanctum\HasApiTokens;` + `use HasApiTokens;` di dalam class |
| `POST /api/login` selalu balikin 401 walau kredensial bener | Cek `Auth::attempt()` — pastiin kolom `email`/`password` di request cocok sama yang di database |
| `401 Unauthorized` padahal udah kirim token | Cek header `Authorization` formatnya harus **persis** `Bearer <token>` (ada spasi setelah "Bearer") |
| Response HTML/error 500 alih-alih JSON | Cek `bootstrap/app.php` ada `shouldRenderJsonWhen(fn ($request) => $request->is('api/*'))` — biar error di route API otomatis diformat JSON, bukan halaman HTML |

### Ringkasan File yang Ditambah/Diubah di Praktik Kesembilan Belas

| File | Perubahan |
|---|---|
| `routes/api.php` | File baru — route `/login`, `/posts` (publik & terautentikasi) |
| `bootstrap/app.php` | Otomatis ketambahan `api: __DIR__.'/../routes/api.php'` dari `install:api` |
| `app/Http/Resources/PostResource.php` | File baru — format JSON buat Post |
| `app/Http/Controllers/Api/PostController.php` | Controller baru khusus API, return `PostResource` |
| `database/migrations/..._create_personal_access_tokens_table.php` | Migration baru dari Sanctum (token storage) |

### Ringkasan Praktik Kesembilan Belas

| Sebelum (Praktik 1-18) | Sesudah (Praktik 19) |
|---|---|
| Cuma ada web routes (return HTML) | Ada API routes juga (return JSON) |
| Autentikasi pakai session (cookie) | Autentikasi API pakai token (Sanctum) |
| Testing manual lewat browser/klik-klik | Testing manual lewat Thunder Client (GET/POST/PUT/DELETE langsung) |
| Policy cuma kepake di web | Policy yang sama kepake juga di API (`$this->authorize(...)`) |

> Ini fondasi kalau nanti project Laravel-nya mau "dipisah" jadi backend doang, dikonsumsi frontend terpisah (React/Vue/mobile app) — pola `routes/api.php` + `Resource` + token Sanctum ini yang biasa dipakai buat itu.

## Roadmap Belajar Selanjutnya

Catatan urutan topik dari sini sampai siap bikin project Laravel sendiri / ujian praktik. Belum jadi Praktik detail, ini cuma daftar rencana biar gak lupa.

> ✅ Slug + Single Post View, Upload/Link Gambar, dan Database Seeder udah selesai jadi Praktik 12, 13, 14. Lanjut dari sini:

15. **Search & Filter** — cari post berdasarkan judul (`Post::where('title', 'like', ...)`).
16. **Pagination** — batesin jumlah post yang tampil per halaman (`Post::paginate(10)`), penting kalau datanya udah banyak.
17. **Form Request** — pindahin validasi dari Controller ke class terpisah (`php artisan make:request StorePostRequest`), lebih rapi buat form yang makin kompleks.
18. **Policy** — ganti pengecekan manual `if ($post->user_id !== auth()->id())` jadi `Gate`/`Policy` resmi Laravel (`php artisan make:policy PostPolicy --model=Post`), best practice buat Authorization.
19. **API Resource + Testing pakai Postman/Thunder Client** — bikin route API terpisah (`routes/api.php`), return data JSON pakai `Resource Controller`, terus di-test manual pakai **Postman** atau **Thunder Client** (extension VS Code). Ini dasar kalau nanti Laravel dipakai jadi backend buat frontend terpisah (React/Vue) atau aplikasi mobile.
20. **Testing otomatis (PHPUnit)** — nulis test kode biar mastiin fitur gak rusak pas nambah fitur baru, tanpa harus ngetes manual click-click terus.
21. **Deploy** — naikin project ke hosting beneran (Railway/Hostinger/VPS), bukan cuma jalan di localhost.

> Poin 1-18 udah cukup buat bikin project CRUD lengkap yang solid (blog, todo app, dsb) dan cukup buat kebanyakan ujian praktik Laravel dasar-menengah. Poin 19-21 topik lanjutan kalau mau proyeknya lebih serius / siap dipakai orang lain.
