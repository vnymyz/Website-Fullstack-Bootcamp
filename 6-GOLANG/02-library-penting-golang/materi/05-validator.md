# 05 - Validasi Input dengan validator

## Tujuan Pembelajaran
- Memvalidasi input memakai tag `binding` (validator v10).
- Mengubah error validator menjadi pesan yang ramah.

## Konsep
**Jangan pernah percaya input dari client.** Validasi harus dilakukan di **backend**, walaupun frontend sudah memvalidasi (frontend bisa dilewati).

Gin memakai `go-playground/validator/v10`. Aturan ditulis di struct tag `binding`:

| Tag | Arti |
|---|---|
| `required` | Tidak boleh kosong/nol |
| `min=3`, `max=50` | Panjang string / nilai angka |
| `gte=17`, `lte=100` | Lebih besar-sama / lebih kecil-sama |
| `email`, `url` | Format |
| `oneof=siswa guru` | Salah satu nilai |
| `eqfield=Password` | Harus sama dengan field lain |
| `omitempty` | Lewati aturan lain jika kosong |

Beberapa aturan dipisah koma dan berlaku **AND**.

> Gotcha: `required` pada `int` menolak nilai `0`, pada `bool` menolak `false`. Untuk nilai nol yang valid gunakan tipe pointer (`*int`).

### Pasang
Sudah ikut terpasang bersama Gin. Jika ingin memakai langsung:
```powershell
go get github.com/go-playground/validator/v10
```

## Langkah
Buat `contoh-kode/05-validator/main.go`:

```go
// File: contoh-kode/05-validator/main.go
package main

import (
	"errors"
	"fmt"
	"net/http"

	"github.com/gin-gonic/gin"
	"github.com/go-playground/validator/v10"
)

type DaftarRequest struct {
	Nama     string `json:"nama" binding:"required,min=3,max=50"`
	Email    string `json:"email" binding:"required,email"`
	Umur     int    `json:"umur" binding:"required,gte=17,lte=100"`
	Password string `json:"password" binding:"required,min=8"`
	Konfirm  string `json:"konfirmasi" binding:"required,eqfield=Password"`
	Role     string `json:"role" binding:"omitempty,oneof=siswa guru"`
	Website  string `json:"website" binding:"omitempty,url"`
}

// pesanError menerjemahkan error validator menjadi pesan yang ramah (Bahasa Indonesia).
func pesanError(err error) map[string]string {
	hasil := map[string]string{}
	var ve validator.ValidationErrors
	if errors.As(err, &ve) {
		for _, fe := range ve {
			var pesan string
			switch fe.Tag() {
			case "required":
				pesan = "wajib diisi"
			case "min":
				pesan = fmt.Sprintf("minimal %s karakter/nilai", fe.Param())
			case "max":
				pesan = fmt.Sprintf("maksimal %s karakter/nilai", fe.Param())
			case "email":
				pesan = "format email tidak valid"
			case "gte":
				pesan = fmt.Sprintf("minimal %s", fe.Param())
			case "lte":
				pesan = fmt.Sprintf("maksimal %s", fe.Param())
			case "eqfield":
				pesan = "harus sama dengan " + fe.Param()
			case "oneof":
				pesan = "harus salah satu dari: " + fe.Param()
			default:
				pesan = "tidak valid (" + fe.Tag() + ")"
			}
			hasil[fe.Field()] = pesan
		}
		return hasil
	}
	hasil["body"] = "format JSON tidak valid"
	return hasil
}

func main() {
	r := gin.Default()

	r.POST("/daftar", func(c *gin.Context) {
		var req DaftarRequest
		if err := c.ShouldBindJSON(&req); err != nil {
			c.JSON(http.StatusBadRequest, gin.H{"errors": pesanError(err)})
			return
		}
		c.JSON(http.StatusCreated, gin.H{"pesan": "pendaftaran berhasil", "nama": req.Nama})
	})

	r.Run(":8080")
}
```

Jalankan:
```powershell
go run ./contoh-kode/05-validator
```

Uji dengan data salah:
```powershell
Invoke-RestMethod -Method Post -Uri http://localhost:8080/daftar -ContentType "application/json" `
  -Body '{"nama":"Al","email":"bukan-email","umur":15,"password":"123","konfirmasi":"456"}'
```
PowerShell menampilkan error 400; untuk melihat isinya gunakan `curl.exe`:
```powershell
curl.exe -s -X POST http://localhost:8080/daftar -H "Content-Type: application/json" -d "{\"nama\":\"Al\",\"email\":\"bukan-email\",\"umur\":15,\"password\":\"123\",\"konfirmasi\":\"456\"}"
```

## Output yang Diharapkan (kira-kira)
```json
{"errors":{"Email":"format email tidak valid","Konfirm":"harus sama dengan Password","Nama":"minimal 3 karakter/nilai","Password":"minimal 8 karakter/nilai","Umur":"minimal 17"}}
```
Nama field pada `errors` mengikuti nama field **struct** (`Konfirm`, `Nama`). Anda dapat mengubahnya menjadi nama JSON dengan `validator.RegisterTagNameFunc` (latihan).

Data benar:
```powershell
Invoke-RestMethod -Method Post -Uri http://localhost:8080/daftar -ContentType "application/json" `
  -Body '{"nama":"Andi","email":"andi@mail.com","umur":20,"password":"rahasia123","konfirmasi":"rahasia123"}'
```

## Kesalahan Umum
- Memakai `required` untuk angka 0 yang sah.
- Menaruh `binding` tanpa memanggil `ShouldBindJSON`/`ShouldBind`.
- Menampilkan error mentah validator ke pengguna akhir.

## Latihan
Daftarkan `RegisterTagNameFunc` agar nama field di pesan error memakai nama JSON (`konfirmasi`, `nama`, ...).

## Catatan Instruktur
Estimasi 45 menit. Tunjukkan bahwa menghapus validasi di frontend tidak membuat backend aman.
