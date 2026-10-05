# Latihan Materi 2 - Library Penting Golang

## Soal 1 - Penghitung Kata (standard library, mudah)
Buat program `go run . teks.txt` yang:
1. Membaca file per baris (`bufio.Scanner`).
2. Menghitung total kata dan frekuensi tiap kata (huruf kecil, abaikan tanda baca).
3. Mencetak hasil dalam **JSON** berindentasi, beserta 3 kata terbanyak.

Petunjuk: `strings.FieldsFunc`, `unicode.IsLetter`, `sort.Slice`, `json.MarshalIndent`.

## Soal 2 - Todo API dengan Gin (sedang)
Buat REST API in-memory:

| Method | URL | Keterangan |
|---|---|---|
| GET | `/todos` | Daftar todo |
| POST | `/todos` | Buat todo. `judul` wajib, 3-100 karakter |
| PUT | `/todos/:id` | Ubah todo. 404 jika tidak ada |
| DELETE | `/todos/:id` | Hapus todo. 204 jika sukses, 404 jika tidak ada |

Syarat:
- Data bersama dilindungi `sync.Mutex`.
- Validasi memakai tag `binding`.
- Tulis **test** dengan `httptest` + `testify` untuk semua endpoint (sukses dan gagal).

## Soal 3 - Middleware Auth Sederhana (sedang)
Tambahkan ke Soal 2:
- `POST /login` menerima `{"username":"admin","password":"rahasia"}` dan mengembalikan token JWT (berlaku 15 menit).
- Middleware `Auth` yang melindungi `POST`, `PUT`, `DELETE` `/todos`. Tanpa token valid -> 401.
- Secret dibaca dari `.env` memakai `godotenv`.

## Soal 4 - Konfigurasi dan Logging (menantang)
Gunakan `log/slog` untuk mencatat setiap request (method, path, status, durasi) dalam format JSON ke `stdout`, via middleware Gin buatan sendiri.

## Soal Teori
1. Mengapa bcrypt lebih cocok untuk password dibanding SHA-256?
2. Apa isi payload JWT dan mengapa tidak boleh menyimpan rahasia di dalamnya?
3. Apa itu preflight request dan kapan browser mengirimnya?
4. Sebutkan dua alasan memisahkan `SetupRouter()` dari `main()`.
5. Apa beda `assert` dan `require` pada testify?
6. Kapan Anda memilih `net/http` dibanding Gin?

## Kunci Jawaban
- Kode: `latihan/solusi/01-hitung-kata` dan `latihan/solusi/02-todo-api` (termasuk test).
- Soal 3-4: dikerjakan sebagai pengembangan; rujuk materi 03, 04, 07, dan project materi 4.
- Teori: bagian "Konsep" pada file materi 06, 07, 08, 09, dan 03.

Menjalankan solusi:
```powershell
echo "Go itu asyik, Go itu cepat. Belajar Go asyik!" > teks.txt
go run ./latihan/solusi/01-hitung-kata teks.txt
go test ./latihan/solusi/02-todo-api -v
```
