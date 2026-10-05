# 06 - Hash Password dengan bcrypt

## Tujuan Pembelajaran
- Memahami mengapa password harus di-hash, bukan disimpan polos atau dienkripsi.
- Memakai `golang.org/x/crypto/bcrypt`.

## Konsep
Jika database bocor dan password tersimpan polos, semua akun (dan akun di situs lain, karena orang memakai password yang sama) ikut bocor.

| Cara | Aman? | Alasan |
|---|---|---|
| Teks polos | Tidak | Langsung terbaca |
| MD5 / SHA-1 / SHA-256 saja | Tidak | Terlalu cepat; mudah di-*brute force* dan ada tabel pelangi (*rainbow table*) |
| Enkripsi | Kurang | Bisa didekripsi bila kunci bocor |
| **bcrypt / argon2 / scrypt** | **Ya** | Sengaja lambat, memakai *salt* acak per password |

**Hash** adalah fungsi satu arah: password -> hash, tidak bisa dibalik. Untuk login, hash password yang diketik lalu **bandingkan** dengan hash tersimpan.

bcrypt:
- **Salt acak** otomatis -> dua password sama menghasilkan hash berbeda.
- **Cost** (default 10) -> makin besar makin lambat (2^cost iterasi). Naikkan seiring perkembangan perangkat keras.
- Batas input **72 byte**.
- Hasil berbentuk `$2a$10$<salt 22 karakter><hash 31 karakter>`, semuanya dalam satu string, jadi cukup satu kolom `VARCHAR(255)`.

### Pasang
```powershell
go get golang.org/x/crypto
```

## Langkah
Buat `contoh-kode/06-bcrypt/main.go`:

```go
// File: contoh-kode/06-bcrypt/main.go
package main

import (
	"fmt"
	"log"
	"time"

	"golang.org/x/crypto/bcrypt"
)

func HashPassword(password string) (string, error) {
	b, err := bcrypt.GenerateFromPassword([]byte(password), bcrypt.DefaultCost)
	return string(b), err
}

func CheckPassword(hash, password string) bool {
	return bcrypt.CompareHashAndPassword([]byte(hash), []byte(password)) == nil
}

func main() {
	password := "rahasia123"

	mulai := time.Now()
	hash1, err := HashPassword(password)
	if err != nil {
		log.Fatal(err)
	}
	fmt.Println("Hash 1:", hash1)
	fmt.Println("Waktu hashing:", time.Since(mulai).Round(time.Millisecond), "(sengaja lambat)")

	// Password yang sama menghasilkan hash BERBEDA karena salt acak.
	hash2, _ := HashPassword(password)
	fmt.Println("Hash 2:", hash2)
	fmt.Println("Hash sama?", hash1 == hash2)

	// Verifikasi
	fmt.Println("Password benar ->", CheckPassword(hash1, password))
	fmt.Println("Password salah ->", CheckPassword(hash1, "salah"))

	// Cost lebih tinggi = lebih aman tetapi lebih lambat
	cost, _ := bcrypt.Cost([]byte(hash1))
	fmt.Println("Cost:", cost)
}
```

Jalankan:
```powershell
go run ./contoh-kode/06-bcrypt
```

## Output yang Diharapkan
```
Hash 1: $2a$10$wp8O99mREKWjMVsjL87GCumcUcVGoOTqrX5KzTZfdSOi780Qft4zS
Waktu hashing: 109ms (sengaja lambat)
Hash 2: $2a$10$RiBF.7ifttVMK5oG4a7wk.i4KvxcVEH4RE5YSWWbq.77Q9KpxiXP6
Hash sama? false
Password benar -> true
Password salah -> false
Cost: 10
```
Isi hash dan waktunya berbeda pada tiap komputer.

## Kesalahan Umum
- Membandingkan hash dengan `==` (tidak mungkin cocok karena salt). Gunakan `CompareHashAndPassword`.
- Menyimpan password polos di log.
- Menetapkan batas panjang password lebih dari 72 byte tanpa menyadari bcrypt memotongnya.

## Latihan
Ukur waktu `GenerateFromPassword` untuk cost 4, 10, 12, dan 14. Berapa kali lipat lebih lambat?

## Catatan Instruktur
Estimasi 30 menit. Diskusikan: mengapa lambat itu **fitur** pada hash password?
