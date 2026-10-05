# Kelas Golang Fullstack: Dari Nol sampai Aplikasi Web

Materi ini dirancang untuk mengajar siswa dari **nol** sampai bisa membuat **aplikasi web fullstack** dengan:

- **Frontend**: React JS (Vite)
- **Backend**: Golang (Gin)
- **Database**: MySQL

Semua materi ditulis dalam Bahasa Indonesia, berisi langkah demi langkah, **kode lengkap**, dan petunjuk **file apa yang harus dibuat dan di folder mana**.

---

## Struktur Folder Materi

```
Go-New/
├── README.md                              <- (file ini) silabus & panduan instruktur
├── 01-fundamental-golang/                 <- Materi 1: Dasar-dasar Go
├── 02-library-penting-golang/             <- Materi 2: Library yang sering dipakai
├── 03-golang-mysql/                       <- Materi 3: Menghubungkan Go ke MySQL
└── 04-project-fullstack-task-manager/     <- Materi 4: Project React + Go + MySQL
```

Setiap folder materi punya struktur yang sama:

| Folder / File | Isi |
|---|---|
| `README.md` | Ringkasan, tujuan belajar, cara menjalankan contoh |
| `materi/` | File `.md` teori + kode + penjelasan, dibaca **berurutan** |
| `contoh-kode/` | Kode yang bisa langsung dijalankan (`go run`) |
| `latihan/soal.md` | Soal latihan |
| `latihan/solusi/` | Kunci jawaban (jangan dibagikan sebelum latihan selesai) |

---

## Prasyarat Siswa

| Kebutuhan | Versi | Keterangan |
|---|---|---|
| Windows / macOS / Linux | - | Panduan memakai contoh Windows |
| Go | 1.23 atau lebih baru | https://go.dev/dl/ |
| Node.js | 20 atau lebih baru | https://nodejs.org (untuk materi 4) |
| VS Code | terbaru | + ekstensi **Go** (oleh Go Team at Google) |
| XAMPP atau Laragon | terbaru | Untuk MySQL + phpMyAdmin (materi 3 & 4) |
| Git | terbaru | Opsional tapi disarankan |
| Postman / Thunder Client | - | Untuk menguji API |

> **Catatan versi Go:** Materi 1 cukup Go 1.23+. Materi 2-4 memakai library terbaru (Gin, GORM, dll.) yang `go.mod`-nya meminta Go lebih baru (tertulis `go 1.27.1`). Go modern akan **mengunduh toolchain yang sesuai otomatis** (`GOTOOLCHAIN=auto`, default sejak Go 1.21). Cara paling mudah: pasang Go versi stabil terbaru dari https://go.dev/dl/.

Pengetahuan awal yang membantu (bukan wajib): konsep dasar pemrograman (variabel, if, loop), HTML/CSS/JavaScript dasar.

---

## Silabus dan Estimasi Waktu

Estimasi 1 pertemuan = 2-3 jam.

### Pertemuan 0 - Persiapan (1 pertemuan)
- Install Go, VS Code, Node.js, XAMPP/Laragon, Git
- Cek semua tool dengan `go version`, `node -v`, `npm -v`
- Git dasar: `init`, `add`, `commit`

### Materi 1 - Fundamental Golang (4-5 pertemuan)
| Pertemuan | Topik | File |
|---|---|---|
| 1 | Instalasi, Hello World, struktur program | `01`, `02` |
| 2 | Variabel, konstanta, tipe data, operator, kontrol alur | `03`, `04` |
| 3 | Fungsi, array, slice, map | `05`, `06` |
| 4 | Struct, method, pointer, interface | `07`, `08`, `09` |
| 5 | Error handling, defer/panic/recover, package, generics, goroutine, testing | `10`-`15` |

### Materi 2 - Library Penting Golang (2-3 pertemuan)
| Pertemuan | Topik | File |
|---|---|---|
| 1 | Standard library (fmt, strings, strconv, time, json, slog, context) | `01` |
| 2 | net/http, Gin, godotenv, validator | `02`-`05` |
| 3 | bcrypt, JWT, CORS, testify, tooling, peta ekosistem | `06`-`11` |

### Materi 3 - Golang + MySQL (2-3 pertemuan)
| Pertemuan | Topik | File |
|---|---|---|
| 1 | Setup MySQL, driver, koneksi, pool, INSERT | `01`-`04` |
| 2 | SELECT, UPDATE, DELETE, prepared statement, NULL | `05`-`08` |
| 3 | Transaction, context, repository pattern, GORM | `09`-`12` |

