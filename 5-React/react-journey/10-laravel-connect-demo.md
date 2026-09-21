# Sesi 10 — Laravel Connect Demo

Tujuan: buktiin React bisa ngobrol sama backend yang udah kamu kenal (Laravel). Ini **bukan** project fitur baru — cuma demo kecil buat nunjukin cara nyambungnya.

App: Notes CRUD kecil + login, 1-2 tabel doang.

**Hasil akhir sesi ini:**
- Backend Laravel (`notes-api`) dengan endpoint login + CRUD notes yang dijaga token.
- React: halaman login (email + password), halaman notes (tambah/edit/hapus), redirect otomatis kalau belum login.
- Kamu bisa nunjukin di DevTools Network: header `Authorization: Bearer ...` beneran terkirim.

**Sebelum mulai (cek dulu):**
- [ ] Sesi 8 selesai (kamu ngerti `AuthContext`, `useAuth`, `ProtectedRoute`).
- [ ] Sesi 9 selesai (pola fetch + CRUD di React udah kamu kuasai).
- [ ] **Laragon** terpasang (bawa PHP, MySQL, Composer). Cek di terminal Laragon: `php -v` dan `composer -V` keluar nomor versi.
- [ ] `npm run dev` di `learn-react/` masih jalan seperti biasa.

## Cara Kerja Panduan Ini

Sama kayak sesi 9: tiap file dikasih **kode lengkap**, tinggal copy seluruh isinya.

- **File:** path file. Ada **dua project** di sesi ini, jadi path-nya ditulis lengkap dengan nama project:
  - `notes-api/...` = project Laravel, di `C:\laragon\www\notes-api\`.
  - `learn-react/...` = project React yang udah kamu punya.
- **Isi lengkap:** copy semuanya, **ganti seluruh isi** file lama (atau isi file baru).
- **Terminal:** tiap blok perintah ditulis di terminal folder mana dijalaninnya.
- **Penanda `BARU`** di dalam kode = baris yang ditambahin dibanding file bawaan/sebelumnya, biar kamu tau apa yang berubah dan di mana.

**Gambaran besar dulu — ada DUA project terpisah yang jalan bersamaan:**

```
Browser
  │
  ├── http://localhost:5173  ← React (learn-react/)    : cuma tampilan
  │         │
  │         │  fetch(...) dengan header Authorization
  │         ▼
  └── http://localhost:8000  ← Laravel (notes-api/)    : logic + database
                │
                ▼
             MySQL (Laragon)
```

React gak pernah nyentuh database. Dia cuma **minta data lewat HTTP**, persis kayak ke `json-server` di sesi 6 — bedanya sekarang server-nya beneran dan minta bukti login (token).

**Peta langkah:**

| Bagian | Langkah | Isi |
|---|---|---|
| **A. Laravel** | 1 | Bikin project, database, `.env` |
| | 2 | `User` (token) + seeder + tabel `notes` |
| | 3 | Route API + controller + resource |
| **B. Testing API** | 4 | Thunder Client (manual) + Pest (otomatis) |
| **C. Sambungan** | 5 | CORS |
| **D. React** | 6 | `AuthContext` versi token |
| | 7 | Halaman Login |
| | 8 | `App.jsx` + `Navbar.jsx` |
| | 9 | Halaman Notes (CRUD) |
| | 10 | Tes alur lengkap |

---

# Bagian A — Laravel API

## Langkah 1 — Bikin Project, Database, `.env`

**Sanctum** = paket resmi Laravel buat token API. Backend bikin token pas login, React nyimpen dan ngirimnya lagi di tiap request.

### 1a. Bikin project (terpisah dari `learn-react/`)

**Terminal Laragon** (Laragon → tombol **Terminal**), jalanin di folder `C:\laragon\www`:

```
cd C:\laragon\www
composer create-project laravel/laravel notes-api
cd notes-api
php artisan install:api
```

- `create-project` butuh beberapa menit (Composer download banyak paket).
- `install:api` nambahin file `routes/api.php`, masang Sanctum, dan bikin migration tabel token. Kalau ditanya "run migrations?", jawab **no** dulu — database belum kita siapin.

Mulai sekarang, **semua perintah `php artisan ...` dijalanin di terminal folder `C:\laragon\www\notes-api`**.

### 1b. Bikin database MySQL

1. Nyalain Laragon: **Start All**.
2. Buka **HeidiSQL** (tombol Database di Laragon) — user `root`, password kosong (default Laragon).
3. Bikin database baru bernama `notes_api`.

### 1c. `.env`

**File:** `notes-api/.env`

**Cari 7 baris yang diawali `DB_`** (biasanya di bagian tengah; kalau `DB_CONNECTION=sqlite` dan sisanya dikomentari `#`, itu bawaan Laravel baru) dan **ganti** dengan:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=notes_api
DB_USERNAME=root
DB_PASSWORD=
```

Baris lain di `.env` **jangan diubah**.

### Cek langkah 1

- [ ] Folder `C:\laragon\www\notes-api` ada, di dalamnya ada `routes/api.php`.
- [ ] Database `notes_api` ada di HeidiSQL (masih kosong).
- [ ] `.env` nunjuk ke `notes_api`.

---

## Langkah 2 — `User` (Token), Seeder, dan Tabel `notes`

### 2a. Aktifin token di model `User`

**File:** `notes-api/app/Models/User.php`

**Isi lengkap** (ganti seluruh isinya). Ada **3 hal `BARU`**:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;                       // BARU 1: import trait token

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;           // BARU 2: tambah HasApiTokens

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // BARU 3: satu user punya banyak catatan
    public function notes()
    {
        return $this->hasMany(Note::class);
    }
}
```

