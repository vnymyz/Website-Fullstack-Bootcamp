# Sesi 10 — Laravel Connect Demo

Tujuan: buktiin React bisa ngobrol sama backend yang udah kamu kenal (Laravel). Ini **bukan** project fitur baru — cuma demo kecil buat nunjukin cara nyambungnya.

App: Notes CRUD kecil + login, 1-2 tabel doang.

## Langkah 1 — Bikin Laravel API (project terpisah, di Laragon)

Bikin project Laravel baru (di `www` Laragon, terpisah dari `learn-react/`):

```
composer create-project laravel/laravel notes-api
cd notes-api
php artisan install:api
```

Migration `notes`:
```php
Schema::create('notes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained();
    $table->string('judul');
    $table->text('isi');
    $table->timestamps();
});
```

`routes/api.php`:
```php
use App\Http\Controllers\NoteController;
use App\Http\Controllers\AuthController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('notes', NoteController::class);
    Route::post('/logout', [AuthController::class, 'logout']);
});
```

`AuthController@login` (inti logikanya):
```php
public function login(Request $request)
{
    $user = User::where('email', $request->email)->first();
    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Login gagal'], 401);
    }
    $token = $user->createToken('react-app')->plainTextToken;
    return response()->json(['token' => $token, 'user' => $user]);
}
```

`NoteController` — pakai `apiResource`, jadi cukup 5 method standar (`index`, `store`, `show`, `update`, `destroy`), balikin JSON pakai API Resource:

```php
php artisan make:resource NoteResource
```

## Langkah 2 — Testing API (Sebelum Nyambungin ke React)

Jangan tunggu React jadi buat mulai tes API. Tes dulu API-nya sendiri pakai 2 cara ini — kalau di sini aja udah bener, nanti masalah pas nyambung ke React jadi kepencil ke urusan React doang.

### 2a. Thunder Client — buat coba-coba manual pas develop

1. Install extension **Thunder Client** di VS Code (cari di tab Extensions).
2. Buka Laravel: `php artisan serve` (biasanya jalan di `http://localhost:8000`).
3. Di Thunder Client, bikin request baru:
   - `POST http://localhost:8000/api/login` — body JSON `{ "email": "test@test.com", "password": "password" }`. Copy `token` dari response.
   - `GET http://localhost:8000/api/notes` — tab **Auth** → Bearer Token → paste token dari langkah sebelumnya.
   - `POST http://localhost:8000/api/notes` — body JSON `{ "judul": "Catatan pertama", "isi": "Isi catatan" }`, header Bearer Token sama kayak di atas.
   - `PUT` dan `DELETE` ke `http://localhost:8000/api/notes/1` — pola sama (Bearer Token + body kalau perlu).
4. Simpen semua request ini jadi satu **Collection** ("Notes API") — next time kamu tinggal buka collection-nya, gak perlu ngetik ulang.

Kalau salah satu gagal (misal status 500 atau 401 yang gak seharusnya), baca pesan error di response body-nya — itu biasanya udah nunjuk lokasi masalahnya (migration belum jalan, route salah, dst).

### 2b. Pest — automated test, jalan otomatis

Laravel baru udah include Pest secara default. Bikin file test:

```
php artisan make:test NoteApiTest
```

Isi `tests/Feature/NoteApiTest.php`:

```php
use App\Models\User;
use App\Models\Note;

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

## Langkah 3 — CORS

Laravel butuh tau alamat React (`http://localhost:5173`) boleh akses API-nya. Edit `config/cors.php`:

```php
'paths' => ['api/*'],
'allowed_origins' => ['http://localhost:5173'],
'supports_credentials' => true,
```

**Kenapa ini baru muncul sekarang?** Selama ini React & fake API (json-server) kamu jalanin manual satu-satu tanpa masalah karena json-server default-nya udah ngizinin semua origin. Laravel, secara default, MENOLAK request dari origin/alamat lain demi keamanan (mencegah situs asing diam-diam manggil API-mu). CORS (`Cross-Origin Resource Sharing`) adalah aturan browser yang mengecek header ini sebelum ngizinin response dibaca oleh JS di origin lain.

