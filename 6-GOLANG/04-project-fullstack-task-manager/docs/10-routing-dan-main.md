# 10 - Routing dan Main

## Tujuan Pembelajaran
- Menyusun route dengan group dan middleware.
- Merangkai semua komponen di `main.go`.
- Menjalankan server untuk pertama kali.

## Langkah

### 1. Routes
Buat `backend/internal/routes/routes.go`:

```go
// File: backend/internal/routes/routes.go
package routes

import (
	"net/http"
	"strings"
	"time"

	"github.com/gin-contrib/cors"
	"github.com/gin-gonic/gin"

	"taskmanager/internal/config"
	"taskmanager/internal/handlers"
	"taskmanager/internal/middleware"
	"taskmanager/internal/utils"
)

// Setup membuat router Gin lengkap dengan CORS dan semua endpoint.
func Setup(cfg *config.Config, auth *handlers.AuthHandler, tasks *handlers.TaskHandler) *gin.Engine {
	if cfg.AppEnv == "production" {
		gin.SetMode(gin.ReleaseMode)
	}
	r := gin.Default()

	r.Use(cors.New(cors.Config{
		AllowOrigins:     strings.Split(cfg.CORSOrigin, ","),
		AllowMethods:     []string{"GET", "POST", "PUT", "DELETE", "OPTIONS"},
		AllowHeaders:     []string{"Origin", "Content-Type", "Authorization"},
		AllowCredentials: true,
		MaxAge:           12 * time.Hour,
	}))

	api := r.Group("/api")
	{
		api.GET("/health", func(c *gin.Context) {
			utils.OK(c, http.StatusOK, "server berjalan", gin.H{"time": time.Now()})
		})

		authGroup := api.Group("/auth")
		{
			authGroup.POST("/register", auth.Register)
			authGroup.POST("/login", auth.Login)
			authGroup.GET("/me", middleware.Auth(cfg.JWTSecret), auth.Me)
		}

		taskGroup := api.Group("/tasks", middleware.Auth(cfg.JWTSecret))
		{
			taskGroup.GET("", tasks.List)
			taskGroup.POST("", tasks.Create)
			taskGroup.GET("/:id", tasks.Get)
			taskGroup.PUT("/:id", tasks.Update)
			taskGroup.DELETE("/:id", tasks.Delete)
		}
	}

	r.NoRoute(func(c *gin.Context) {
		utils.Fail(c, http.StatusNotFound, "endpoint tidak ditemukan")
	})
	return r
}
```

**Penjelasan**
- `r.Group("/api")` memberi prefix `/api` untuk semua route di dalamnya.
- `taskGroup := api.Group("/tasks", middleware.Auth(...))`: middleware auth dipasang **sekali** untuk seluruh group, jadi semua endpoint task otomatis terlindungi.
- `NoRoute` menangani URL yang tidak terdaftar dengan response JSON yang konsisten.
- Di mode `production` Gin dimatikan log debug-nya.

### 2. Main
Buat `backend/cmd/api/main.go`:

```go
// File: backend/cmd/api/main.go
package main

import (
	"log"

	"taskmanager/internal/config"
	"taskmanager/internal/database"
	"taskmanager/internal/handlers"
	"taskmanager/internal/repository"
	"taskmanager/internal/routes"
)

func main() {
	cfg, err := config.Load()
	if err != nil {
		log.Fatalf("config: %v", err)
	}

	db, err := database.NewMySQL(cfg.DSN())
	if err != nil {
		log.Fatalf("database: %v", err)
	}
	defer db.Close()
	log.Println("terhubung ke MySQL")

	userRepo := repository.NewUserRepository(db)
	taskRepo := repository.NewTaskRepository(db)

	authHandler := handlers.NewAuthHandler(userRepo, cfg.JWTSecret, cfg.JWTExpire)
	taskHandler := handlers.NewTaskHandler(taskRepo)

	router := routes.Setup(cfg, authHandler, taskHandler)

	log.Printf("server berjalan di http://localhost:%s", cfg.AppPort)
	if err := router.Run(":" + cfg.AppPort); err != nil {
		log.Fatalf("server: %v", err)
	}
}
```

**Penjelasan**
Urutan di `main`: **config -> database -> repository -> handler -> router -> run**. Ini disebut *composition root*: satu-satunya tempat objek dirangkai.

### 3. Rapikan dependency lalu jalankan
Pastikan MySQL menyala dan `schema.sql` sudah dijalankan.

```powershell
cd backend
go mod tidy
go run ./cmd/api
```

## Output yang Diharapkan
```
terhubung ke MySQL
[GIN-debug] Listening and serving HTTP on :8080
server berjalan di http://localhost:8080
```

Buka `http://localhost:8080/api/health` di browser:
```json
{"success":true,"message":"server berjalan","data":{"time":"2026-..."}}
```

## Opsional: Hot Reload dengan Air
Supaya server restart otomatis saat file berubah:
```powershell
go install github.com/air-verse/air@latest
air
```
File konfigurasi `backend/.air.toml`:

```toml
# File: backend/.air.toml
root = "."
tmp_dir = "tmp"

[build]
  cmd = "go build -o ./tmp/main.exe ./cmd/api"
  bin = "tmp/main.exe"
  include_ext = ["go"]
  exclude_dir = ["tmp"]
  delay = 500
```

## Kesalahan Umum
| Error | Solusi |
|---|---|
| `bind: Only one usage of each socket address` | Port 8080 dipakai aplikasi lain. Ubah `APP_PORT` |
| `JWT_SECRET wajib diisi` | `.env` belum dibuat / tidak berada di folder `backend` saat `go run` |
| `gagal ping database` | MySQL belum menyala |
| `import cycle` | Paket saling mengimpor. Periksa arah dependency |

> **Catatan:** jalankan `go run` dari folder `backend` agar file `.env` terbaca.

## Catatan Instruktur
Estimasi 45 menit. Ini momen "server pertama hidup", rayakan. Minta siswa menjelaskan arti tiap baris log.