**Kalau `User.php`-mu beda dari ini** (misal versi Laravel lebih lama): jangan ganti seluruh file. Pertahankan isi lamamu dan tambahkan cuma 3 hal bertanda `BARU` di atas.

### 2b. Model + migration `notes`

**Terminal `notes-api`:**

```
php artisan make:model Note -m
```

Ini bikin 2 file: `app/Models/Note.php` dan sebuah file migration di `database/migrations/` (namanya diawali tanggal, diakhiri `_create_notes_table.php`).

**File:** `notes-api/app/Models/Note.php`

**Isi lengkap** (ganti seluruh isinya):

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    // Kolom yang boleh diisi dari request.
    // user_id sengaja GAK ada di sini: diisi otomatis dari user yang login,
    // biar orang gak bisa nulis catatan atas nama user lain.
    protected $fillable = ['judul', 'isi'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

**File:** `notes-api/database/migrations/xxxx_xx_xx_xxxxxx_create_notes_table.php` (buka file migration yang namanya berakhiran `_create_notes_table.php`)

**Isi lengkap** (ganti seluruh isinya). Bagian `BARU` = 3 kolom yang kamu tambah:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();   // BARU: tiap catatan milik satu user
            $table->string('judul');                       // BARU
            $table->text('isi');                           // BARU
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
```

`foreignId('user_id')->constrained()` = kolom `user_id` yang nunjuk ke tabel `users` (foreign key).

### 2c. Seeder — bikin user contoh

**File:** `notes-api/database/seeders/DatabaseSeeder.php`

**Isi lengkap** (ganti seluruh isinya):

```php
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // User contoh buat login. Password default dari factory Laravel = "password".
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@test.com',
        ]);
    }
}
```

### 2d. Jalanin migration + seeder

**Terminal `notes-api`:**

```
php artisan migrate --seed
```

### Cek langkah 2

- [ ] Di HeidiSQL, database `notes_api` sekarang punya tabel `users`, `notes`, `personal_access_tokens`, dst. (klik refresh).
- [ ] Tabel `users` isinya 1 baris: `test@test.com`.
- [ ] Gak ada error merah di terminal.

**Kalau error `SQLSTATE[HY000] [1049] Unknown database`:** database `notes_api` belum dibikin di HeidiSQL (langkah 1b). **Kalau `Connection refused`:** MySQL Laragon belum jalan (Start All).

---

## Langkah 3 — Route, Controller, Resource

### 3a. Route API

**File:** `notes-api/routes/api.php`

**Isi lengkap** (ganti seluruh isinya):

```php
<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\NoteController;
use Illuminate\Support\Facades\Route;

// Siapa aja boleh manggil (belum login)
Route::post('/login', [AuthController::class, 'login']);

// Wajib bawa token valid, kalau enggak -> 401
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('notes', NoteController::class);
    Route::post('/logout', [AuthController::class, 'logout']);
});
```

**Baca per bagian:**
- `/login` di luar grup → siapa aja boleh manggil (ya iyalah, belum login).
- `middleware('auth:sanctum')` → "satpam": request tanpa token valid ditolak dengan **401**.
- `apiResource('notes', ...)` → otomatis bikin 5 route: `GET /notes`, `POST /notes`, `GET /notes/{id}`, `PUT /notes/{id}`, `DELETE /notes/{id}`.
- Semua route di `api.php` otomatis punya awalan `/api` → alamat lengkapnya `http://localhost:8000/api/login`, dst.

### 3b. `AuthController`

**Terminal `notes-api`:**

```
php artisan make:controller AuthController
```

**File:** `notes-api/app/Http/Controllers/AuthController.php`

**Isi lengkap** (ganti seluruh isinya):

```php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Cari user berdasarkan email
        $user = User::where('email', $request->email)->first();

        // Bandingin password yang diketik dengan hash di database
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Login gagal'], 401);
        }

        // Bikin token. Teks token cuma muncul SEKALI ini; di database cuma hash-nya.
        $token = $user->createToken('react-app')->plainTextToken;
        return response()->json(['token' => $token, 'user' => $user]);
    }

    public function logout(Request $request)
    {
        // Hapus token yang lagi dipakai, jadi token itu gak berlaku lagi
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logout berhasil']);
    }
}
```

### 3c. `NoteResource`

Resource = "filter" supaya cuma field yang kamu mau yang keluar ke React (misal `user_id` gak ikut bocor).

