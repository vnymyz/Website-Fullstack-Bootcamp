# 03 - Gin Framework

## Tujuan Pembelajaran
- Memahami mengapa memakai Gin.
- Membuat route, group, path param, query string, dan binding JSON.
- Menulis middleware sendiri.

## Konsep
**Gin** adalah web framework Go paling populer: cepat, ringkas, dengan routing, middleware, binding, dan validasi bawaan. Gin dibangun di atas `net/http`.

| Kebutuhan | `net/http` | Gin |
|---|---|---|
| Kirim JSON | 3 baris | `c.JSON(200, data)` |
| Path param | `r.PathValue("id")` | `c.Param("id")` |
| Query | `r.URL.Query().Get("q")` | `c.Query("q")` / `c.DefaultQuery` |
| Body JSON + validasi | decode manual | `c.ShouldBindJSON(&req)` |
| Middleware | bungkus handler manual | `r.Use(...)` |
| Group route | manual | `r.Group("/api")` |

### Pasang
```powershell
go get github.com/gin-gonic/gin
```

### `gin.Context`
`c` adalah pusat segalanya: membaca request, menulis response, menyimpan data antar middleware (`c.Set` / `c.Get`).

### Middleware
Fungsi yang berjalan sebelum/sesudah handler:
```
Request -> Logger -> ApiKey -> Handler -> (kembali ke Logger) -> Response
```
- `c.Next()` : lanjut ke berikutnya, lalu kembali setelah selesai.
- `c.Abort...()` : hentikan rantai.

## Langkah
Buat `contoh-kode/03-gin/main.go`:

```go
// File: contoh-kode/03-gin/main.go
package main

import (
	"log"
	"net/http"
	"strconv"
	"time"

	"github.com/gin-gonic/gin"
)

type Catatan struct {
	ID    int    `json:"id"`
	Judul string `json:"judul"`
	Isi   string `json:"isi"`
}

type CatatanRequest struct {
	Judul string `json:"judul" binding:"required,min=3,max=100"`
	Isi   string `json:"isi" binding:"required"`
}

var (
	catatan = []Catatan{{1, "Belanja", "Beli susu"}}
	nextID  = 2
)

// Middleware: mencatat waktu eksekusi tiap request.
func Logger() gin.HandlerFunc {
	return func(c *gin.Context) {
		mulai := time.Now()
		c.Next() // jalankan handler berikutnya
		log.Printf("%s %s -> %d (%v)", c.Request.Method, c.Request.URL.Path, c.Writer.Status(), time.Since(mulai))
	}
}

// Middleware: contoh proteksi sederhana dengan API key di header.
func ApiKey(key string) gin.HandlerFunc {
	return func(c *gin.Context) {
		if c.GetHeader("X-API-Key") != key {
			c.AbortWithStatusJSON(http.StatusUnauthorized, gin.H{"error": "API key salah"})
			return
		}
		c.Next()
	}
}

func main() {
	r := gin.New() // tanpa middleware bawaan
	r.Use(gin.Recovery(), Logger())

	r.GET("/ping", func(c *gin.Context) {
		c.JSON(http.StatusOK, gin.H{"pesan": "pong"})
	})

	// Path param dan query string
	r.GET("/salam/:nama", func(c *gin.Context) {
		nama := c.Param("nama")
		bahasa := c.DefaultQuery("bahasa", "id") // /salam/Budi?bahasa=en
		if bahasa == "en" {
			c.JSON(http.StatusOK, gin.H{"pesan": "Hello, " + nama})
			return
		}
		c.JSON(http.StatusOK, gin.H{"pesan": "Halo, " + nama})
	})

	// Group route
	api := r.Group("/api/v1")
	{
		api.GET("/catatan", func(c *gin.Context) {
			c.JSON(http.StatusOK, catatan)
		})

		api.GET("/catatan/:id", func(c *gin.Context) {
			id, err := strconv.Atoi(c.Param("id"))
			if err != nil {
				c.JSON(http.StatusBadRequest, gin.H{"error": "id harus angka"})
				return
			}
			for _, n := range catatan {
				if n.ID == id {
					c.JSON(http.StatusOK, n)
					return
				}
			}
			c.JSON(http.StatusNotFound, gin.H{"error": "tidak ditemukan"})
		})

		// Group dengan middleware: hanya yang punya API key
		aman := api.Group("", ApiKey("rahasia123"))
		aman.POST("/catatan", func(c *gin.Context) {
			var req CatatanRequest
			if err := c.ShouldBindJSON(&req); err != nil {
				c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
				return
			}
			n := Catatan{ID: nextID, Judul: req.Judul, Isi: req.Isi}
			nextID++
			catatan = append(catatan, n)
			c.JSON(http.StatusCreated, n)
		})
	}

	log.Fatal(r.Run(":8080"))
}
```

Jalankan:
```powershell
go run ./contoh-kode/03-gin
```

Uji di terminal lain:
```powershell
Invoke-RestMethod http://localhost:8080/ping
Invoke-RestMethod "http://localhost:8080/salam/Budi?bahasa=en"
Invoke-RestMethod http://localhost:8080/api/v1/catatan

# Tanpa API key -> 401
Invoke-RestMethod -Method Post -Uri http://localhost:8080/api/v1/catatan -ContentType "application/json" -Body '{"judul":"Tes","isi":"Isi catatan"}'

# Dengan API key -> 201
Invoke-RestMethod -Method Post -Uri http://localhost:8080/api/v1/catatan -Headers @{ "X-API-Key" = "rahasia123" } -ContentType "application/json" -Body '{"judul":"Tes","isi":"Isi catatan"}'
```

## Output yang Diharapkan
```
pesan
-----
pong
```
Log di terminal server:
```
2026/10/05 11:20:01 GET /ping -> 200 (150µs)
```

## Penjelasan
- `gin.New()` + `gin.Recovery()` = router kosong plus penyelamat dari `panic` (server tidak mati). `gin.Default()` sudah termasuk Logger dan Recovery.
- `binding:"required,min=3,max=100"` -> aturan validasi (dibahas di materi 05).
- `ShouldBindJSON` mengembalikan error, kita yang memutuskan responsenya. (`BindJSON` otomatis membalas 400 dan lebih kaku.)
- `gin.H` adalah alias `map[string]any` untuk JSON cepat.

## Kesalahan Umum
- Lupa `return` setelah `c.JSON(...)` pada kasus error.
- Group dengan middleware didefinisikan **setelah** route yang seharusnya terlindungi.
- Memakai `gin.Default()` di produksi tanpa `gin.SetMode(gin.ReleaseMode)`.

## Latihan
Tambahkan `PUT /api/v1/catatan/:id` dan `DELETE /api/v1/catatan/:id` (terlindungi API key).

## Catatan Instruktur
Estimasi 90 menit. Ajak siswa membandingkan dengan versi `net/http` di materi sebelumnya.
