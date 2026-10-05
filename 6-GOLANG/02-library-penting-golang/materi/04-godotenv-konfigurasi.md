# 04 - Konfigurasi dengan godotenv

## Tujuan Pembelajaran
- Memisahkan konfigurasi dan rahasia dari kode.
- Membaca file `.env` memakai `godotenv`.
- Membuat struct `Config` dengan nilai default dan konversi tipe.

## Konsep
Password database, secret JWT, dan port **tidak boleh** ditulis langsung di kode (apalagi di-commit ke Git). Praktik umum ("The Twelve-Factor App"): simpan konfigurasi di **environment variable**.

Saat development, mengetik variabel satu per satu itu merepotkan, sehingga dipakai file `.env`:
```
APP_NAME=Kelas Go
APP_PORT=8080
DEBUG=true
```
`godotenv.Load()` memuat isi file ke environment proses, lalu dibaca dengan `os.Getenv` / `os.LookupEnv`.

**Aturan emas**
1. `.env` **masuk `.gitignore`**.
2. Sediakan `.env.example` (tanpa rahasia) sebagai template.
3. Sediakan nilai default atau error yang jelas bila variabel wajib kosong.
4. Semua nilai environment berupa **string**; konversi sendiri dengan `strconv`.

## Langkah

### 1. Pasang
```powershell
go get github.com/joho/godotenv
```

### 2. Buat `.env`
Di `contoh-kode/04-godotenv/.env`:
```
APP_NAME=Kelas Go
APP_PORT=8080
DEBUG=true
MAX_USER=25
```

### 3. Program
Buat `contoh-kode/04-godotenv/main.go`:

```go
// File: contoh-kode/04-godotenv/main.go
package main

import (
	"fmt"
	"log"
	"os"
	"strconv"

	"github.com/joho/godotenv"
)

type Config struct {
	AppName string
	Port    string
	Debug   bool
	MaxUser int
}

func getEnv(key, fallback string) string {
	if v, ok := os.LookupEnv(key); ok && v != "" {
		return v
	}
	return fallback
}

func Load() (*Config, error) {
	// Membaca file .env di folder kerja saat ini. Jika tidak ada, tidak masalah:
	// di server biasanya env diatur langsung oleh sistem.
	if err := godotenv.Load(); err != nil {
		log.Println("file .env tidak ditemukan, memakai environment sistem")
	}

	debug, err := strconv.ParseBool(getEnv("DEBUG", "false"))
	if err != nil {
		return nil, fmt.Errorf("DEBUG tidak valid: %w", err)
	}
	maxUser, err := strconv.Atoi(getEnv("MAX_USER", "10"))
	if err != nil {
		return nil, fmt.Errorf("MAX_USER tidak valid: %w", err)
	}

	return &Config{
		AppName: getEnv("APP_NAME", "Aplikasi"),
		Port:    getEnv("APP_PORT", "8080"),
		Debug:   debug,
		MaxUser: maxUser,
	}, nil
}

func main() {
	cfg, err := Load()
	if err != nil {
		log.Fatal(err)
	}
	fmt.Printf("%+v\n", *cfg)
}
```

### 4. Jalankan
`godotenv.Load()` mencari `.env` di **folder kerja saat ini**, jadi jalankan dari dalam foldernya:
```powershell
cd contoh-kode\04-godotenv
go run .
```

## Output yang Diharapkan
```
{AppName:Kelas Go Port:8080 Debug:true MaxUser:25}
```

Hapus/ganti nama `.env` sementara untuk melihat nilai default:
```
2026/10/05 11:25:00 file .env tidak ditemukan, memakai environment sistem
{AppName:Aplikasi Port:8080 Debug:false MaxUser:10}
```

## Kesalahan Umum
- `.env` tidak terbaca karena program dijalankan dari folder lain.
- Spasi di sekitar `=` (`KEY = value`). Tulis tanpa spasi.
- Meng-commit `.env` berisi password -> segera ganti semua rahasia tersebut.

## Latihan
Tambahkan variabel wajib `DB_PASS`; buat `Load()` mengembalikan error jika kosong.

## Catatan Instruktur
Estimasi 30 menit. Tunjukkan contoh nyata kebocoran rahasia di repository publik (akun GitHub scanner otomatis).