### Materi 4 - Project Fullstack Task Manager (4-5 pertemuan)
| Pertemuan | Topik | File |
|---|---|---|
| 1 | Arsitektur, desain database, setup backend, config & DB | `01`-`04` |
| 2 | Model, repository, response helper, auth JWT | `05`-`07` |
| 3 | Middleware, CRUD task, routing, testing API | `08`-`11` |
| 4 | Setup React, Axios, AuthContext, halaman Login/Register | `12`-`14` |
| 5 | Dashboard & CRUD UI, integrasi, troubleshooting, pengembangan lanjutan | `15`-`18` |

**Total estimasi: 13-17 pertemuan.**

---

## Rekomendasi Tambahan dari Saya (Senior Fullstack)

Hal-hal di bawah ini sudah dimasukkan ke dalam materi, atau disebut sebagai tugas lanjutan:

1. **Pertemuan 0 (setup & Git)** - 70% masalah di pertemuan awal adalah masalah instalasi. Pisahkan dari materi inti.
2. **Unit testing sejak awal** - ada di materi 1 (`go test`, table-driven test) dan materi 2 (`httptest` + `testify`). Kebiasaan menulis test lebih baik dibangun sejak dini.
3. **Keamanan dasar** - hash password dengan bcrypt, SQL injection & prepared statement, rahasia di `.env`, jangan commit `.env`, validasi input. Dibahas di materi 2, 3, dan 4.
4. **Error handling idiomatis Go** - `errors.Is/As`, wrapping dengan `%w`. Siswa dari bahasa lain biasanya kaget dengan gaya Go yang tidak punya `try/catch`.
5. **Concurrency** - goroutine & channel adalah keunggulan Go. Dikenalkan di materi 1, cukup dasar saja.
6. **Struktur project yang rapi** - `cmd/` dan `internal/`, pola handler -> repository. Ini struktur yang dipakai di industri.
7. **Tooling developer** - `gofmt`, `go vet`, `golangci-lint`, `air` (hot reload). Membuat siswa produktif.
8. **Context & timeout** pada query database - praktik produksi yang sering dilewatkan di tutorial.
9. **Perbandingan database/sql vs GORM** - siswa paham SQL mentah dulu, baru kenal ORM.
10. **Kuis & tugas akhir per modul** - ada latihan dan solusi di tiap materi.

### Topik bonus (opsional, disebut di `docs/18-pengembangan-lanjutan.md`)
- Docker & Docker Compose (Go + MySQL + React)
- Migrasi database dengan `golang-migrate`
- Dokumentasi API dengan Swagger (`swaggo/swag`)
- Refresh token & httpOnly cookie
- Deployment (Railway, Render, VPS)
- Logging terstruktur (`log/slog`, `zerolog`)
- CI sederhana dengan GitHub Actions (`go vet`, `go test`)
- Upload file, pagination lanjutan, WebSocket

---

## Rubrik Penilaian yang Disarankan

| Aspek | Bobot |
|---|---|
| Latihan materi 1 | 20% |
| Latihan materi 2 & 3 | 20% |
| Project fullstack (fitur berjalan) | 35% |
| Kualitas kode (struktur, penamaan, error handling) | 15% |
| Presentasi / demo | 10% |

---

## Cara Memakai Materi Ini

**Untuk instruktur**
1. Baca `README.md` di folder materi untuk tujuan & estimasi waktu.
2. Tiap file di `materi/` punya bagian **Catatan Instruktur** (waktu, poin diskusi).
3. Jalankan `contoh-kode` sendiri sebelum mengajar.
4. Bagikan `latihan/soal.md`, simpan `latihan/solusi/` sampai latihan selesai.

**Untuk siswa**
1. Ikuti file di `materi/` berurutan. Ketik kode sendiri, jangan hanya copy-paste.
2. Setiap kali ada "Buat file ...", buat file di lokasi yang disebutkan.
3. Jalankan perintah di terminal dan cocokkan hasilnya dengan **Output yang diharapkan**.
4. Kerjakan latihan sebelum lanjut ke materi berikutnya.

---

## Konvensi Penulisan

- `kode` = nama file, perintah, atau potongan kode
- Blok kode berjudul `// File: path/ke/file.go` artinya isi file itu **lengkap**
- Perintah terminal ditulis untuk **PowerShell / Terminal VS Code**
- Placeholder seperti `NAMA_ANDA` harus diganti sesuai kebutuhan