**Terminal `notes-api`:**

```
php artisan make:resource NoteResource
```

**File:** `notes-api/app/Http/Resources/NoteResource.php`

**Isi lengkap** (ganti seluruh isinya):

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NoteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'judul' => $this->judul,
            'isi' => $this->isi,
            'created_at' => $this->created_at,
        ];
    }
}
```

### 3d. `NoteController`

**Terminal `notes-api`:**

```
php artisan make:controller NoteController --api
```

**File:** `notes-api/app/Http/Controllers/NoteController.php`

**Isi lengkap** (ganti seluruh isinya). `apiResource` butuh 5 method standar: `index`, `store`, `show`, `update`, `destroy`.

```php
<?php

namespace App\Http\Controllers;

use App\Http\Resources\NoteResource;
use App\Models\Note;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    // Pastikan catatan ini milik user yang login. Kalau bukan -> 403.
    private function pastikanMilikSaya(Request $request, Note $note): void
    {
        abort_if($note->user_id !== $request->user()->id, 403);
    }

    // GET /api/notes -- cuma catatan milik user yang login
    public function index(Request $request)
    {
        return NoteResource::collection($request->user()->notes);
    }

    // POST /api/notes
    public function store(Request $request)
    {
        $data = $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
        ]);
        $note = $request->user()->notes()->create($data); // user_id keisi otomatis
        return (new NoteResource($note))->response()->setStatusCode(201);
    }

    // GET /api/notes/{id}
    public function show(Request $request, Note $note)
    {
        $this->pastikanMilikSaya($request, $note);
        return new NoteResource($note);
    }

    // PUT /api/notes/{id}
    public function update(Request $request, Note $note)
    {
        $this->pastikanMilikSaya($request, $note);
        $data = $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
        ]);
        $note->update($data);
        return new NoteResource($note);
    }

    // DELETE /api/notes/{id}
    public function destroy(Request $request, Note $note)
    {
        $this->pastikanMilikSaya($request, $note);
        $note->delete();
        return response()->noContent(); // 204, tanpa isi
    }
}
```

**Baca per bagian:**
- `$request->user()` — user yang lagi login (Sanctum ngisi ini dari token).
- `$request->user()->notes` — cuma catatan **milik user itu**. User A gak bisa lihat catatan user B.
- `$request->validate([...])` — validasi gagal → Laravel otomatis balikin 422 + daftar error.
- `notes()->create($data)` — bikin catatan dengan `user_id` terisi otomatis.
- `abort_if(... , 403)` — coba akses/ubah/hapus catatan orang lain → 403 Forbidden.

**PENTING buat sisi React nanti:** `NoteResource::collection(...)` dan `new NoteResource(...)` membungkus hasilnya dalam kunci `data`:

```json
{ "data": [ { "id": 1, "judul": "...", "isi": "..." } ] }
```

Jadi di React kamu ambil `json.data`, bukan `json` langsung.

### 3e. Jalanin server Laravel

**Terminal `notes-api`:**

```
php artisan serve
```

Biarin terminal ini jalan. Alamat: `http://localhost:8000`.

### Cek bagian A

- [ ] `php artisan route:list` (di terminal lain, folder `notes-api`) nampilin `api/login`, `api/notes` (5 baris), `api/logout`.
- [ ] `php artisan serve` jalan tanpa error.

---

# Bagian B — Testing API (Sebelum Nyambungin ke React)

## Langkah 4 — Testing API

Jangan tunggu React jadi buat mulai tes API. Tes dulu API-nya sendiri pakai 2 cara ini — kalau di sini aja udah bener, nanti masalah pas nyambung ke React jadi kepencil ke urusan React doang.

### 4a. Thunder Client — buat coba-coba manual pas develop

1. Install extension **Thunder Client** di VS Code (cari di tab Extensions).
2. Pastikan `php artisan serve` jalan (`http://localhost:8000`).
3. Di Thunder Client, bikin request baru. **Di setiap request, tambah header `Accept: application/json`** (kalau gak, Laravel balikin halaman HTML/redirect alih-alih JSON error, terutama saat token gak valid).
   - `POST http://localhost:8000/api/login` — body JSON `{ "email": "test@test.com", "password": "password" }`. **Copy `token`** dari response.
   - `GET http://localhost:8000/api/notes` — tab **Auth** → Bearer Token → paste token dari langkah sebelumnya. Harusnya `{"data": []}` (kosong).
   - `POST http://localhost:8000/api/notes` — body JSON `{ "judul": "Catatan pertama", "isi": "Isi catatan" }`, Bearer Token sama kayak di atas. Harusnya 201.
   - `PUT` dan `DELETE` ke `http://localhost:8000/api/notes/1` — pola sama (Bearer Token + body kalau perlu).
4. Simpen semua request ini jadi satu **Collection** ("Notes API") — next time kamu tinggal buka collection-nya, gak perlu ngetik ulang.