## Langkah 4 — React: Login & Simpan Token

Di `learn-react/`, bikin `src/context/AuthContext.jsx` (mirip sesi 8, tapi sekarang beneran manggil API):

```jsx
import { createContext, useContext, useState } from "react";

const AuthContext = createContext(null);
const API = "http://localhost:8000/api"; // sesuaikan port Laravel-mu

export function AuthProvider({ children }) {
  const [token, setToken] = useState(localStorage.getItem("token"));

  async function login(email, password) {
    const res = await fetch(`${API}/login`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ email, password }),
    });
    if (!res.ok) throw new Error("Login gagal");
    const data = await res.json();
    setToken(data.token);
    localStorage.setItem("token", data.token);
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

## Langkah 5 — Fetch dengan Header Authorization

```jsx
import { useAuth } from "../context/AuthContext.jsx";

function NotesPage() {
  const { token, logout } = useAuth();

  useEffect(() => {
    fetch("http://localhost:8000/api/notes", {
      headers: { Authorization: `Bearer ${token}` },
    })
      .then((res) => {
        if (res.status === 401) {
          logout(); // token invalid/expired -> paksa logout
          throw new Error("Sesi habis, silakan login lagi");
        }
        return res.json();
      })
      .then(setNotes)
      .catch((err) => setError(err.message));
  }, [token]);

  // ...
}
```

## Bandingkan: Apa yang Berubah dari Sesi 9?

| | json-server (Sesi 9) | Laravel (Sesi 10) |
|---|---|---|
| Base URL | `http://localhost:3001` | `http://localhost:8000/api` |
| Header tambahan | tidak ada | `Authorization: Bearer <token>` |
| Login | tidak ada | POST `/login`, simpan token |
| 401 handling | tidak ada | redirect ke login |
| **Struktur component React** | — | **HAMPIR TIDAK BERUBAH** |

**Ini poin paling penting dari sesi ini:** komponen React kamu (form, list, state management, `.map()`, dst) nyaris gak berubah sama sekali. Yang berubah cuma base URL dan header. Ini yang bikin nanti pas ganti ke backend Golang, prosesnya bakal berasa familiar, bukan mulai dari nol lagi.

## Sidebar Singkat: Supabase & Inertia

**Supabase** = PostgreSQL + auth + API instan, tanpa kamu nulis backend sama sekali. Cocok buat prototyping cepat, tapi karena tujuan kamu belajar backend beneran (Golang), Supabase bukan jalur utama di kurikulum ini — cukup tau ini exist sebagai opsi.

**Kenapa kita GAK pakai Inertia.js** (walau itu cara populer gabungin Laravel + React/Vue): Inertia bikin React "nempel" ke Laravel sedemikian rupa sehingga garis pemisah frontend/backend jadi kabur — padahal garis itu justru yang pengen kita LIAT jelas di sesi ini (biar paham gimana API kerja secara umum, gak cuma di Laravel doang).

## Latihan

Bikin fitur Notes CRUD lengkap: list, tambah, edit, hapus — semuanya lewat token yang tersimpan, dengan halaman login terpisah dan redirect otomatis kalau belum login.

## Catatan buat Kamu

1. Coba logout, refresh halaman — pastikan token ke-hapus dan kamu ke-redirect ke login (bukan malah stuck di halaman notes tanpa data).
2. Buka DevTools Network tab, klik salah satu request ke `/api/notes` — cek header `Authorization`-nya beneran ada dan formatnya `Bearer <token panjang>`.
3. Tulis 3-4 kalimat: apa hal yang paling mengejutkan buat kamu soal seberapa sedikit kode React yang berubah di sesi ini?
4. Tambah 1 test Pest baru: mastiin `update` dan `delete` note cuma bisa dilakuin sama pemilik note-nya sendiri (bukan user lain). Jalanin `php artisan test`, pastikan lolos.

---

Kurikulum React selesai di sini. Selanjutnya kamu lanjut ke stage **Golang** (`6-GOLANG/go-journey/`) — di situ baru dibangun project serius (Task Management, database PostgreSQL beneran), lalu ujian closed-book fullstack, dan baru di paling akhir belajar deploy/hosting lengkap.
