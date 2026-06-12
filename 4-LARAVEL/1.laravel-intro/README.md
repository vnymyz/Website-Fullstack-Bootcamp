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
