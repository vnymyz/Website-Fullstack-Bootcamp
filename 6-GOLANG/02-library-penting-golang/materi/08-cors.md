# 08 - CORS

## Tujuan Pembelajaran
- Memahami mengapa browser memblokir request lintas origin.
- Mengatur CORS di Gin dengan `gin-contrib/cors`.

## Konsep
**Origin** = kombinasi `protokol + domain + port`.
- `http://localhost:5173` (frontend React)
- `http://localhost:8080` (backend Go)

Berbeda port = **origin berbeda**. Browser menerapkan *Same-Origin Policy*: JavaScript dari satu origin tidak boleh membaca respons origin lain, kecuali server mengizinkannya lewat header CORS.

```
Browser (localhost:5173) --OPTIONS /api/tasks (preflight)--> Go (localhost:8080)
                         <-- Access-Control-Allow-Origin: http://localhost:5173
                         --GET /api/tasks (+ Authorization)-->
```

- **Preflight**: untuk request "tidak sederhana" (method selain GET/POST sederhana, atau memakai header `Authorization`/`Content-Type: application/json`), browser mengirim `OPTIONS` dulu untuk bertanya izin.
- CORS **bukan** fitur keamanan server; ia melindungi **pengguna di browser**. `curl`/Postman tidak terpengaruh.

### Pasang
```powershell
go get github.com/gin-contrib/cors
```

## Langkah
Buat `contoh-kode/08-cors/main.go`:

```go
// File: contoh-kode/08-cors/main.go
package main

import (
	"net/http"
	"time"

	"github.com/gin-contrib/cors"
	"github.com/gin-gonic/gin"
)

func main() {
	r := gin.Default()

	r.Use(cors.New(cors.Config{
		// Hanya origin ini yang diizinkan. Hindari "*" di produksi bila memakai credentials.
		AllowOrigins:     []string{"http://localhost:5173", "http://localhost:3000"},
		AllowMethods:     []string{"GET", "POST", "PUT", "DELETE", "OPTIONS"},
		AllowHeaders:     []string{"Origin", "Content-Type", "Authorization"},
		ExposeHeaders:    []string{"Content-Length"},
		AllowCredentials: true,
		MaxAge:           12 * time.Hour, // browser menyimpan hasil preflight
	}))

	r.GET("/data", func(c *gin.Context) {
		c.JSON(http.StatusOK, gin.H{"pesan": "Data dari backend Go"})
	})

	r.Run(":8080")
}
```

Jalankan:
```powershell
go run ./contoh-kode/08-cors
```

Uji preflight dengan `curl.exe`:
```powershell
curl.exe -i -X OPTIONS http://localhost:8080/data -H "Origin: http://localhost:5173" -H "Access-Control-Request-Method: GET"
```
Pada response akan ada `Access-Control-Allow-Origin: http://localhost:5173`. Coba dengan `Origin: http://evil.com`: header tersebut **tidak muncul**.

## Opsi Alternatif di Development
Vite dapat meneruskan `/api` ke backend lewat **proxy** (dipakai di project materi 4), sehingga browser melihat satu origin saja.

## Kesalahan Umum
| Gejala | Penyebab |
|---|---|
| `blocked by CORS policy: No 'Access-Control-Allow-Origin'` | Origin frontend tidak ada di `AllowOrigins` (cek port!) |
| Preflight gagal | Header `Authorization` belum ada di `AllowHeaders` |
| `AllowOrigins: ["*"]` + `AllowCredentials: true` | Tidak diizinkan oleh spesifikasi/library; sebutkan origin eksplisit |

## Latihan
Tambahkan origin kedua lewat environment variable `CORS_ORIGIN` yang dipisah koma.

## Catatan Instruktur
Estimasi 30 menit. Gambarkan alur preflight di papan tulis.