**Tes juga jalur gagalnya:**
- `GET /api/notes` **tanpa** token → harus **401**.
- Login dengan password salah → harus **401** + `"Login gagal"`.
- `POST /api/notes` dengan body kosong → harus **422** + daftar error validasi.

Kalau salah satu gagal (misal status 500 atau 401 yang gak seharusnya), baca pesan error di response body-nya — itu biasanya udah nunjuk lokasi masalahnya (migration belum jalan, route salah, dst). Log lengkap ada di `notes-api/storage/logs/laravel.log`.

### 4b. Pest — automated test, jalan otomatis

Laravel baru udah include Pest secara default.

**Terminal `notes-api`:**

```
php artisan make:test NoteApiTest
```

**File:** `notes-api/tests/Feature/NoteApiTest.php`

**Isi lengkap** (ganti seluruh isinya):

```php
<?php

use App\Models\User;

it('bisa login dan dapet token', function () {
    $user = User::factory()->create(['password' => bcrypt('password')]);

    $response = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertStatus(200)->assertJsonStructure(['token', 'user']);
});

it('nolak akses notes tanpa token', function () {
    $this->getJson('/api/notes')->assertStatus(401);
});

it('bisa CRUD notes kalau udah login', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/notes', ['judul' => 'Test', 'isi' => 'Isi test']);

    $response->assertStatus(201);
    $this->assertDatabaseHas('notes', ['judul' => 'Test']);
});
```

Jalanin:

```
php artisan test
```

Tiap kamu ubah kode controller/route, cukup jalanin `php artisan test` lagi — gak perlu klik-klik manual ulang buat mastiin gak ada yang rusak.

**Kapan pakai yang mana:** Thunder Client pas lagi develop/debug interaktif (lihat response asli, coba-coba cepat). Pest pas mau mastiin semua endpoint masih jalan bener setelah ada perubahan kode (regression check) — ini juga yang tutor pakai buat ngecek kerjaan kamu tanpa perlu klik manual satu-satu.

### Cek bagian B

- [ ] Login lewat Thunder Client → dapat `token`.
- [ ] `GET /api/notes` tanpa token → 401; dengan token → 200.
- [ ] `php artisan test` → semua hijau (passed).

**Jangan lanjut ke React sebelum semua cek di atas hijau.**

---

# Bagian C — CORS

## Langkah 5 — CORS

Laravel butuh tau alamat React (`http://localhost:5173`) boleh akses API-nya.

### 5a. Bikin file konfigurasi CORS

Laravel versi baru gak nyertain `config/cors.php` secara default. **Terminal `notes-api`:**

```
php artisan config:publish cors
```

### 5b. Isi `config/cors.php`

**File:** `notes-api/config/cors.php`

**Isi lengkap** (ganti seluruh isinya):

```php
<?php

return [

    // URL mana yang kena aturan CORS
    'paths' => ['api/*'],

    'allowed_methods' => ['*'],

    // BARU: alamat React yang boleh manggil API ini
    'allowed_origins' => ['http://localhost:5173'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // Untuk login pakai Bearer token seperti ini sebenernya gak wajib, tapi gak bikin masalah.
    'supports_credentials' => true,

];
```

Setelah itu, **restart** `php artisan serve` (`Ctrl+C`, lalu jalanin lagi).

**Kenapa ini baru muncul sekarang?** Selama ini React & fake API (json-server) kamu jalanin manual satu-satu tanpa masalah karena json-server default-nya udah ngizinin semua origin. Laravel, secara default, MENOLAK request dari origin/alamat lain demi keamanan (mencegah situs asing diam-diam manggil API-mu). CORS (`Cross-Origin Resource Sharing`) adalah aturan browser yang mengecek header ini sebelum ngizinin response dibaca oleh JS di origin lain.

**Cara ngenalin error CORS:** di Console browser muncul `has been blocked by CORS policy: No 'Access-Control-Allow-Origin' header`. Thunder Client gak kena error ini (dia bukan browser) — jadi API bisa "jalan" di Thunder Client tapi gagal di React. Itu ciri khas masalah CORS.

---

# Bagian D — React

Semua file di bagian ini ada di project **`learn-react/`** (bukan `notes-api`).

## Langkah 6 — `AuthContext` Versi Token

Beda utama dari sesi 8:

| | Sesi 8 | Sesi 10 |
|---|---|---|
| Yang disimpan | `user` `{ nama }` | `token` (string) |
| `login(...)` | `login(nama)` — asal ketik | `login(email, password)` — `async`, nanya server |
| Disimpan di | state saja (hilang saat refresh) | state **+ `localStorage`** (tetap ada saat refresh) |

**File:** `learn-react/src/context/AuthContext.jsx`

**Isi lengkap** (ganti seluruh isinya):

