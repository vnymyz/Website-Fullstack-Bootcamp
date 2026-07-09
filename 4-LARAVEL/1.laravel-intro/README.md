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

## Roadmap Belajar Selanjutnya

Catatan urutan topik dari sini sampai siap bikin project Laravel sendiri / ujian praktik. Belum jadi Praktik detail, ini cuma daftar rencana biar gak lupa.

11. **Slug + Single Post View** — ganti `/posts/{id}` jadi `/posts/{slug}` (URL pakai judul, bukan angka), plus halaman detail satu post (`/posts/judul-post`, terpisah dari halaman list).
12. **Upload/Link Gambar** — Post bisa punya gambar, dua cara: upload file (`storage/`) atau isi link URL (misal dari Unsplash).
13. **Search & Filter** — cari post berdasarkan judul (`Post::where('title', 'like', ...)`).
14. **Pagination** — batesin jumlah post yang tampil per halaman (`Post::paginate(10)`), penting kalau datanya udah banyak.
15. **Form Request** — pindahin validasi dari Controller ke class terpisah (`php artisan make:request StorePostRequest`), lebih rapi buat form yang makin kompleks.
16. **Policy** — ganti pengecekan manual `if ($post->user_id !== auth()->id())` jadi `Gate`/`Policy` resmi Laravel (`php artisan make:policy PostPolicy --model=Post`), best practice buat Authorization.
17. **API Resource + Testing pakai Postman/Thunder Client** — bikin route API terpisah (`routes/api.php`), return data JSON pakai `Resource Controller`, terus di-test manual pakai **Postman** atau **Thunder Client** (extension VS Code). Ini dasar kalau nanti Laravel dipakai jadi backend buat frontend terpisah (React/Vue) atau aplikasi mobile.
18. **Testing otomatis (PHPUnit)** — nulis test kode biar mastiin fitur gak rusak pas nambah fitur baru, tanpa harus ngetes manual click-click terus.
19. **Deploy** — naikin project ke hosting beneran (Railway/Hostinger/VPS), bukan cuma jalan di localhost.

> Poin 1-16 udah cukup buat bikin project CRUD lengkap yang solid (blog, todo app, dsb) dan cukup buat kebanyakan ujian praktik Laravel dasar-menengah. Poin 17-19 topik lanjutan kalau mau proyeknya lebih serius / siap dipakai orang lain.
