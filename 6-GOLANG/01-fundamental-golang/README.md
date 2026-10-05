# Materi 1 - Fundamental Golang

Dasar-dasar bahasa Go dari nol: sintaks, tipe data, fungsi, struct, interface, error handling, package, generics, goroutine, dan unit test.

## Tujuan Belajar
Setelah materi ini siswa mampu:
1. Memasang Go dan menjalankan program.
2. Memakai variabel, tipe data, kontrol alur, fungsi, slice, dan map.
3. Memodelkan data dengan struct, method, dan interface.
4. Memahami pointer dan menangani error secara idiomatik.
5. Mengorganisasi kode dengan package dan module.
6. Memakai generics dan concurrency dasar (goroutine, channel, mutex).
7. Menulis unit test.

## Prasyarat
Konsep dasar pemrograman (variabel, percabangan, perulangan). Go **1.23 atau lebih baru** (contoh memakai `range` pada bilangan bulat dan paket `slices`/`maps`).

## Struktur Folder
```
01-fundamental-golang/
├── README.md
├── go.mod                       <- module "fundamental"
├── materi/                      <- 15 file panduan, baca berurutan
├── contoh-kode/
│   ├── 02-hello/main.go
│   ├── 03-variabel/main.go
│   ├── 04-kontrol-alur/main.go
│   ├── 05-fungsi/main.go
│   ├── 06-koleksi/main.go
│   ├── 07-struct-method/main.go
│   ├── 08-pointer/main.go
│   ├── 09-interface/main.go
│   ├── 10-error/main.go
│   ├── 11-defer-panic/main.go
│   ├── 12-package/ (main.go + matematika/matematika.go)
│   ├── 13-generics/main.go
│   ├── 14-goroutine/main.go
│   └── 15-testing/ (hitung.go + hitung_test.go)
└── latihan/
    ├── soal.md
    └── solusi/
```
(Materi 01 hanya instalasi, tidak punya folder contoh.)

## Cara Menjalankan Contoh
```powershell
cd 01-fundamental-golang
go run ./contoh-kode/02-hello
go run ./contoh-kode/06-koleksi
go test ./contoh-kode/15-testing -v
```

## Cara Membaca Output
Satu program sering mencetak banyak hasil. Setiap kelompok output diberi pembatas bernomor:

```
========== [3] Zero value ==========
int=0 float=0.0 string="" bool=false pointer=<nil>
```

Nomor `[3]` sama dengan komentar `// [3] Zero value` di kode, tepat di atas baris yang mencetak pembatas. Jadi saat menjelaskan, instruktur cukup berkata "lihat bagian [3]". Berlaku untuk contoh `03` sampai `14` (kecuali `12` dan `15`) dan solusi `02-rekening`.

## Daftar Materi

| No | File | Topik | Estimasi |
|---|---|---|---|
| 01 | `materi/01-instalasi-dan-setup.md` | Install Go, VS Code, `go mod init` | 45-60 mnt |
| 02 | `materi/02-hello-world-dan-struktur-program.md` | Struktur program, `fmt` | 30 mnt |
| 03 | `materi/03-variabel-konstanta-tipe-data.md` | Variabel, tipe, konstanta, `iota` | 60 mnt |
| 04 | `materi/04-operator-dan-kontrol-alur.md` | `if`, `switch`, `for`, `range` | 60-75 mnt |
| 05 | `materi/05-fungsi.md` | Fungsi, closure, variadic | 75 mnt |
| 06 | `materi/06-array-slice-map.md` | Array, slice, map | 90 mnt |
| 07 | `materi/07-struct-dan-method.md` | Struct, method, embedding | 90 mnt |
| 08 | `materi/08-pointer.md` | Pointer | 60 mnt |
| 09 | `materi/09-interface.md` | Interface, type switch | 75 mnt |
| 10 | `materi/10-error-handling.md` | `errors.Is/As`, `%w` | 75 mnt |
| 11 | `materi/11-defer-panic-recover.md` | defer, panic, recover | 45 mnt |
| 12 | `materi/12-package-dan-module.md` | Package, module, `go get` | 60 mnt |
| 13 | `materi/13-generics.md` | Generics | 60 mnt |
| 14 | `materi/14-goroutine-channel-sync.md` | Concurrency | 90 mnt |
| 15 | `materi/15-unit-testing-dasar.md` | `go test`, table-driven | 60 mnt |

Total sekitar 17 jam (4-5 pertemuan @ 3-4 jam, atau 8 pertemuan @ 2 jam).

## Saran Pembagian Pertemuan
1. **Pertemuan 1**: 01, 02, 03
2. **Pertemuan 2**: 04, 05, 06
3. **Pertemuan 3**: 07, 08, 09
4. **Pertemuan 4**: 10, 11, 12
5. **Pertemuan 5**: 13, 14, 15 + mini proyek (Manajer Kontak)

## Cara Belajar yang Disarankan
1. Baca bagian **Konsep**.
2. **Ketik sendiri** kode contoh (jangan hanya copy-paste).
3. Jalankan dan bandingkan dengan **Output yang Diharapkan**.
4. Ubah-ubah kode, sengaja bikin error, baca pesannya.
5. Kerjakan **Latihan** di akhir tiap file.

## Status Verifikasi
Seluruh contoh di `contoh-kode/` dan `latihan/solusi/` lolos `gofmt`, `go vet`, dijalankan, dan output di materi dicocokkan dengan hasil eksekusi nyata. Test pada `15-testing` dan `05-stack-generik` lulus. Contoh `11-defer-panic` **sengaja berakhir dengan panic**.
