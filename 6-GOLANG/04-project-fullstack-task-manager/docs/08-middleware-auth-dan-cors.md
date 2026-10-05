# 08 - Middleware Auth dan CORS

## Tujuan Pembelajaran
- Memahami middleware di Gin.
- Membuat middleware yang memverifikasi JWT.
- Memahami dan mengatur CORS.

## Konsep Middleware
Middleware adalah fungsi yang berjalan **sebelum** handler. Ia bisa memeriksa request, lalu:
- `c.Next()` -> lanjut ke handler berikutnya, atau
- `c.Abort()` -> hentikan dan balas error.

```
Request -> [CORS] -> [Auth middleware] -> Handler -> Response
```

## Langkah

### 1. Middleware Auth
Buat `backend/internal/middleware/auth.go`:

```go
// File: backend/internal/middleware/auth.go
package middleware

import (
	"net/http"
	"strings"

	"github.com/gin-gonic/gin"

	"taskmanager/internal/utils"
)

// ContextUserID adalah kunci untuk menyimpan ID user di gin.Context.
const ContextUserID = "userID"

// Auth memverifikasi header "Authorization: Bearer <token>".
func Auth(secret string) gin.HandlerFunc {
	return func(c *gin.Context) {
		header := c.GetHeader("Authorization")
		parts := strings.SplitN(header, " ", 2)
		if len(parts) != 2 || !strings.EqualFold(parts[0], "Bearer") {
			utils.Fail(c, http.StatusUnauthorized, "token tidak ditemukan")
			c.Abort()
			return
		}

		claims, err := utils.ParseToken(parts[1], secret)
		if err != nil {
			utils.Fail(c, http.StatusUnauthorized, "token tidak valid atau kedaluwarsa")
			c.Abort()
			return
		}

		c.Set(ContextUserID, claims.UserID)
		c.Next()
	}
}

// UserID mengambil ID user yang sudah diset oleh middleware Auth.
func UserID(c *gin.Context) int64 {
	return c.GetInt64(ContextUserID)
}
```

**Penjelasan**
- `Auth(secret)` mengembalikan `gin.HandlerFunc` (closure yang menyimpan `secret`).
- Header yang diharapkan: `Authorization: Bearer eyJhbGciOi...`.
- Jika valid, `c.Set("userID", ...)` menyimpan ID untuk dipakai handler. Handler mengambilnya lewat `middleware.UserID(c)`.
- **User ID tidak pernah diambil dari body/URL** untuk urusan kepemilikan; selalu dari token. Ini mencegah user memalsukan identitas.

### 2. CORS
CORS (*Cross-Origin Resource Sharing*) adalah aturan browser. Frontend di `localhost:5173` memanggil backend di `localhost:8080` = **origin berbeda**, sehingga browser memblokir kecuali backend mengizinkan lewat header khusus.

CORS dipasang di `routes.go` (langkah 10) memakai `gin-contrib/cors`:
```go
r.Use(cors.New(cors.Config{
    AllowOrigins:     strings.Split(cfg.CORSOrigin, ","),
    AllowMethods:     []string{"GET", "POST", "PUT", "DELETE", "OPTIONS"},
    AllowHeaders:     []string{"Origin", "Content-Type", "Authorization"},
    AllowCredentials: true,
    MaxAge:           12 * time.Hour,
}))
```
- Browser mengirim request **preflight** (`OPTIONS`) sebelum request "berbahaya" (misalnya yang membawa header `Authorization`). Library CORS menjawabnya otomatis.
- Jangan memakai `AllowOrigins: ["*"]` di produksi bersama credentials.
- Pada project ini frontend juga memakai **proxy Vite**, sehingga di mode dev browser merasa satu origin dan CORS tidak terlalu terasa. Tetap kita pasang agar siap untuk produksi.

## Kesalahan Umum
- `Access to XMLHttpRequest ... blocked by CORS policy` -> `CORS_ORIGIN` di `.env` tidak sama dengan alamat frontend (termasuk port).
- Lupa mengizinkan header `Authorization` di `AllowHeaders`.

## Latihan
Tulis middleware `Logger` sederhana yang mencetak method, path, status, dan durasi tiap request memakai `time.Since`.

## Catatan Instruktur
Estimasi 45 menit. Demo CORS: matikan middleware CORS dan akses dari browser tanpa proxy untuk memperlihatkan errornya.