```jsx
import { createContext, useContext, useState } from "react";

const AuthContext = createContext(null);
const API = "http://localhost:8000/api"; // sesuaikan port Laravel-mu

export function AuthProvider({ children }) {
  // BARU: nilai awal dibaca dari localStorage -- makanya refresh gak bikin logout.
  const [token, setToken] = useState(localStorage.getItem("token"));

  // BARU: login sekarang nanya server, dan melempar error kalau gagal
  async function login(email, password) {
    const res = await fetch(`${API}/login`, {
      method: "POST",
      headers: { "Content-Type": "application/json", Accept: "application/json" },
      body: JSON.stringify({ email, password }),
    });
    if (!res.ok) throw new Error("Login gagal");
    const data = await res.json();
    setToken(data.token);                     // simpan di state (React langsung update)
    localStorage.setItem("token", data.token); // simpan di browser (awet saat refresh)
  }

  function logout() {
    setToken(null);
    localStorage.removeItem("token");
  }

  return (
    <AuthContext.Provider value={{ token, login, logout }}>
      {children}
    </AuthContext.Provider>
  );
}

export function useAuth() {
  return useContext(AuthContext);
}
```

**Baca per bagian:**
- `useState(localStorage.getItem("token"))` — nilai awal token diambil dari browser. Kalau gak ada, hasilnya `null`.
- `if (!res.ok) throw new Error(...)` — `login` **melempar error** kalau gagal; halaman Login yang nangkep dan nampilin pesannya (langkah 7).
- `Accept: "application/json"` — minta Laravel selalu jawab JSON.
- `setToken` + `localStorage.setItem` — dua-duanya: state buat React langsung update, `localStorage` biar awet.
- `logout` — hapus dari dua tempat itu juga.

`main.jsx` gak berubah dari sesi 8 (`<BrowserRouter><AuthProvider><App /></AuthProvider></BrowserRouter>`).

---

## Langkah 7 — Halaman Login (Email + Password)

**File:** `learn-react/src/pages/Login.jsx`

**Isi lengkap** (ganti seluruh isinya):

```jsx
import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { useAuth } from "../context/AuthContext.jsx";

export default function Login() {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState("");          // BARU: pesan error login
  const [submitting, setSubmitting] = useState(false); // BARU: lagi nunggu server?
  const { login } = useAuth();
  const navigate = useNavigate();

  async function handleSubmit(e) {
    e.preventDefault();
    setError("");
    setSubmitting(true);
    try {
      await login(email, password); // nunggu server
      navigate("/notes");           // cuma kepanggil kalau login berhasil
    } catch (err) {
      setError(err.message);        // "Login gagal"
    } finally {
      setSubmitting(false);         // jalan di sukses maupun gagal
    }
  }

  return (
    <div className="mx-auto max-w-sm p-6">
      <h1 className="mb-4 text-2xl font-bold">Login</h1>
      <form onSubmit={handleSubmit} className="flex flex-col gap-3">
        <input
          type="email"
          placeholder="Email"
          value={email}
          onChange={(e) => setEmail(e.target.value)}
          className="rounded border px-3 py-2"
        />
        <input
          type="password"
          placeholder="Password"
          value={password}
          onChange={(e) => setPassword(e.target.value)}
          className="rounded border px-3 py-2"
        />
        {error && <p className="text-sm text-red-500">{error}</p>}
        <button
          type="submit"
          disabled={submitting}
          className="rounded bg-blue-600 px-3 py-2 text-white disabled:opacity-50"
        >
          {submitting ? "Memproses..." : "Login"}
        </button>
      </form>
    </div>
  );
}
```

**Baca per bagian:**
- `await login(email, password)` — nunggu server. Kalau `login` melempar error (401), lompat ke `catch`, nampilin `err.message`.
- `navigate("/notes")` — pindah ke halaman notes, cuma kalau login berhasil.
- `submitting` — nonaktifin tombol selama nunggu, biar gak diklik dua kali (pola loading dari sesi 6).
- `finally` — jalan di kedua kasus (berhasil/gagal).

---

## Langkah 8 — `App.jsx` dan `Navbar.jsx`

### 8a. `App.jsx`

`ProtectedRoute` sekarang ngecek **token**, bukan `user`. Tetap ditulis **di luar** `App` (aturan sesi 8). Cuma satu baris inti yang beda dari sesi 8: `!user` → `!token`.

**File:** `learn-react/src/App.jsx`

**Isi lengkap** (ganti seluruh isinya):

```jsx
import { Routes, Route, Navigate } from "react-router-dom";
import { useAuth } from "./context/AuthContext.jsx";
import Layout from "./Layout.jsx";
import Login from "./pages/Login.jsx";
import NotesPage from "./pages/NotesPage.jsx"; // BARU: dibikin di langkah 9

// Ditulis DI LUAR App
function ProtectedRoute({ children }) {
  const { token } = useAuth();                  // BARU: token, bukan user
  if (!token) return <Navigate to="/login" />;  // belum login -> tendang
  return children;
}

export default function App() {
  return (
    <Routes>
      <Route path="/" element={<Layout />}>
        <Route index element={<Navigate to="/notes" />} />
        <Route path="login" element={<Login />} />
        <Route
          path="notes"
          element={
            <ProtectedRoute>
              <NotesPage />
            </ProtectedRoute>
          }
        />
      </Route>
    </Routes>
  );
}
```

