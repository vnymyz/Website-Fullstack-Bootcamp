# Catatan Ringkasan Belajar Laravel

Ringkasan cepat semua konsep yang udah dipelajari di [`README.md`](./README.md) (21 Praktik). Buat murid yang lupa-lupa inget — baca ini dulu sebelum buka README lengkap buat detail step-by-step-nya.

## Daftar Isi

- [1. Konsep Dasar (MVC, Routing, Controller, Blade)](#1-konsep-dasar-mvc-routing-controller-blade)
- [2. Eloquent & Database](#2-eloquent--database)
- [3. Auth & Otorisasi](#3-auth--otorisasi)
- [4. Fitur CRUD Lengkap](#4-fitur-crud-lengkap)
- [5. Data Dummy (Seeder & Factory)](#5-data-dummy-seeder--factory)
- [6. Tampilan (Tailwind & Layout)](#6-tampilan-tailwind--layout)
- [7. API & Testing](#7-api--testing)
- [8. Cheatsheet Command Artisan](#8-cheatsheet-command-artisan)
- [9. Peta Praktik → Konsep](#9-peta-praktik--konsep)

---

## 1. Konsep Dasar (MVC, Routing, Controller, Blade)

**MVC** = pola pisahin tanggung jawab kode:
- **Model** (`app/Models/`) — data & logic ke database (Eloquent).
- **View** (`resources/views/`) — tampilan HTML (Blade template, `.blade.php`).
- **Controller** (`app/Http/Controllers/`) — jembatan Model ↔ View, nerima request & proses logic.

**Alur request:** `URL → routes/web.php → Controller → Model (kalau perlu) → View → Response`

**Routing dasar:**
```php
Route::get('/hello', [HelloController::class, 'index']);
Route::get('/hello/{name}', [HelloController::class, 'show']); // route parameter
```

**Blade dasar:**
```blade
{{ $variabel }}              {{-- echo, otomatis di-escape (aman dari XSS) --}}
@if(...) @else @endif
@foreach($items as $item) @endforeach
@forelse($items as $item) @empty ... @endforelse   {{-- ada fallback kalau kosong --}}
@extends('layouts.app')      {{-- pakai layout --}}
@section('content') ... @endsection
@yield('content')            {{-- "lubang" di layout, diisi @section --}}
{{-- komentar Blade --}}
```

---

## 2. Eloquent & Database

**Bikin Model + Migration:**
```bash
php artisan make:model NamaModel -m
php artisan migrate
```

**CRUD dasar Eloquent:**
```php
Post::all();                          // ambil semua
Post::find($id);                      // ambil 1 by id
Post::where('title', 'like', '%x%')->get();
Post::create([...]);                  // insert (butuh $fillable)
$post->update([...]);                 // update
$post->delete();                      // delete
```

**`$fillable`** — whitelist kolom yang boleh di-mass-assign (`Model::create([...])`). Kolom sensitif (misal `role`) **sengaja jangan** dimasukin biar gak bisa disusupin lewat form (mass assignment exploit).

**Relasi:**
```php
// Post belongsTo User (Post punya kolom user_id)
public function user() { return $this->belongsTo(User::class); }

// User hasMany Post
public function posts() { return $this->hasMany(Post::class); }
```
Pakainya: `$post->user->name`, `$user->posts()->count()`.

**Migration tambah kolom ke tabel yang udah ada:**
```bash
php artisan make:migration add_kolom_ke_tabel --table=nama_tabel
```
```php
Schema::table('posts', function (Blueprint $table) {
    $table->string('slug')->unique();                       // kolom biasa
    $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // foreign key
});
```

**Model Event** (auto-generate slug pas create):
```php
protected static function booted(): void
{
    static::creating(function (Post $post) {
        $post->slug = Str::slug($post->title);
    });
}
```

---

## 3. Auth & Otorisasi

**Authentication** (siapa kamu) vs **Authorization** (kamu boleh ngapain) — dua hal beda.

**Login/Register** — pakai Laravel Breeze:
```bash
composer require laravel/breeze --dev
php artisan breeze:install     # pilih "Blade with Alpine"
npm install && npm run dev
php artisan migrate
```

**Proteksi route pakai middleware:**
```php
Route::middleware('auth')->group(function () {
    // route yang wajib login
});
```

**Cek status login di Blade:**
```blade
@auth ... @else ... @endauth
```

**Role admin/user** — kolom `role` di tabel `users`, middleware custom:
```bash
php artisan make:middleware EnsureUserIsAdmin
```
```php
// bootstrap/app.php
$middleware->alias(['admin' => EnsureUserIsAdmin::class]);
```
```php
Route::middleware(['auth', 'admin'])->group(function () { ... });
```

**Form Request** — pindahin validasi + otorisasi ke class terpisah:
```bash
php artisan make:request StorePostRequest
```
```php
class StorePostRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }
    public function rules(): array { return ['title' => 'required|...']; }
}
```
Controller: ganti `Request $request` jadi `StorePostRequest $request`, `$request->validate([...])` jadi `$request->validated()`.

**Policy** — pusatin logic "boleh ngapain" per Model (gantiin `if ($post->user_id !== auth()->id())` yang tercecer di banyak tempat):
```bash
php artisan make:policy PostPolicy --model=Post
```
```php
public function update(User $user, Post $post): bool
{
    return $post->user_id === $user->id || $user->role === 'admin';
}
```
Pakainya:
```php
$this->authorize('update', $post);      // di Controller (butuh trait AuthorizesRequests di base Controller)
$this->user()->can('update', $post);    // di Form Request
@can('update', $post) ... @endcan       // di Blade
```

---

## 4. Fitur CRUD Lengkap

**Slug + Single Post View:**
```php
Route::get('/posts/{post:slug}', [PostController::class, 'show']); // route model binding by slug
```
> Route slug **harus** ditaruh setelah `/posts/create` (URL statis), biar gak ketuker.

**Upload/Link Gambar:**
```php
// migration: $table->string('image')->nullable();
// storage link (sekali aja, biar file bisa diakses browser):
php artisan storage:link
```
```php
// form: wajib enctype="multipart/form-data" kalau ada <input type="file">
if ($request->hasFile('image')) {
    $validated['image'] = $request->file('image')->store('posts', 'public');
} elseif ($request->filled('image_url')) {
    $validated['image'] = $request->image_url; // link luar (Unsplash dll)
}
```
```php
// Model helper buat tampilin, bedain path lokal vs link luar
public function imageUrl(): ?string
{
    return Str::startsWith($this->image, ['http://', 'https://'])
        ? $this->image
        : asset('storage/' . $this->image);
}
```

**Search & Filter:**
```php
Post::when($request->search, function ($query, $search) {
    $query->where('title', 'like', '%' . $search . '%');
})->latest()->get();
```
Form pakai `method="GET"` (bukan POST) biar kata kunci masuk ke query string (`?search=...`), bisa di-bookmark/share.

**Pagination:**
```php
Post::paginate(6);           // ganti ->get()
$posts->withQueryString();   // biar ?search=... tetep kebawa pas pindah halaman
```
```blade
{{ $posts->links() }}        {{-- tombol Previous/Next, otomatis styled --}}
```

---

## 5. Data Dummy (Seeder & Factory)

**Factory** = template generate data palsu. **Seeder** = script yang manggil Factory buat isi database.

```bash
php artisan make:factory PostFactory --model=Post
php artisan make:seeder PostSeeder
```
```php
// Model WAJIB pakai trait ini biar punya method ::factory()
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Post extends Model { use HasFactory; ... }
```
```php
// PostFactory.php
public function definition(): array
{
    return [
        'title' => fake()->sentence(4),
        'user_id' => User::factory(),   // wajib diisi biar recycle() di Seeder bisa jalan
    ];
}
```
```php
// PostSeeder.php
$users = User::all();
Post::factory(15)->recycle($users)->create();  // numpang user yang ada, gak bikin user baru tiap post
```
```php
// DatabaseSeeder.php
$this->call([UserSeeder::class, PostSeeder::class]); // urutan penting!
```

Jalanin:
```bash
php artisan migrate:fresh --seed   # reset database + isi data dummy sekaligus
```

---

## 6. Tampilan (Tailwind & Layout)

**Setup Tailwind** (Laravel 13 udah include, tinggal load):
```blade
{{-- di <head> layout --}}
@vite(['resources/css/app.css', 'resources/js/app.js'])
```
```bash
npm install
npm run dev   # jalanin bareng php artisan serve, biar CSS ke-compile live
```

**Container width per halaman** (biar gak semua halaman kena lebar sama):
```blade
{{-- di layouts/app.blade.php --}}
<main class="@yield('container', 'max-w-2xl') mx-auto p-6">

{{-- di halaman spesifik --}}
@section('container', 'max-w-6xl')
```

**Grid responsive:**
```blade
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
```

**Layout terpisah per konteks** — jangan paksa 1 layout buat semua:
- `layouts.app` — dipakai halaman abis login (sidebar).
- `layouts.public` — dipakai halaman publik/tamu (navbar simpel).
- `layouts.guest` — bawaan Breeze, dipakai halaman login/register (card di tengah).

---

## 7. API & Testing

### API (routes/api.php)

Beda dari web routes (return HTML), API routes return **JSON**, dipakai buat diakses dari luar (mobile app, frontend terpisah, atau di-test manual).

**Setup:**
```bash
php artisan install:api    # scaffold routes/api.php + install Sanctum (token auth)
php artisan migrate        # bikin tabel personal_access_tokens
```
> ⚠️ Kalau `bootstrap/app.php` atau `app/Models/User.php` udah dimodif manual sebelumnya, `install:api` bisa gagal auto-patch. Cek manual:
> - `bootstrap/app.php` harus ada `api: __DIR__.'/../routes/api.php',` di `withRouting(...)`.
> - `User.php` harus ada `use Laravel\Sanctum\HasApiTokens;` + trait `HasApiTokens` di class.

**API Resource** — format JSON custom (kontrol field mana yang di-expose):
```bash
php artisan make:resource PostResource
```
```php
class PostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'author' => $this->user->name,
        ];
    }
}
```
```php
PostResource::collection($posts);  // buat banyak data (index)
new PostResource($post);           // buat 1 data (show/store/update)
```

**Route API — publik vs butuh token:**
```php
// routes/api.php
Route::post('/login', function (Request $request) {
    if (! Auth::attempt($request->only('email', 'password'))) {
        return response()->json(['message' => 'Salah.'], 401);
    }
    $token = Auth::user()->createToken('api-token')->plainTextToken;
    return response()->json(['token' => $token]);
});

Route::get('/posts', [PostController::class, 'index']);        // publik
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/posts', [PostController::class, 'store']);   // wajib token
});
```

**Header wajib buat request yang butuh login:**
```
Authorization: Bearer <token>     ← WAJIB ada kata "Bearer" + spasi
Accept: application/json
```

**Testing manual pakai Thunder Client** (extension VS Code, gak perlu akun — beda dari Postman):
1. Install extension "Thunder Client" di VS Code.
2. New Request → pilih Method (GET/POST/PUT/DELETE) → isi URL.
3. Tab **Headers** → tambah `Authorization`/`Accept` kalau perlu.
4. Tab **Body** → pilih JSON → isi data.
5. Klik **Send**, cek Status Code & Response.

**Policy tetep berlaku di API** — `$this->authorize('update', $post)` di Controller API manggil `PostPolicy` yang sama kayak di web, gak perlu nulis ulang.

### Testing Otomatis (PHPUnit)

Beda dari testing manual (klik-klik browser), ini nulis **kode yang ngetes kode**.

`phpunit.xml` udah otomatis pakai SQLite in-memory buat testing — **gak nyentuh** database MySQL asli sama sekali.

```bash
php artisan make:test PostTest
php artisan test                    # jalanin semua test
php artisan test --filter=PostTest  # jalanin 1 file test doang
```

```php
class PostTest extends TestCase
{
    use RefreshDatabase;   // database di-reset tiap test, mulai bersih tiap kali

    public function test_guest_tidak_bisa_akses_halaman_posts(): void
    {
        $response = $this->get('/posts');
        $response->assertRedirect('/login');
    }

    public function test_user_bisa_bikin_post_baru(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post('/posts', ['title' => '...', 'body' => '...']);
        $response->assertRedirect('/posts');
        $this->assertDatabaseHas('posts', ['title' => '...']);
    }
}
```

Assertion umum: `assertStatus(200)`, `assertRedirect(...)`, `assertDatabaseHas(...)`.

> Tips: coba **sengaja rusak** kode (misal hapus proteksi Policy), jalanin test lagi — kalau ada yang FAIL, berarti test-nya beneran ngetes sesuatu (bukan cuma selalu hijau).

---

## 8. Cheatsheet Command Artisan

| Command | Fungsi |
|---|---|
| `php artisan serve` | Jalanin server lokal |
| `php artisan make:controller NamaController` | Bikin Controller |
| `php artisan make:model Nama -m` | Bikin Model + Migration sekaligus |
| `php artisan make:migration nama_migration --table=nama_tabel` | Bikin migration buat tabel yang udah ada |
| `php artisan migrate` | Jalanin migration |
| `php artisan migrate:fresh` | Drop semua tabel, migrate ulang dari nol |
| `php artisan migrate:fresh --seed` | Reset + isi data dummy sekaligus |
| `php artisan make:seeder NamaSeeder` | Bikin Seeder |
| `php artisan make:factory NamaFactory --model=Model` | Bikin Factory |
| `php artisan db:seed` | Jalanin seeder tanpa reset tabel |
| `php artisan make:middleware Nama` | Bikin Middleware |
| `php artisan make:request NamaRequest` | Bikin Form Request |
| `php artisan make:policy NamaPolicy --model=Model` | Bikin Policy |
| `php artisan make:resource NamaResource` | Bikin API Resource |
| `php artisan make:test NamaTest` | Bikin Test |
| `php artisan test` | Jalanin semua test |
| `php artisan storage:link` | Bikin symbolic link buat akses file upload |
| `php artisan install:api` | Scaffold API + install Sanctum |
| `php artisan tinker` | Console interaktif, coba-coba kode langsung |
| `php artisan route:clear` / `config:clear` / `optimize:clear` | Bersihin cache (route/config/semua) |
| `composer dump-autoload` | Refresh autoload class (kalau ada Class not found aneh) |

---

## 9. Peta Praktik → Konsep

| Praktik | Konsep Utama |
|---|---|
| 1 | Route + View dasar |
| 2 | Controller + route dinamis |
| 3 | Blade Layout (`@extends`/`@yield`) |
| 4 | Model, Migration, Eloquent |
| 5 | CRUD lengkap (Create, Update, Delete) |
| 6 | Tailwind CSS |
| 7 | SQLite → MySQL |
| 8 | Login/Register (Breeze) |
| 9 | Relasi Post-User (`belongsTo`/`hasMany`) |
| 10 | Role Admin vs User (middleware custom) |
| 11 | CRUD User Management lengkap |
| 12 | Slug + halaman detail post |
| 13 | Upload/link gambar |
| 14 | Database Seeder & Factory |
| 15 | Search & Filter |
| 16 | Pagination & Card Grid |
| 17 | Form Request |
| 18 | Policy |
| 19 | API Resource + Sanctum + Thunder Client |
| 20 | Testing Otomatis (PHPUnit) |
| 21 | Landing Page Publik + Sidebar Dashboard |

> Detail step-by-step tiap Praktik (kode lengkap, troubleshooting, cara test) ada di [`README.md`](./README.md).
