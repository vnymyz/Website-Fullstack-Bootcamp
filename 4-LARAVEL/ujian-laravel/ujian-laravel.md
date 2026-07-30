# Panduan Ujian Project — Platform Resep Masakan

Panduan step-by-step bikin project Laravel dari nol, temanya **Resep Masakan** — beda dari materi belajar (`laravel-intro` pakai tema "Post"), tapi **pola & konsepnya sama persis**. Tiap step nyambungin balik ke Praktik yang relevan di `laravel-intro/README.md`, biar ketauan ini bukan materi baru — cuma diterapin ke domain lain.

**Tech stack:** Laravel + PHP + Tailwind CSS.

**Tujuan akhir:** project ini rencananya bakal di-hosting, jadi kerjain dengan asumsi bakal diakses orang lain beneran.

> 💡 Ada **mockup tampilan statis** (HTML/CSS, gak perlu Laravel) di folder [`mockup/`](./mockup/) — buka `mockup/index.html` pakai Live Server buat liat gambaran akhir tampilan website-nya sebelum mulai coding. Cara jalaninnya ada di `mockup/README.md`.

## Daftar Isi

- [Soal Ujian Teori Laravel (20 Soal)](#soal-ujian-teori-laravel-20-soal)
- [0. Setup Laravel](#0-setup-laravel)
- [1. Struktur Folder Project](#1-struktur-folder-project)
- [2. Struktur & Query Database](#2-struktur--query-database)
- [3. Bikin Model & Migration Recipe](#3-bikin-model--migration-recipe)
- [4. Auth (Breeze) & Role Admin/User](#4-auth-breeze--role-adminuser)
- [5. Relasi Recipe-User](#5-relasi-recipe-user)
- [6. CRUD Recipe (Controller + Route + View)](#6-crud-recipe-controller--route--view)
- [7. Slug + Halaman Detail Resep](#7-slug--halaman-detail-resep)
- [8. Upload/Link Foto Resep](#8-uploadlink-foto-resep)
- [9. Search & Pagination](#9-search--pagination)
- [10. Form Request & Policy](#10-form-request--policy)
- [11. Seeder & Factory](#11-seeder--factory)
- [12. Tampilan (Tailwind, Layout Sidebar & Publik)](#12-tampilan-tailwind-layout-sidebar--publik)
- [13. API Resource](#13-api-resource)
- [14. Testing (PHPUnit)](#14-testing-phpunit)
- [Checklist Sebelum Submit](#checklist-sebelum-submit)
- [Catatan Deploy](#catatan-deploy)

---

## Soal Ujian Teori Laravel (20 Soal)

20 soal teori buat mastiin pemahaman materi Laravel yang udah dipelajari (referensi lengkap: `laravel-intro/README.md`, 21 Praktik). Jawab pakai kalimat sendiri, boleh sertain contoh kode kalau perlu.

Total nilai: 100 (tiap soal 5 poin).

### Daftar Isi Soal

- [Soal 1 — MVC](#soal-1)
- [Soal 2 — Route Statis vs Dinamis](#soal-2)
- [Soal 3 — Blade (`@extends`/`@section`/`@yield`)](#soal-3)
- [Soal 4 — Tanggung Jawab Controller](#soal-4)
- [Soal 5 — `$fillable` & Mass Assignment](#soal-5)
- [Soal 6 — Migration](#soal-6)
- [Soal 7 — Relasi `belongsTo`/`hasMany`](#soal-7)
- [Soal 8 — Authentication vs Authorization](#soal-8)
- [Soal 9 — Middleware](#soal-9)
- [Soal 10 — Role Admin/User](#soal-10)
- [Soal 11 — Route Model Binding](#soal-11)
- [Soal 12 — `enctype="multipart/form-data"`](#soal-12)
- [Soal 13 — Search pakai `when()`](#soal-13)
- [Soal 14 — Pagination & `withQueryString()`](#soal-14)
- [Soal 15 — Form Request](#soal-15)
- [Soal 16 — Policy](#soal-16)
- [Soal 17 — Web Routes vs API Routes](#soal-17)
- [Soal 18 — Sanctum](#soal-18)
- [Soal 19 — `RefreshDatabase` & SQLite in-memory](#soal-19)
- [Soal 20 — Seeder vs Factory](#soal-20)

### Soal

<a id="soal-1"></a>**1.** Jelasin konsep MVC (Model-View-Controller) di Laravel. Sebutin folder tempat masing-masing bagian itu ditaruh.

<a id="soal-2"></a>**2.** Apa bedanya route statis (`/posts/create`) sama route dinamis (`/posts/{id}`)? Kenapa route statis harus ditaruh sebelum route dinamis di `routes/web.php`?

<a id="soal-3"></a>**3.** Jelasin fungsi `@extends`, `@section`, dan `@yield` di Blade. Gambarin (boleh pakai kata-kata) gimana ketiganya saling nyambung.

<a id="soal-4"></a>**4.** Apa tanggung jawab Controller dalam alur MVC? Kenapa validasi & logic bisnis sebaiknya **jangan** ditaruh langsung di View?

<a id="soal-5"></a>**5.** Apa fungsi `$fillable` di Model Eloquent? Kenapa kolom kayak `role` sebaiknya **gak** dimasukin ke `$fillable` kalau bisa diisi user biasa lewat form?

<a id="soal-6"></a>**6.** Jelasin fungsi migration. Apa bedanya `php artisan make:migration nama --table=posts` sama `php artisan make:model Post -m`?

<a id="soal-7"></a>**7.** Jelasin relasi `belongsTo` dan `hasMany`. Kalau tabel `recipes` punya kolom `user_id`, di Model mana `belongsTo` ditulis dan di Model mana `hasMany` ditulis?

<a id="soal-8"></a>**8.** Apa beda **Authentication** dan **Authorization**? Kasih 1 contoh masing-masing dari project yang udah dibikin.

<a id="soal-9"></a>**9.** Apa fungsi Middleware? Jelasin gimana cara bikin middleware custom (misal buat ngecek role admin) dan gimana cara make middleware itu ke sebuah route.

<a id="soal-10"></a>**10.** Jelasin cara kerja sistem role admin/user di project kamu. Gimana caranya user biasa **gak bisa** akses halaman khusus admin walau dia tau URL-nya?

<a id="soal-11"></a>**11.** Apa itu **route model binding**? Jelasin bedanya `Route::get('/posts/{post}', ...)` sama `Route::get('/posts/{post:slug}', ...)`.

<a id="soal-12"></a>**12.** Kenapa `<form>` yang punya `<input type="file">` **wajib** ditambah `enctype="multipart/form-data"`? Apa yang terjadi kalau itu kelewat?

<a id="soal-13"></a>**13.** Jelasin cara kerja fitur Search pakai `when()` di Eloquent. Kenapa form search sebaiknya pakai `method="GET"`, bukan `POST`?

<a id="soal-14"></a>**14.** Apa manfaat Pagination selain soal tampilan? Jelasin fungsi `withQueryString()` pas dipakai bareng fitur Search.

<a id="soal-15"></a>**15.** Apa itu **Form Request**? Sebutin minimal 2 keuntungan pakai Form Request dibanding nulis `$request->validate([...])` manual di Controller.

<a id="soal-16"></a>**16.** Apa itu **Policy**? Jelasin bedanya cara pakai Policy di Controller (`$this->authorize(...)`), di Form Request (`$this->user()->can(...)`), sama di Blade (`@can`).

<a id="soal-17"></a>**17.** Apa beda **web routes** (`routes/web.php`) sama **API routes** (`routes/api.php`)? Kenapa API routes biasanya return JSON, bukan HTML?

<a id="soal-18"></a>**18.** Jelasin alur autentikasi API pakai Laravel Sanctum — mulai dari login sampai bisa akses endpoint yang butuh token. Header apa yang wajib disertain di tiap request setelah dapet token?

<a id="soal-19"></a>**19.** Apa fungsi `RefreshDatabase` di testing? Kenapa `phpunit.xml` di-setup pakai SQLite in-memory, bukan database MySQL yang sama kayak development?

<a id="soal-20"></a>**20.** Apa beda **Seeder** dan **Factory**? Kenapa `Post::factory(15)->recycle($users)->create()` butuh variabel `$users` yang udah di-generate duluan?

---

## 0. Setup Laravel

```bash
cd D:/laragon/www
composer create-project laravel/laravel resep-masakan
cd resep-masakan
copy .env.example .env
php artisan key:generate
```

Atur `.env`, pakai MySQL (sesuai Praktik 7):

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=resep_masakan
DB_USERNAME=root
DB_PASSWORD=
```

Bikin database `resep_masakan` lewat phpMyAdmin, terus jalanin:

```bash
php artisan migrate
```

Setup Tailwind (Laravel 13 udah include):

```bash
npm install
npm run dev
```

Jalanin server:

```bash
php artisan serve
```

---

## 1. Struktur Folder Project

```
resep-masakan/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── RecipeController.php
│   │   │   ├── ProfileController.php
│   │   │   ├── Admin/
│   │   │   │   └── UserController.php
│   │   │   └── Api/
│   │   │       └── RecipeController.php
│   │   ├── Middleware/
│   │   │   └── EnsureUserIsAdmin.php
│   │   ├── Requests/
│   │   │   ├── StoreRecipeRequest.php
│   │   │   └── UpdateRecipeRequest.php
│   │   └── Resources/
│   │       └── RecipeResource.php
│   ├── Models/
│   │   ├── Recipe.php
│   │   └── User.php
│   └── Policies/
│       └── RecipePolicy.php
│
├── database/
│   ├── migrations/
│   │   ├── ..._create_users_table.php
│   │   ├── ..._create_recipes_table.php
│   │   └── ..._add_role_to_users_table.php
│   ├── factories/
│   │   ├── UserFactory.php
│   │   └── RecipeFactory.php
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── UserSeeder.php
│       └── RecipeSeeder.php
│
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   ├── app.blade.php        (sidebar, area login)
│   │   │   ├── public.blade.php     (navbar, area publik)
│   │   │   └── sidebar.blade.php
│   │   ├── recipes/
│   │   │   ├── index.blade.php
│   │   │   ├── create.blade.php
│   │   │   ├── edit.blade.php
│   │   │   └── show.blade.php
│   │   ├── admin/
│   │   │   └── users/
│   │   │       ├── index.blade.php
│   │   │       └── create.blade.php
│   │   ├── home.blade.php           (landing page publik)
│   │   └── dashboard.blade.php
│   └── css/
│       └── app.css
│
├── routes/
│   ├── web.php
│   ├── api.php
│   └── auth.php
│
├── storage/
│   └── app/
│       └── public/
│           └── recipes/             (foto hasil upload)
│
├── tests/
│   └── Feature/
│       └── RecipeTest.php
│
├── public/
│   └── index.php                    (front controller)
│
└── .env                             (jangan di-commit)
```

| Folder/File                   | Fungsi                                                                  |
| ----------------------------- | ----------------------------------------------------------------------- |
| `app/Models/`                 | Model Eloquent (`Recipe.php`, `User.php`) — representasi tabel database |
| `app/Http/Controllers/`       | Logic nerima request & nentuin response (`RecipeController.php`)        |
| `app/Http/Controllers/Admin/` | Controller khusus admin (`UserController.php` — kelola user)            |
| `app/Http/Middleware/`        | Filter request sebelum nyampe Controller (`EnsureUserIsAdmin.php`)      |
| `app/Http/Requests/`          | Form Request — validasi + otorisasi terpisah dari Controller            |
| `app/Policies/`               | Aturan "siapa boleh ngapain" per Model (`RecipePolicy.php`)             |
| `database/migrations/`        | Blueprint struktur tabel, version control buat database                 |
| `database/factories/`         | Template generate data dummy                                            |
| `database/seeders/`           | Script isi data dummy ke database                                       |
| `resources/views/`            | File Blade (`.blade.php`) — tampilan HTML                               |
| `resources/views/layouts/`    | Layout dipakai bareng banyak halaman (`app`, `public`, `sidebar`)       |
| `resources/views/recipes/`    | View CRUD resep (`index`, `create`, `edit`, `show`)                     |
| `routes/web.php`              | Route buat halaman biasa (return HTML)                                  |
| `routes/api.php`              | Route buat API (return JSON)                                            |
| `public/`                     | Folder diakses publik, entry point `index.php`                          |
| `storage/app/public/`         | Tempat file upload (foto resep) disimpen                                |
| `tests/Feature/`              | Test otomatis (PHPUnit)                                                 |
| `.env`                        | Konfigurasi lokal (database, app key) — **jangan di-commit**            |

---

## 2. Struktur & Query Database

**Tabel `users`** (bawaan Breeze, ditambah 1 kolom):

| Kolom      | Tipe              | Keterangan                       |
| ---------- | ----------------- | -------------------------------- |
| `id`       | `BIGINT UNSIGNED` | Primary key                      |
| `name`     | `VARCHAR(255)`    | Nama user                        |
| `email`    | `VARCHAR(255)`    | Unique                           |
| `password` | `VARCHAR(255)`    | Di-hash                          |
| `role`     | `VARCHAR(255)`    | Default `'user'`, bisa `'admin'` |

**Tabel `recipes`:**

| Kolom                      | Tipe                     | Keterangan                    |
| -------------------------- | ------------------------ | ----------------------------- |
| `id`                       | `BIGINT UNSIGNED`        | Primary key                   |
| `user_id`                  | `BIGINT UNSIGNED`        | Foreign key ke `users.id`     |
| `title`                    | `VARCHAR(255)`           | Judul resep                   |
| `slug`                     | `VARCHAR(255)`           | Unique, buat URL              |
| `ingredients`              | `TEXT`                   | Bahan-bahan                   |
| `steps`                    | `TEXT`                   | Cara masak                    |
| `image`                    | `VARCHAR(255)`, nullable | Path file lokal atau link URL |
| `created_at`, `updated_at` | `TIMESTAMP`              | Otomatis Laravel              |

Query SQL gambaran akhirnya (referensi doang — bikinnya tetep lewat migration, lihat step 3):

```sql
ALTER TABLE users ADD COLUMN role VARCHAR(255) NOT NULL DEFAULT 'user' AFTER email;

CREATE TABLE recipes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    ingredients TEXT NOT NULL,
    steps TEXT NOT NULL,
    image VARCHAR(255) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    UNIQUE KEY recipes_slug_unique (slug),
    CONSTRAINT recipes_user_id_foreign FOREIGN KEY (user_id)
        REFERENCES users (id) ON DELETE CASCADE
);
```

---

## 3. Bikin Model & Migration Recipe

```bash
php artisan make:model Recipe -m
```

**📁 `database/migrations/..._create_recipes_table.php`:**

```php
public function up(): void
{
    Schema::create('recipes', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->string('title');
        $table->string('slug')->unique();
        $table->text('ingredients');
        $table->text('steps');
        $table->string('image')->nullable();
        $table->timestamps();
    });
}
```

```bash
php artisan migrate
```

**📁 `app/Models/Recipe.php`:**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Recipe extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'ingredients', 'steps', 'image', 'user_id'];

    protected static function booted(): void
    {
        static::creating(function (Recipe $recipe) {
            $recipe->slug = static::generateUniqueSlug($recipe->title);
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
}
```

> Pola ini persis Praktik 4 (Model & Migration) + Praktik 12 (slug otomatis lewat Model Event) + Praktik 13 (`imageUrl()` helper).

---

## 4. Auth (Breeze) & Role Admin/User

```bash
composer require laravel/breeze --dev
php artisan breeze:install
# pilih: Blade with Alpine, testing pakai PHPUnit
npm install && npm run dev
php artisan migrate
```

Migration tambah kolom `role`:

```bash
php artisan make:migration add_role_to_users_table --table=users
```

```php
public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('role')->default('user')->after('email');
    });
}
```

```bash
php artisan migrate
```

Middleware admin:

```bash
php artisan make:middleware EnsureUserIsAdmin
```

```php
// app/Http/Middleware/EnsureUserIsAdmin.php
public function handle(Request $request, Closure $next): Response
{
    if ($request->user()?->role !== 'admin') {
        abort(403, 'Halaman ini khusus admin.');
    }

    return $next($request);
}
```

**📁 `bootstrap/app.php`** — daftarin alias:

```php
use App\Http\Middleware\EnsureUserIsAdmin;

->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias(['admin' => EnsureUserIsAdmin::class]);
})
```

Set akun pertama jadi admin lewat tinker:

```bash
php artisan tinker
```

```php
$user = App\Models\User::find(1);
$user->role = 'admin';
$user->save();
```

> Sama persis Praktik 8 (Breeze) + Praktik 10 (role & middleware custom).

---

## 5. Relasi Recipe-User

**📁 `app/Models/Recipe.php`** — tambah:

```php
public function user()
{
    return $this->belongsTo(User::class);
}
```

**📁 `app/Models/User.php`** — tambah:

```php
public function recipes()
{
    return $this->hasMany(Recipe::class);
}
```

> Sama persis Praktik 9.

---

## 6. CRUD Recipe (Controller + Route + View)

```bash
php artisan make:controller RecipeController
```

**📁 `app/Http/Controllers/RecipeController.php`** (versi dasar sebelum ditambah Form Request/Policy di step 10):

```php
<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
    public function index()
    {
        $recipes = Recipe::latest()->get();

        return view('recipes.index', ['recipes' => $recipes]);
    }

    public function create()
    {
        return view('recipes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'ingredients' => 'required|string',
            'steps' => 'required|string',
        ]);

        $validated['user_id'] = auth()->id();

        Recipe::create($validated);

        return redirect('/recipes');
    }

    public function edit(Recipe $recipe)
    {
        if ($recipe->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403);
        }

        return view('recipes.edit', ['recipe' => $recipe]);
    }

    public function update(Request $request, Recipe $recipe)
    {
        if ($recipe->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'ingredients' => 'required|string',
            'steps' => 'required|string',
        ]);

        $recipe->update($validated);

        return redirect('/recipes');
    }

    public function destroy(Recipe $recipe)
    {
        if ($recipe->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403);
        }

        $recipe->delete();

        return redirect('/recipes');
    }
}
```

**📁 `routes/web.php`:**

```php
use App\Http\Controllers\RecipeController;

Route::middleware('auth')->group(function () {
    Route::get('/recipes', [RecipeController::class, 'index']);
    Route::get('/recipes/create', [RecipeController::class, 'create']);
    Route::post('/recipes', [RecipeController::class, 'store']);
    Route::get('/recipes/{recipe}/edit', [RecipeController::class, 'edit']);
    Route::put('/recipes/{recipe}', [RecipeController::class, 'update']);
    Route::delete('/recipes/{recipe}', [RecipeController::class, 'destroy']);
});
```

> Sama persis Praktik 5. View (`create.blade.php`, `edit.blade.php`, `index.blade.php`) polanya sama kayak `posts/*.blade.php` di `laravel-intro` — tinggal ganti field `title`/`body` jadi `title`/`ingredients`/`steps`, dan sesuain teks labelnya.

---

## 7. Slug + Halaman Detail Resep

**📁 `routes/web.php`** — tambah setelah `/recipes/create`:

```php
Route::get('/recipes/{recipe:slug}', [RecipeController::class, 'show']);
```

**📁 `app/Http/Controllers/RecipeController.php`** — tambah:

```php
public function show(Recipe $recipe)
{
    return view('recipes.show', ['recipe' => $recipe]);
}
```

> Sama persis Praktik 12. Inget: route `{recipe:slug}` harus ditaruh **setelah** `/recipes/create`, **sebelum** `{recipe}/edit`.

---

## 8. Upload/Link Foto Resep

```bash
php artisan storage:link
```

**📁 `app/Http/Controllers/RecipeController.php`** — update `store()` & `update()`:

```php
$validated = $request->validate([
    'title' => 'required|string|max:255',
    'ingredients' => 'required|string',
    'steps' => 'required|string',
    'image' => 'nullable|image|max:2048',
    'image_url' => 'nullable|url',
]);

if ($request->hasFile('image')) {
    $validated['image'] = $request->file('image')->store('recipes', 'public');
} elseif ($request->filled('image_url')) {
    $validated['image'] = $request->image_url;
}

unset($validated['image_url']);
```

Form (`create.blade.php`/`edit.blade.php`) wajib `enctype="multipart/form-data"` + 2 input (`type="file"` dan `type="url"`).

> Sama persis Praktik 13.

---

## 9. Search & Pagination

**📁 `app/Http/Controllers/RecipeController.php`** — update `index()`:

```php
public function index(Request $request)
{
    $recipes = Recipe::when($request->search, function ($query, $search) {
        $query->where('title', 'like', '%' . $search . '%');
    })->latest()->paginate(6)->withQueryString();

    return view('recipes.index', [
        'recipes' => $recipes,
        'search' => $request->search,
    ]);
}
```

View: form `method="GET"` buat search, `@forelse`/`@empty`, `{{ $recipes->links() }}` di bawah grid.

> Sama persis Praktik 15 + 16.

---

## 10. Form Request & Policy

```bash
php artisan make:request StoreRecipeRequest
php artisan make:request UpdateRecipeRequest
php artisan make:policy RecipePolicy --model=Recipe
```

**📁 `app/Policies/RecipePolicy.php`:**

```php
public function update(User $user, Recipe $recipe): bool
{
    return $recipe->user_id === $user->id || $user->role === 'admin';
}

public function delete(User $user, Recipe $recipe): bool
{
    return $recipe->user_id === $user->id || $user->role === 'admin';
}
```

**📁 `app/Http/Controllers/Controller.php`** — tambah trait:

```php
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller
{
    use AuthorizesRequests;
}
```

**📁 `app/Http/Requests/StoreRecipeRequest.php`:**

```php
public function authorize(): bool { return auth()->check(); }

public function rules(): array
{
    return [
        'title' => 'required|string|max:255',
        'ingredients' => 'required|string',
        'steps' => 'required|string',
        'image' => 'nullable|image|max:2048',
        'image_url' => 'nullable|url',
    ];
}
```

**📁 `app/Http/Requests/UpdateRecipeRequest.php`** — sama rules-nya, plus:

```php
public function authorize(): bool
{
    return $this->user()->can('update', $this->route('recipe'));
}
```

Update `RecipeController` — ganti type-hint `Request` jadi `StoreRecipeRequest`/`UpdateRecipeRequest`, `$request->validate([...])` jadi `$request->validated()`, dan `edit()`/`destroy()` pakai `$this->authorize('update'/'delete', $recipe)` gantiin `if` manual.

> Sama persis Praktik 17 + 18.

---

## 11. Seeder & Factory

```bash
php artisan make:factory RecipeFactory --model=Recipe
php artisan make:seeder UserSeeder
php artisan make:seeder RecipeSeeder
```

**📁 `database/factories/RecipeFactory.php`:**

```php
public function definition(): array
{
    return [
        'title' => fake()->words(3, true) . ' ' . fake()->randomElement(['Goreng', 'Bakar', 'Kukus', 'Rebus']),
        'ingredients' => fake()->paragraphs(2, true),
        'steps' => fake()->paragraphs(3, true),
        'image' => fake()->randomElement([
            'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=640&q=80',
            'https://images.unsplash.com/photo-1567620905732-2d1ec7ab7445?w=640&q=80',
            'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=640&q=80',
        ]),
        'user_id' => User::factory(),
    ];
}
```

**📁 `database/seeders/RecipeSeeder.php`:**

```php
public function run(): void
{
    $users = User::all();

    Recipe::factory(20)->recycle($users)->create();
}
```

**📁 `database/seeders/DatabaseSeeder.php`:**

```php
public function run(): void
{
    $this->call([UserSeeder::class, RecipeSeeder::class]);
}
```

```bash
php artisan migrate:fresh --seed
```

> Sama persis Praktik 14.

---

## 12. Tampilan (Tailwind, Layout Sidebar & Publik)

Copy pola layout dari `laravel-intro`:

- `layouts/app.blade.php` — sidebar (Dashboard, Recipes, User Management, Profile, Logout), buat halaman abis login.
- `layouts/sidebar.blade.php` — partial sidebar-nya.
- `layouts/public.blade.php` — navbar simpel, buat landing page.
- `home.blade.php` — landing page publik, nampilin resep terbaru.

Card resep di `recipes/index.blade.php` & `home.blade.php` — grid responsive:

```blade
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach ($recipes as $recipe)
        <div class="bg-white border rounded-lg shadow-sm overflow-hidden flex flex-col">
            @if ($recipe->imageUrl())
                <img src="{{ $recipe->imageUrl() }}" class="w-full h-48 object-cover">
            @endif
            <div class="p-4 flex flex-col flex-1">
                <h2 class="font-semibold text-lg">{{ $recipe->title }}</h2>
                <p class="text-gray-600 text-sm line-clamp-3 flex-1">{{ $recipe->ingredients }}</p>
            </div>
        </div>
    @endforeach
</div>
```

> Sama persis Praktik 6, 16, 21.

---

## 13. API Resource

```bash
php artisan install:api
php artisan migrate
php artisan make:resource RecipeResource
php artisan make:controller Api/RecipeController --api
```

**📁 `app/Http/Resources/RecipeResource.php`:**

```php
public function toArray(Request $request): array
{
    return [
        'id' => $this->id,
        'title' => $this->title,
        'slug' => $this->slug,
        'image_url' => $this->imageUrl(),
        'author' => $this->user->name,
    ];
}
```

**📁 `routes/api.php`:**

```php
Route::get('/recipes', [Api\RecipeController::class, 'index']); // publik
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/recipes', [Api\RecipeController::class, 'store']);
});
```

> Sama persis Praktik 19.

---

## 14. Testing (PHPUnit)

```bash
php artisan make:test RecipeTest
```

```php
public function test_guest_tidak_bisa_akses_halaman_recipes(): void
{
    $response = $this->get('/recipes');
    $response->assertRedirect('/login');
}

public function test_user_gak_bisa_edit_resep_orang_lain(): void
{
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $recipe = Recipe::factory()->create(['user_id' => $owner->id]);

    $response = $this->actingAs($other)->put("/recipes/{$recipe->id}", [
        'title' => 'Diubah paksa', 'ingredients' => '...', 'steps' => '...',
    ]);

    $response->assertStatus(403);
}
```

```bash
php artisan test
```

> Sama persis Praktik 20.

---

## Checklist Sebelum Submit

- [ ] `php artisan migrate:fresh --seed` jalan tanpa error
- [ ] Bisa register, login, logout
- [ ] User biasa cuma liat tombol Edit/Hapus di resep miliknya sendiri
- [ ] Coba akses `/recipes/{id}/edit` milik orang lain langsung lewat URL → harus 403
- [ ] Admin bisa edit/hapus semua resep
- [ ] Search & Pagination di halaman daftar resep jalan bareng (`?search=...&page=2`)
- [ ] Upload foto (file & link URL dua-duanya jalan)
- [ ] `php artisan test` — semua test PASS
- [ ] Landing page (`/`) bisa diakses tanpa login
- [ ] `.env` **gak ikut** di-commit ke git (ada di `.gitignore`)

---

## Catatan Deploy

Project ini rencananya di-hosting. Sebelum deploy, pastiin:

- `APP_DEBUG=false` di `.env` produksi (jangan expose error detail ke publik)
- `APP_KEY` di-generate ulang di server (`php artisan key:generate`)
- Database produksi **beda** dari database development — jangan pernah jalanin `migrate:fresh` di server produksi (itu ngapus semua data asli)
- `php artisan storage:link` harus dijalanin juga di server, biar upload foto bisa diakses