**Kalau sebelumnya `App.jsx`-mu punya route project dari sesi 9** (`ProjectList`, `ProjectDetail`), route itu ilang kalau file diganti seluruhnya. Boleh ditambahin lagi ke dalam `<Route path="/" ...>`, atau biarin ilang — sesi 10 fokus ke notes.

### 8b. `Navbar.jsx`

Token gak punya nama, jadi cukup tombol Logout / link Login.

**File:** `learn-react/src/components/Navbar.jsx`

**Isi lengkap** (ganti seluruh isinya):

```jsx
import { Link } from "react-router-dom";
import { useAuth } from "../context/AuthContext.jsx";

export default function Navbar() {
  const { token, logout } = useAuth(); // BARU: token, bukan user

  return (
    <nav className="flex items-center justify-between bg-slate-900 px-6 py-4 text-white">
      <span className="text-lg font-bold">Portofolio Vanya</span>
      <ul className="flex items-center gap-4 text-sm">
        <li>
          <Link to="/notes">Catatan</Link>
        </li>
        <li>
          {token ? (
            <button onClick={logout} className="text-red-500 hover:text-red-400">
              Logout
            </button>
          ) : (
            <Link to="/login">Login</Link>
          )}
        </li>
      </ul>
    </nav>
  );
}
```

### Cek langkah 6–8

- [ ] Gak ada garis merah di VS Code (kecuali `NotesPage` yang belum ada — normal, dibikin di langkah 9).
- [ ] `npm run dev` jalan tanpa error sintaks di terminal.

---

## Langkah 9 — Halaman Notes (CRUD dengan Header Authorization)

Bagian "beda" dari sesi 9 cuma **base URL** dan **header `Authorization`**. Kode lengkapnya di bawah, tiap bagian ditandain huruf `[A]`–`[D]`:

| Huruf | Bagian | Ke server |
|---|---|---|
| **[A]** | Helper request (header + penanganan 401) | dipakai semua fitur |
| **[B]** | Ambil daftar notes | `GET /api/notes` |
| **[C]** | Tambah / edit (satu form, dua mode) | `POST /api/notes` atau `PUT /api/notes/:id` |
| **[D]** | Hapus | `DELETE /api/notes/:id` |

**File baru:** `learn-react/src/pages/NotesPage.jsx`

```jsx
import { useState, useEffect } from "react";
import { useAuth } from "../context/AuthContext.jsx";

const API = "http://localhost:8000/api";

// [A] HELPER: bungkus fetch + 3 header wajib + penanganan 401.
// Fungsi biasa di LUAR component.
async function apiRequest(token, logout, path, options = {}) {
  const res = await fetch(`${API}${path}`, {
    ...options,
    headers: {
      "Content-Type": "application/json",
      Accept: "application/json",
      Authorization: `Bearer ${token}`, // "tiket masuk" yang diminta auth:sanctum
    },
  });
  if (res.status === 401) {
    logout(); // token invalid/expired -> paksa logout, ProtectedRoute nendang ke /login
    throw new Error("Sesi habis, silakan login lagi");
  }
  if (!res.ok) throw new Error("Permintaan gagal");
  return res.status === 204 ? null : res.json(); // 204 = sukses tanpa isi (DELETE)
}

export default function NotesPage() {
  const { token, logout } = useAuth();

  // ───────────── STATE (semua useState di ATAS if (loading)) ─────────────
  const [notes, setNotes] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [form, setForm] = useState({ judul: "", isi: "" }); // [C] isi form tambah/edit
  const [editingId, setEditingId] = useState(null);          // [C] null = mode tambah, ada isi = mode edit

  // ───────────── [B] AMBIL DAFTAR NOTES (GET) ─────────────
  useEffect(() => {
    async function load() {
      try {
        setLoading(true);
        const json = await apiRequest(token, logout, "/notes");
        setNotes(json.data); // Laravel Resource ngebungkus dalam "data"
      } catch (err) {
        setError(err.message);
      } finally {
        setLoading(false);
      }
    }
    load();
  }, [token, logout]);

  // ───────────── [C] TAMBAH / EDIT ─────────────
  function handleChange(e) {
    const { name, value } = e.target;
    setForm({ ...form, [name]: value });
  }

  async function handleSubmit(e) {
    e.preventDefault();
    if (!form.judul.trim() || !form.isi.trim()) return;
    setError("");
    try {
      if (editingId) {
        // mode edit -> PUT
        const json = await apiRequest(token, logout, `/notes/${editingId}`, {
          method: "PUT",
          body: JSON.stringify(form),
        });
        setNotes(notes.map((n) => (n.id === editingId ? json.data : n)));
      } else {
        // mode tambah -> POST
        const json = await apiRequest(token, logout, "/notes", {
          method: "POST",
          body: JSON.stringify(form),
        });
        setNotes([...notes, json.data]);
      }
      setForm({ judul: "", isi: "" });
      setEditingId(null);
    } catch (err) {
      setError(err.message);
    }
  }

  function mulaiEdit(note) {
    setEditingId(note.id);
    setForm({ judul: note.judul, isi: note.isi });
  }

  function batalEdit() {
    setEditingId(null);
    setForm({ judul: "", isi: "" });
  }

  // ───────────── [D] HAPUS ─────────────
  async function hapusNote(id) {
    if (!confirm("Yakin hapus catatan ini?")) return;
    setError("");
    try {
      await apiRequest(token, logout, `/notes/${id}`, { method: "DELETE" });
      setNotes(notes.filter((n) => n.id !== id));
    } catch (err) {
      setError(err.message);
    }
  }

  // ───────────── EARLY RETURN ─────────────
  if (loading) return <p className="p-6">Loading...</p>;

  // ───────────── TAMPILAN ─────────────
  return (
    <div className="mx-auto max-w-lg p-6">
      <h1 className="mb-4 text-2xl font-bold">Catatan Saya</h1>
      {error && <p className="mb-2 text-sm text-red-500">{error}</p>}

      {/* [C] form tambah/edit: label tombol berubah sesuai mode */}
      <form onSubmit={handleSubmit} className="mb-4 space-y-2 rounded border p-3">
        <input
          name="judul"
          value={form.judul}
          onChange={handleChange}
          placeholder="Judul"
          className="w-full rounded border px-2 py-1"
        />
        <textarea
          name="isi"
          value={form.isi}
          onChange={handleChange}
          placeholder="Isi catatan"
          className="w-full rounded border px-2 py-1"
        />
        <div className="flex gap-2">
          <button type="submit" className="rounded bg-blue-600 px-3 py-1 text-white">
            {editingId ? "Simpan Perubahan" : "Tambah"}
          </button>
          {editingId && (
            <button type="button" onClick={batalEdit} className="text-sm text-slate-500">
              Batal
            </button>
          )}
        </div>
      </form>

      {/* [B] list notes */}
      <ul className="space-y-2">
        {notes.map((n) => (
          <li key={n.id} className="rounded border p-2">
            <p className="font-semibold">{n.judul}</p>
            <p className="text-sm text-slate-600">{n.isi}</p>
            <div className="mt-1 flex gap-3 text-sm">
              {/* [C] tombol Edit */}
              <button onClick={() => mulaiEdit(n)} className="text-blue-600">Edit</button>
              {/* [D] tombol Hapus */}
              <button onClick={() => hapusNote(n.id)} className="text-red-500">Hapus</button>
            </div>
          </li>
        ))}
      </ul>
    </div>
  );
}
```

