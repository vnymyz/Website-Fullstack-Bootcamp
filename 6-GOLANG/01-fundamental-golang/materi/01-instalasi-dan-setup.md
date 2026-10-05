# 01 - Instalasi dan Setup

## Tujuan Pembelajaran
- Mengenal Go dan alasan memakainya.
- Memasang Go, VS Code, dan ekstensi Go.
- Membuat module pertama dan menjalankan program.

## Apa itu Go?
**Go (Golang)** dibuat oleh Google (2009). Cirinya:
- **Sederhana**: sintaks kecil, mudah dibaca.
- **Cepat**: dikompilasi menjadi *binary* native.
- **Concurrency bawaan**: goroutine dan channel.
- **Satu file hasil**: binary mandiri, mudah di-deploy.
- Banyak dipakai untuk backend, API, microservice, DevOps (Docker, Kubernetes ditulis dengan Go).

| Bahasa | Dijalankan lewat | Catatan |
|---|---|---|
| Go | Kompilasi -> binary | Tipe statis, cepat |
| JavaScript / Python | Interpreter | Dinamis, lebih lambat |
| Java | JVM | Butuh runtime |

## Langkah

### 1. Pasang Go
1. Buka https://go.dev/dl/ lalu unduh installer Windows (`.msi`), gunakan **Go 1.23 atau lebih baru**.
2. Jalankan installer sampai selesai (default: `C:\Program Files\Go`).
3. **Tutup dan buka kembali** terminal/VS Code agar `PATH` diperbarui.
4. Periksa:
```powershell
go version
```
Output contoh: `go version go1.24.0 windows/amd64` (angka versi bisa berbeda).

Lihat konfigurasi penting:
```powershell
go env GOPATH GOROOT GOOS GOARCH
```
- `GOROOT`: lokasi instalasi Go.
- `GOPATH`: tempat Go menyimpan modul unduhan (`%USERPROFILE%\go`).

### 2. Pasang VS Code dan ekstensi Go
1. Unduh VS Code: https://code.visualstudio.com
2. Buka tab **Extensions** (`Ctrl+Shift+X`), cari **Go** (publisher: *Go Team at Google*), klik **Install**.
3. Tekan `Ctrl+Shift+P` -> ketik **Go: Install/Update Tools** -> centang semua -> OK.
4. Aktifkan format otomatis saat simpan: **File -> Preferences -> Settings**, cari `format on save`, centang.

### 3. Buat folder kerja dan module pertama
```powershell
mkdir belajar-go
cd belajar-go
go mod init belajar-go
```
Perintah ini membuat file `go.mod`:
```
module belajar-go

go 1.23
```
`go.mod` = identitas module dan daftar dependency. **Setiap project Go berada dalam sebuah module.**

### 4. Program pertama
Buat file `main.go` di dalam folder `belajar-go`:
```go
package main

import "fmt"

func main() {
	fmt.Println("Halo, Dunia!")
}
```

Jalankan:
```powershell
go run .
```
Output:
```
Halo, Dunia!
```

### 5. Perintah dasar
| Perintah | Fungsi |
|---|---|
| `go run .` | Kompilasi sementara dan langsung jalankan |
| `go build` | Hasilkan file `.exe` di folder saat ini |
| `go build -o halo.exe` | Beri nama output |
| `go fmt ./...` | Rapikan format kode |
| `go vet ./...` | Periksa kesalahan umum |
| `go mod tidy` | Rapikan dependency |

Coba:
```powershell
go build -o halo.exe
.\halo.exe
```
Berkas `halo.exe` bisa dikirim ke komputer lain **tanpa** memasang Go.

## Cara Memakai Materi Ini
Folder `01-fundamental-golang` sudah berisi module `fundamental` (file `go.mod`) dan semua contoh di `contoh-kode/`. Menjalankannya:
```powershell
cd 01-fundamental-golang
go run ./contoh-kode/02-hello
```
Anda boleh juga mengetik ulang kode di project `belajar-go` sendiri (lebih baik untuk belajar).

## Kesalahan Umum
| Gejala | Solusi |
|---|---|
| `'go' is not recognized as an internal or external command` | Tutup-buka terminal; cek `PATH` memuat `C:\Program Files\Go\bin` |
| `go: go.mod file not found in current directory` | Jalankan `go mod init` atau pindah ke folder yang benar |
| `package main is not in std` | Menjalankan `go run main.go` dari folder yang salah |
| VS Code tidak menampilkan saran kode | Pasang tools: **Go: Install/Update Tools** |

## Latihan
1. Pasang Go, tunjukkan hasil `go version`.
2. Ubah program menjadi mencetak nama dan kota Anda.
3. Kompilasi menjadi `.exe` dan jalankan.

## Catatan Instruktur
Estimasi 45-60 menit. Pastikan **semua** siswa berhasil menjalankan `go run .` sebelum lanjut. Siapkan cadangan: Go Playground (https://go.dev/play) untuk siswa yang laptopnya bermasalah.
