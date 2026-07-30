## Kunci Jawaban (buat pengajar)

> Bagian ini buat referensi ngoreksi, bukan buat dikasih ke murid.

1. MVC: Model (`app/Models/`, data & logic DB), View (`resources/views/`, tampilan), Controller (`app/Http/Controllers/`, jembatan). Alur: Route → Controller → Model → View.
2. Statis = URL tetap (`/posts/create`). Dinamis = ada parameter (`/posts/{id}`). Laravel cocokin route dari atas ke bawah — kalau dinamis ditaruh duluan, `create` bisa "ketangkep" jadi isi `{id}`, bikin salah route/404.
3. `@extends('layouts.x')` = pakai layout tertentu. `@yield('content')` = "lubang" di layout. `@section('content') ... @endsection` = isi buat ngisi lubang itu.
4. Controller: nerima request, proses logic, siapin data buat View. Validasi/logic di View bikin campur aduk presentasi & logic, susah di-maintain & di-test.
5. `$fillable` = whitelist kolom yang boleh di mass-assignment (`create()`/`update()` dengan array). Kolom `role` kalau di-fillable bisa disusupin (`role=admin`) lewat form publik kayak register — celah keamanan (privilege escalation).
6. Migration = version control buat struktur database. `--table=posts` bikin migration buat ubah tabel yang **udah ada**. `make:model -m` bikin Model **+** migration buat tabel **baru** sekaligus.
7. `belongsTo` di Model yang **punya** foreign key (`Recipe` punya `user_id` → `Recipe belongsTo User`). `hasMany` di Model satunya (`User hasMany Recipe`).
8. Authentication = siapa kamu (login/register). Authorization = kamu boleh ngapain (role, kepemilikan data). Contoh: Breeze login = Authentication; Policy/middleware admin = Authorization.
9. Middleware = filter/pengecekan sebelum request nyampe Controller. Bikin: `php artisan make:middleware Nama`, daftarin alias di `bootstrap/app.php`, pakai `Route::middleware('nama')`.
10. Kolom `role` di tabel `users` + middleware custom yang ngecek `auth()->user()->role === 'admin'`, `abort(403)` kalau bukan — dipasang di route group, bukan cuma disembunyiin di UI.
11. Route model binding = Laravel otomatis nyariin Model berdasarkan parameter URL. `{post}` = cari by `id` (default). `{post:slug}` = cari by kolom `slug`.
12. Tanpa `enctype`, browser gak ngirim isi file (cuma nama file doang / kosong) — validasi `image` bakal gagal terus walau file-nya valid.
13. `when($kondisi, function ($query) {...})` cuma nambah kondisi query kalau syaratnya kepenuhi. `GET` dipakai biar kata kunci masuk query string (`?search=...`), bisa di-bookmark/share, sesuai sifat "baca data" bukan "ubah data".
14. Manfaat lain: performa — gak narik semua data sekaligus dari DB. `withQueryString()` mastiin `?search=...` tetep kebawa pas klik halaman 2/3, biar hasil filter gak reset.
15. Form Request = class terpisah buat validasi + otorisasi. Keuntungan: gak ada duplikasi rules antar method, Controller lebih fokus logic bisnis, jalan otomatis sebelum method Controller dieksekusi.
16. Policy = class terpusat isi aturan "siapa boleh ngapain" per Model. `$this->authorize()` di Controller otomatis `abort(403)` kalau gagal. `$this->user()->can()` return boolean, dipakai manual (misal di Form Request `authorize()`). `@can`/`@endcan` di Blade buat nyembunyiin elemen UI.
17. Web routes return HTML/View, didesain diakses browser pakai session. API routes return JSON, didesain diakses dari luar (mobile app, frontend terpisah, tool testing) — gak butuh render HTML.
18. Login via `POST /api/login` (email+password) → dapet token dari `createToken()->plainTextToken` → tiap request berikutnya sertain header `Authorization: Bearer <token>` → route yang dibungkus `middleware('auth:sanctum')` otomatis validasi token itu.
19. `RefreshDatabase` = reset database ke kondisi bersih tiap method test (migrate ulang). SQLite in-memory dipilih karena cepet (di RAM) dan gak ngerusak/campur sama data development di MySQL.
20. Factory = template generate 1 data dummy. Seeder = script yang manggil Factory buat isi banyak data ke database. `$users` perlu di-generate duluan biar `recycle($users)` bisa "numpangin" data Post baru ke User yang udah ada, bukan bikin User baru terus tiap Post.