**Aturan urutan di file ini** (sama kayak sesi 9): semua `useState` dan `useEffect` **di atas** `if (loading) return ...`.

### Bedah per bagian

**[A] `apiRequest`**
- `Authorization: \`Bearer ${token}\`` — inilah "tiket masuk" yang diminta `auth:sanctum` di Laravel. **`Bearer` lalu satu spasi lalu token.**
- Kalau status 401 → `logout()` → `token` jadi `null` → `ProtectedRoute` otomatis nendang ke `/login` (gak perlu `navigate` manual).
- Status `204` (hasil DELETE) gak punya isi, jadi `res.json()` bakal error kalau dipanggil — makanya dicek dulu.
- Fungsi ini di **luar** component, jadi gak dibikin ulang tiap render.

**[B] Ambil daftar**
- `useEffect` dengan `[token, logout]` — jalan waktu halaman pertama muncul.
- `json.data` (bukan `json`) — karena `NoteResource::collection` di Laravel ngebungkus dalam `data`.

**[C] Tambah / edit dalam satu form**
- `editingId` menentukan mode: `null` = tambah, ada isi = edit catatan itu.
- Pola `handleChange` + `name` sama persis kayak form sesi 5.
- Tombol submit ganti label: "Tambah" / "Simpan Perubahan". Tombol "Batal" cuma muncul di mode edit.
- Mode edit → `PUT /notes/:id`. Mode tambah → `POST /notes`. Keduanya balikin `{ "data": {...} }`.

**[D] Hapus**
- `confirm(...)` dulu, lalu `DELETE`. Status 204 → `apiRequest` balikin `null`.

### Cek langkah 9 (tes pertama end-to-end)

- [ ] Buka `http://localhost:5173/login`, isi `test@test.com` / `password` → pindah ke `/notes`, tampil "Catatan Saya" (kosong).
- [ ] DevTools → **Network** → klik request `notes` → tab **Headers** → di "Request Headers" ada `Authorization: Bearer <token panjang>`.
- [ ] Logout, buka `/notes` langsung → dilempar ke `/login`.

**Kalau muncul error CORS di Console** → balik ke Langkah 5. **Kalau `json.data` undefined** → cek bentuk response di Network tab (Preview).

---

## Langkah 10 — Tes Alur Lengkap

