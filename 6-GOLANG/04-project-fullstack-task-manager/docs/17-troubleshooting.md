# 17 - Troubleshooting

Cara mencari masalah: **database -> backend -> frontend**. Cek log terminal backend dan tab **Network** di DevTools browser.

## Database

| Gejala | Penyebab | Solusi |
|---|---|---|
| `gagal ping database` | MySQL belum menyala | Start MySQL di XAMPP/Laragon |
| `Access denied for user 'root'@'localhost'` | Password salah | Isi `DB_PASS` yang benar |
| `Unknown database 'task_manager'` | Schema belum dijalankan | Jalankan `database/schema.sql` |
| `dial tcp 127.0.0.1:3306: connectex: No connection could be made` | Port MySQL lain (mis. 3307) | Ubah `DB_PORT` |
| XAMPP MySQL langsung mati | Port 3306 dipakai MySQL lain | Hentikan service MySQL Windows atau ubah port di `my.ini` |

## Backend

| Gejala | Penyebab | Solusi |
|---|---|---|
| `JWT_SECRET wajib diisi` | `.env` tidak terbaca | Jalankan dari folder `backend`; pastikan `.env` ada |
| `bind: Only one usage of each socket address` | Port 8080 terpakai | Ubah `APP_PORT`, atau `netstat -ano \| findstr :8080` lalu matikan proses |
| `cannot find package` / `no required module provides package` | Dependency belum terpasang | `go mod tidy` |
| `Scan error ... NULL to string` | Kolom nullable di-scan ke tipe biasa | Pakai `COALESCE` atau pointer |
| `unsupported Scan ... []uint8 into *time.Time` | DSN tanpa `parseTime=true` | Tambahkan di DSN |
| Response 401 padahal baru login | Secret berubah setelah restart / token kedaluwarsa | Login ulang |
| Response 400 `data tidak valid: ...` | Body JSON tidak sesuai aturan `binding` | Baca pesan, perbaiki body |

## Frontend

| Gejala | Penyebab | Solusi |
|---|---|---|
| Error CORS di console | Origin tidak diizinkan | Samakan `CORS_ORIGIN` dengan alamat frontend (termasuk port) |
| `Network Error` | Backend mati / proxy salah | Cek backend jalan dan `target` di `vite.config.js` |
| Setelah login tiba-tiba kembali ke login | Token tidak tersimpan / 401 | Cek tab Application -> Local Storage -> key `token` |
| Halaman putih | Error di komponen | Buka Console, baca error pertama |
| `Failed to resolve import` | Salah path / nama file (huruf besar-kecil) | Periksa path impor |
| Tanggal geser satu hari | Konversi zona waktu | Tampilkan string `YYYY-MM-DD` langsung (`task.due_date.slice(0,10)`) |
| Refresh `/dashboard` di produksi 404 | Server statis belum mengarahkan ke `index.html` | Tambahkan *SPA fallback* |

## Alat Bantu Debug
- **Go**: `log.Printf("%+v", variabel)`, `go vet ./...`, debugger VS Code (F5).
- **Browser**: DevTools -> Network (request, status, response), Console, Application (localStorage).
- **MySQL**: `SELECT * FROM users;` di phpMyAdmin untuk melihat data nyata.

## Tips Bertanya
Saat meminta bantuan sertakan: (1) pesan error lengkap, (2) file/baris terkait, (3) apa yang sudah dicoba, (4) versi Go/Node.

## Catatan Instruktur
Gunakan halaman ini sebagai referensi saat sesi praktik. Sengaja rusak-kan satu hal (matikan MySQL, ubah port) dan minta siswa mendiagnosis.
