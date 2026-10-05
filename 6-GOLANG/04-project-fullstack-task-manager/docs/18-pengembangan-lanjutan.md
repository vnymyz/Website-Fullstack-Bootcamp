# 18 - Pengembangan Lanjutan dan Tugas Akhir

## Tugas Akhir untuk Siswa
Pilih **minimal dua** dari daftar berikut (tingkat kesulitan dalam kurung):

1. **Kategori task** (sedang): tabel `categories`, relasi ke `tasks`, filter per kategori.
2. **Pencarian judul** (mudah): `?q=` di backend dan input di frontend.
3. **Prioritas task** (mudah): kolom `priority`, badge warna, urutkan berdasarkan prioritas.
4. **Tenggat terlewat** (mudah): tandai merah, tambahkan filter "terlambat".
5. **Edit profil & ganti password** (sedang): endpoint `PUT /api/auth/me` dan `PUT /api/auth/password`.
6. **Dark mode** (mudah): simpan pilihan di `localStorage`.
7. **Unit test backend** (sedang): tes `utils` (JWT, password) dan handler dengan `httptest`.
8. **Role admin** (sulit): kolom `role`, middleware `RequireRole("admin")`, halaman daftar semua user.
9. **Refresh token** (sulit): access token singkat + refresh token.

## Topik Bonus

### Docker Compose
Menjalankan MySQL, backend, dan frontend dengan satu perintah.
```yaml
# docker-compose.yml (contoh gambaran)
services:
  db:
    image: mysql:8
    environment:
      MYSQL_ROOT_PASSWORD: rahasia
      MYSQL_DATABASE: task_manager
    ports: ["3306:3306"]
    volumes:
      - ./database/schema.sql:/docker-entrypoint-initdb.d/schema.sql
  api:
    build: ./backend
    env_file: ./backend/.env
    depends_on: [db]
    ports: ["8080:8080"]
```
Catatan: di dalam Docker, `DB_HOST` harus `db` (nama service), bukan `127.0.0.1`.

### Migrasi database
Alih-alih menjalankan `schema.sql` manual, gunakan `golang-migrate` agar perubahan skema berversi (`001_create_users.up.sql`, `001_create_users.down.sql`, dst).

### Dokumentasi API
`swaggo/swag` membuat dokumentasi Swagger dari komentar di handler.

### Keamanan tingkat lanjut
- Simpan token di cookie `httpOnly; Secure; SameSite` agar tidak bisa dibaca JavaScript (mengurangi risiko XSS).
- Rate limiting pada endpoint login (cegah brute force).
- Validasi dan batasi ukuran body request.
- Gunakan HTTPS di produksi.

### Logging terstruktur
`log/slog` (bawaan Go) atau `zerolog` agar log mudah dicari.

### CI sederhana (GitHub Actions)
Jalankan `go vet`, `go test`, dan `npm run build` setiap push.

### Deployment
- Backend: Railway, Render, Fly.io, atau VPS (binary + systemd + Nginx).
- Frontend: Netlify, Vercel, Cloudflare Pages.
- Database: PlanetScale-kompatibel, Railway MySQL, atau MySQL di VPS.

## Rubrik Penilaian Tugas Akhir

| Kriteria | Bobot |
|---|---|
| Fitur berjalan sesuai spesifikasi | 40% |
| Kerapian struktur & penamaan | 20% |
| Penanganan error & validasi | 15% |
| Keamanan dasar (hash, query berparameter, `.env`) | 15% |
| Demo & penjelasan | 10% |

## Penutup
Selamat! Siswa kini memahami alur fullstack lengkap: UI -> API -> database. Langkah belajar berikutnya: testing yang lebih dalam, concurrency di server, caching (Redis), message queue, dan arsitektur layanan.