| # | Kamu lakuin | Harus terjadi |
|---|---|---|
| 1 | Buka `/notes` tanpa login | Dilempar ke `/login` |
| 2 | Login dengan password salah | Muncul pesan merah "Login gagal", gak pindah halaman |
| 3 | Login dengan `test@test.com` / `password` | Masuk `/notes` |
| 4 | Tambah catatan | Muncul di list; cek juga di HeidiSQL tabel `notes` |
| 5 | Edit catatan → Simpan Perubahan | Isi berubah; form balik ke mode "Tambah" |
| 6 | Hapus catatan (OK) | Hilang; baris di tabel juga hilang |
| 7 | Refresh halaman (F5) | **Tetap login** (token ada di `localStorage`) |
| 8 | Logout, refresh | Balik ke `/login`; token ilang dari `localStorage` |
| 9 | DevTools → Network → klik request `/api/notes` | Header `Authorization: Bearer ...` ada |

## Kalau Error

| Gejala | Biasanya penyebabnya |
|---|---|
| Console: `blocked by CORS policy` | `config/cors.php` belum bener / belum restart `php artisan serve` (Langkah 5) |
| Login selalu "Login gagal" | Email/password salah, atau seeder belum jalan (`php artisan migrate --seed`) |
| `SQLSTATE ... Connection refused` di respons Laravel | MySQL Laragon belum dinyalain, atau `.env` salah |
| `401` padahal udah login | Header `Authorization` gak kekirim / token salah / lupa spasi setelah `Bearer` |
| Error 500 `Route [login] not defined` | Request gak bawa `Accept: application/json` |
| `Call to undefined method ... createToken()` | Lupa `use HasApiTokens` di `User.php` (Langkah 2a) |
| `Cannot read properties of undefined (reading 'map')` | Pakai `json` alih-alih `json.data` |
| Refresh → langsung logout | `useState` di `AuthProvider` gak baca `localStorage.getItem("token")` |
| `422` saat tambah/edit | Judul atau isi kosong (validasi Laravel) |
| Bisa lihat catatan user lain | `index` pakai `Note::all()` alih-alih `$request->user()->notes` |
| `Class "App\Models\Note" not found` | `Note.php` belum dibikin / salah namespace |

---

## Bandingkan: Apa yang Berubah dari Sesi 9?

| | json-server (Sesi 9) | Laravel (Sesi 10) |
|---|---|---|
| Base URL | `http://localhost:3001` | `http://localhost:8000/api` |
| Header tambahan | tidak ada | `Authorization: Bearer <token>` + `Accept: application/json` |
| Bentuk response list | array langsung | dibungkus `{ "data": [...] }` |
| Login | tidak ada | POST `/login`, simpan token |
| 401 handling | tidak ada | logout paksa → redirect ke login |
| **Struktur component React** | — | **HAMPIR TIDAK BERUBAH** |

**Ini poin paling penting dari sesi ini:** komponen React kamu (form, list, state management, `.map()`, dst) nyaris gak berubah sama sekali. Yang berubah cuma base URL dan header. Ini yang bikin nanti pas ganti ke backend Golang, prosesnya bakal berasa familiar, bukan mulai dari nol lagi.

## Sidebar Singkat: Supabase & Inertia

**Supabase** = PostgreSQL + auth + API instan, tanpa kamu nulis backend sama sekali. Cocok buat prototyping cepat, tapi karena tujuan kamu belajar backend beneran (Golang), Supabase bukan jalur utama di kurikulum ini — cukup tau ini exist sebagai opsi.

**Kenapa kita GAK pakai Inertia.js** (walau itu cara populer gabungin Laravel + React/Vue): Inertia bikin React "nempel" ke Laravel sedemikian rupa sehingga garis pemisah frontend/backend jadi kabur — padahal garis itu justru yang pengen kita LIAT jelas di sesi ini (biar paham gimana API kerja secara umum, gak cuma di Laravel doang).

## Latihan

Kalau kamu ngikutin Langkah 6-10, fitur Notes CRUD lengkap (list, tambah, edit, hapus) dengan login terpisah dan redirect otomatis udah jadi. Latihannya: **bikin ulang tanpa lihat panduan** halaman `NotesPage` dari kerangka kosong, pakai token yang tersimpan.

## Catatan buat Kamu

1. Coba logout, refresh halaman — pastikan token ke-hapus dan kamu ke-redirect ke login (bukan malah stuck di halaman notes tanpa data).
2. Buka DevTools Network tab, klik salah satu request ke `/api/notes` — cek header `Authorization`-nya beneran ada dan formatnya `Bearer <token panjang>`.
3. Tulis 3-4 kalimat: apa hal yang paling mengejutkan buat kamu soal seberapa sedikit kode React yang berubah di sesi ini?
4. Tambah 1 test Pest baru: mastiin `update` dan `delete` note cuma bisa dilakuin sama pemilik note-nya sendiri (bukan user lain). Jalanin `php artisan test`, pastikan lolos.

---

Kurikulum React selesai di sini. Selanjutnya kamu lanjut ke stage **Golang** (`6-GOLANG/go-journey/`) — di situ baru dibangun project serius (Task Management, database PostgreSQL beneran), lalu ujian closed-book fullstack, dan baru di paling akhir belajar deploy/hosting lengkap.
