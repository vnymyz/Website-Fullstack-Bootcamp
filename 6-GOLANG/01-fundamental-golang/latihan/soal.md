# Latihan Materi 1 - Fundamental Golang

Kerjakan di folder sendiri (mis. `latihan/pekerjaan-saya/`). Jangan membuka `solusi/` sebelum mencoba.

## Bagian A - Dasar (materi 02-06)

**A1. Biodata.** Simpan nama, umur, tinggi (float), dan status menikah (bool) ke variabel, lalu cetak dengan `Printf` rapi memakai verb yang tepat.

**A2. Konversi suhu.** Baca Celsius (`fmt.Scan`), cetak Fahrenheit dan Kelvin. Hati-hati pembagian bulat.

**A3. Bilangan prima.** Cetak semua bilangan prima di bawah 100 dan jumlahnya. (`solusi/01-bilangan-prima`)

**A4. FizzBuzz & tabel perkalian.** FizzBuzz 1-100 dan tabel perkalian 1-10.

**A5. Statistik slice.** Dari `[]int` hitung total, rata-rata, minimum, maksimum. Buat fungsi dengan **multiple return**.

**A6. Frekuensi kata.** Dari kalimat, hitung jumlah tiap kata memakai `map` dan cetak berurutan abjad.

## Bagian B - Struct, Pointer, Interface (materi 07-09)

**B1. Rekening bank.** Struct `Rekening` dengan method `Setor`, `Tarik`, `Saldo`. `Tarik` mengembalikan error jika saldo kurang. (`solusi/02-rekening`)

**B2. Bentuk.** Interface `Bentuk` dengan `Luas()`. Implementasikan `Persegi`, `Lingkaran`, `Segitiga`, lalu hitung total luas dari `[]Bentuk`. Buat juga tipe `Suhu` dengan `String()`. (`solusi/03-bentuk`)

**B3. Linked list.** `type Node struct { Nilai int; Next *Node }` dengan `TambahDiAkhir`, `Hapus(nilai)`, dan `Cetak`.

## Bagian C - Error, Defer, Package (materi 10-12)

**C1. Validasi pendaftaran.** `Daftar(nama, email string, umur int) error` dengan custom error `ErrValidasi{Field, Pesan}`. Kumpulkan semua error memakai `errors.Join`.

**C2. File aman.** Baca file angka (satu per baris), jumlahkan. Bungkus error dengan `%w`; di `main` bedakan "file tidak ada" dengan "baris bukan angka" memakai `errors.Is`/`errors.As`. Gunakan `defer` untuk menutup file.

**C3. Package.** Buat package `konversi` di `internal/konversi` dan impor dari `main`.

## Bagian D - Generics, Goroutine, Test (materi 13-15)

**D1. Stack generik** dengan `Push`, `Pop`, `Peek`, `Len` **beserta test**. (`solusi/05-stack-generik`)

**D2. Jumlah paralel.** Bagi slice besar menjadi beberapa bagian, jumlahkan tiap bagian di goroutine berbeda, gabungkan lewat channel. (`solusi/04-jumlah-paralel`)

**D3. Test table-driven.** Tulis fungsi `Slugify("Belajar Go Itu Asyik!") == "belajar-go-itu-asyik"` dan test dengan minimal 6 kasus (termasuk kosong, spasi ganda, simbol).

## Mini Proyek - Manajer Kontak (CLI)
Program command-line yang menyimpan kontak (nama, telepon, email) di memori:
1. Menu: tambah, lihat semua, cari berdasarkan nama, hapus, keluar.
2. Gunakan struct, slice/map, method, dan error.
3. Simpan/baca dari file JSON (`encoding/json`) agar data tidak hilang.
4. Tulis minimal 3 test.

Penilaian: fungsi berjalan (40%), struktur kode & penamaan (20%), penanganan error (20%), test (20%).

## Soal Teori
1. Apa beda `var x int` dan `x := 0`? Kapan `:=` tidak boleh dipakai?
2. Mengapa `len("Halo, 世界")` tidak sama dengan jumlah karakternya?
3. Jelaskan beda array dan slice. Mengapa mengubah hasil slicing dapat mengubah slice asli?
4. Kapan memakai value receiver dan pointer receiver?
5. Apa arti "interface di Go bersifat implisit"?
6. Mengapa memakai `%w` pada `fmt.Errorf`?
7. Apa yang terjadi bila `main` selesai sementara goroutine lain masih berjalan?
8. Apa itu data race dan bagaimana mendeteksinya?

## Kunci Jawaban
Soal yang bertanda `(solusi/...)` punya kunci jawaban kode di `latihan/solusi/`. Soal lainnya sengaja tanpa kunci; instruktur dapat memeriksanya langsung dengan kriteria pada soal. Jawaban teori terdapat di bagian "Konsep" tiap file materi.

Menjalankan solusi:
```powershell
go run ./latihan/solusi/01-bilangan-prima
go run ./latihan/solusi/02-rekening
go run ./latihan/solusi/03-bentuk
go run ./latihan/solusi/04-jumlah-paralel
go test ./latihan/solusi/05-stack-generik -v
```
