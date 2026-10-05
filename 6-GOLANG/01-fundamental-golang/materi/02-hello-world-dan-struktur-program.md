# 02 - Hello World dan Struktur Program

## Tujuan Pembelajaran
- Memahami bagian-bagian program Go: `package`, `import`, `func main`.
- Memakai `fmt.Println` dan `fmt.Printf`.
- Memahami komentar dan aturan formatting.

## Konsep
Setiap file Go punya urutan: **package -> import -> deklarasi (const, var, type, func)**.

```go
package main        // 1. nama paket
import "fmt"        // 2. paket yang dipakai
func main() { ... } // 3. kode
```

| Bagian | Arti |
|---|---|
| `package main` | Paket khusus yang menghasilkan program executable |
| `func main()` | Titik masuk. Tidak menerima argumen dan tidak mengembalikan nilai |
| `import` | Memanggil paket lain; paket yang diimpor tapi tidak dipakai = **error** |
| `{` harus di baris yang sama dengan `func` | Aturan Go (tidak boleh di baris baru) |

### Verb `Printf` yang sering dipakai
| Verb | Untuk |
|---|---|
| `%v` | Nilai apa saja (default) |
| `%+v` | Struct dengan nama field |
| `%d` | Bilangan bulat |
| `%f`, `%.2f` | Desimal (2 angka di belakang koma) |
| `%s` | String |
| `%q` | String dengan tanda kutip |
| `%t` | Boolean |
| `%T` | Tipe data |
| `%c` | Karakter |
| `%p` | Alamat pointer |
| `\n`, `\t` | Baris baru, tab |

## Langkah
Buat `contoh-kode/02-hello/main.go`:

```go
// File: contoh-kode/02-hello/main.go
// Setiap file Go diawali deklarasi package.
// "package main" = program yang bisa dijalankan (executable).
package main

// import: memanggil paket lain. "fmt" = format & cetak teks.
import "fmt"

// main() adalah titik masuk program. Go mulai menjalankan dari sini.
func main() {
	fmt.Println("Halo, Dunia!")
	fmt.Println("Selamat datang di kelas Golang")

	// Printf memakai "verb": %s teks, %d bilangan bulat, %f desimal, \n baris baru
	nama := "Budi"
	umur := 20
	fmt.Printf("Nama saya %s, umur %d tahun\n", nama, umur)
}
```

Jalankan:
```powershell
go run ./contoh-kode/02-hello
```

## Output yang Diharapkan
```
Halo, Dunia!
Selamat datang di kelas Golang
Nama saya Budi, umur 20 tahun
```

## Cara Membaca Output di Contoh Berikutnya
Mulai materi 03, satu program bisa mencetak banyak hasil sekaligus. Agar tidak bingung, setiap kelompok output diawali baris pembatas:

```
========== [3] Zero value ==========
```

- Angka `[3]` sama dengan komentar `// [3] Zero value` di kode, tepat di atas baris `fmt.Println("========== [3] ...")`.
- Semua baris di bawah pembatas, sampai pembatas berikutnya, dihasilkan oleh kode di bawah komentar `// [3]` tersebut.
- Pembatas ini hanya alat bantu belajar; di program nyata Anda tidak perlu menulisnya.

## Komentar
```go
// komentar satu baris

/*
   komentar
   beberapa baris
*/
```
Komentar tepat di atas fungsi/tipe yang diekspor menjadi **dokumentasi** (`go doc`).

## Formatting: `gofmt`
Go punya **satu gaya format resmi**. Jalankan `gofmt -w .` (atau simpan file di VS Code) agar kode seragam. Tidak ada debat tab vs spasi: Go memakai tab.

## Kesalahan Umum
| Error | Penyebab |
|---|---|
| `"os" imported and not used` | Impor tidak dipakai; hapus |
| `declared and not used: x` | Variabel tidak dipakai; hapus atau pakai `_` |
| `expected declaration, found ...` | Kode di luar fungsi |
| `undefined: fmt` | Lupa `import "fmt"` |
| `missing return` | Fungsi yang harus mengembalikan nilai tidak selalu `return` |

## Latihan
1. Cetak biodata (nama, umur, kota) memakai `Printf`.
2. Cetak `%v`, `%T` untuk nilai `42`, `3.14`, `"teks"`, `true`.
3. Sengaja impor `os` tanpa dipakai, baca pesan error, lalu perbaiki.

## Catatan Instruktur
Estimasi 30 menit. Biarkan siswa menikmati pesan error Go: jelas dan membantu.
