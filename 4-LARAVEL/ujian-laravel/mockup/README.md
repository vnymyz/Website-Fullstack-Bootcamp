# Mockup Tampilan — Platform Resep Masakan

Ini **mockup statis** (HTML + CSS + dikit JS), bukan aplikasi Laravel beneran — tujuannya cuma buat kebayang tampilan akhir website yang bakal dibikin di ujian project. Styling pakai Tailwind CSS (lewat CDN), sama gayanya kayak yang udah dipelajari di `laravel-intro`.

## Cara Jalanin (pakai Live Server)

1. Buka folder `mockup/` ini di VS Code.
2. Pastiin extension **"Live Server"** udah ke-install (`Ctrl+Shift+X`, cari "Live Server", install kalau belum ada).
3. Klik kanan file **`index.html`** di sidebar VS Code.
4. Pilih **"Open with Live Server"**.
5. Browser otomatis kebuka (biasanya di `http://127.0.0.1:5500/mockup/index.html`), nampilin landing page.

> Gak perlu `php artisan serve` atau setup Laravel apapun — ini murni file HTML statis, jalan langsung di browser.

## Daftar Halaman

| File | Halaman |
|---|---|
| `index.html` | Landing page publik (belum login) |
| `login.html` | Form Login |
| `register.html` | Form Register |
| `dashboard.html` | Dashboard abis login (sidebar + statistik) |
| `recipes.html` | Daftar resep (search, pagination, tombol Edit/Hapus) |
| `recipe-detail.html` | Halaman detail 1 resep |
| `recipe-create.html` | Form tambah/edit resep |
| `admin-users.html` | Halaman User Management (khusus admin) |

Semua halaman saling ke-link (klik navbar/sidebar/tombol buat pindah halaman) — coba klik-klik sendiri buat ngerasain alurnya sebelum mulai coding.

## Penting

- Ini **cuma tampilan**, semua data di dalamnya (nama resep, user, statistik) itu **hardcode**/dummy — gak nyambung ke database beneran.
- Tombol-tombol (Login, Simpan, Hapus, dll) sebagian cuma pindah halaman doang (simulasi alur), gak beneran ngapa-ngapain ke data.
- Struktur & styling di sini **boleh diubah bebas** pas beneran ngoding — ini cuma referensi/starting point biar gak bingung mulai dari mana, bukan patokan wajib sama persis.
- Detail cara bikin tiap fitur beneran (CRUD, auth, search, dll) ada di `ujian-laravel.md`.
