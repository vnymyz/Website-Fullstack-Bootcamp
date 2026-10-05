# 10 - Tools Developer Go

## Tujuan Pembelajaran
- Memakai perintah bawaan `go` sehari-hari.
- Memasang `air` (hot reload) dan `golangci-lint`.
- Mengenal debugging di VS Code.

## Perintah Bawaan yang Wajib Hafal

| Perintah | Fungsi |
|---|---|
| `go run .` / `go run ./cmd/api` | Jalankan program |
| `go build -o app.exe ./cmd/api` | Kompilasi menjadi binary |
| `go fmt ./...` / `gofmt -w .` | Rapikan format kode |
| `go vet ./...` | Cari kesalahan umum (format string salah, dsb) |
| `go test ./...` | Jalankan semua test |
| `go test -race ./...` | Deteksi data race |
| `go test -cover ./...` | Persentase cakupan test |
| `go mod init nama` | Buat module |
| `go get paket@versi` | Tambah/ubah dependency |
| `go mod tidy` | Rapikan `go.mod` (hapus yang tidak dipakai, tambah yang kurang) |
| `go list -m all` | Daftar dependency |
| `go doc fmt.Printf` | Baca dokumentasi di terminal |
| `go env` | Lihat konfigurasi Go |

Biasakan sebelum commit: `gofmt -l .` (harus kosong), `go vet ./...`, `go test ./...`.

## Air - Hot Reload
Server otomatis restart saat file berubah.

```powershell
go install github.com/air-verse/air@latest
```
Pastikan `%USERPROFILE%\go\bin` ada di `PATH`. Buat `.air.toml` di folder project:
```toml
root = "."
tmp_dir = "tmp"

[build]
  cmd = "go build -o ./tmp/main.exe ./cmd/api"
  bin = "tmp/main.exe"
  include_ext = ["go"]
  exclude_dir = ["tmp"]
  delay = 500
```
Jalankan `air` di folder tersebut. Tambahkan `tmp/` ke `.gitignore`.

## golangci-lint - Linter
Menjalankan banyak pemeriksa kode sekaligus.
```powershell
# lihat petunjuk instalasi terbaru di https://golangci-lint.run/welcome/install/
golangci-lint run ./...
```
Mulailah dengan konfigurasi default; tambah aturan seiring project berkembang.

## VS Code
1. Pasang ekstensi **Go** (Go Team at Google).
2. Tekan `Ctrl+Shift+P` -> **Go: Install/Update Tools** -> pilih semua (termasuk `gopls` dan `dlv`).
3. Pengaturan yang disarankan: format saat simpan (`"editor.formatOnSave": true`) dan `"go.useLanguageServer": true`.
4. **Debugging**: tekan `F5`, pilih "Go: Launch Package". Pasang *breakpoint* dengan klik di margin kiri.

## Struktur Project yang Disarankan
```
myapp/
├── cmd/api/main.go        <- titik masuk, tipis
├── internal/              <- kode privat module ini
│   ├── config/
│   ├── handlers/
│   ├── repository/
│   └── ...
├── go.mod
└── README.md
```

## Latihan
1. Sengaja salah ketik format string: `fmt.Printf("%d", "teks")`, jalankan `go vet`, dan baca peringatannya.
2. Ubah indentasi kode jadi berantakan lalu rapikan dengan `gofmt -w .`.
3. Jalankan salah satu contoh server memakai `air` dan ubah teks respons.

## Catatan Instruktur
Estimasi 45 menit. Tekankan kebiasaan: format, vet, test sebelum commit.
