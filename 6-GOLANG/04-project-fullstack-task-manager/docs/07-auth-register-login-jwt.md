# 07 - Auth: Register, Login, dan Me

## Tujuan Pembelajaran
- Membuat handler Gin untuk register dan login.
- Mengikat JSON request ke struct dan memvalidasinya.
- Mengembalikan token JWT.

## Langkah
Buat `backend/internal/handlers/auth_handler.go`:

```go
// File: backend/internal/handlers/auth_handler.go
package handlers

import (
	"errors"
	"net/http"
	"strings"
	"time"

	"github.com/gin-gonic/gin"

	"taskmanager/internal/middleware"
	"taskmanager/internal/models"
	"taskmanager/internal/repository"
	"taskmanager/internal/utils"
)

// AuthHandler menangani register, login, dan profil.
type AuthHandler struct {
	users     *repository.UserRepository
	jwtSecret string
	jwtExpire time.Duration
}

func NewAuthHandler(users *repository.UserRepository, jwtSecret string, jwtExpire time.Duration) *AuthHandler {
	return &AuthHandler{users: users, jwtSecret: jwtSecret, jwtExpire: jwtExpire}
}

// Register: POST /api/auth/register
func (h *AuthHandler) Register(c *gin.Context) {
	var req models.RegisterRequest
	if err := c.ShouldBindJSON(&req); err != nil {
		utils.Fail(c, http.StatusBadRequest, "data tidak valid: "+err.Error())
		return
	}

	hash, err := utils.HashPassword(req.Password)
	if err != nil {
		utils.Fail(c, http.StatusInternalServerError, "gagal memproses password")
		return
	}

	user := &models.User{
		Name:         strings.TrimSpace(req.Name),
		Email:        strings.ToLower(strings.TrimSpace(req.Email)),
		PasswordHash: hash,
	}
	if err := h.users.Create(c.Request.Context(), user); err != nil {
		if errors.Is(err, repository.ErrEmailTaken) {
			utils.Fail(c, http.StatusConflict, "email sudah terdaftar")
			return
		}
		utils.Fail(c, http.StatusInternalServerError, "gagal menyimpan user")
		return
	}

	h.respondWithToken(c, http.StatusCreated, "registrasi berhasil", user)
}

// Login: POST /api/auth/login
func (h *AuthHandler) Login(c *gin.Context) {
	var req models.LoginRequest
	if err := c.ShouldBindJSON(&req); err != nil {
		utils.Fail(c, http.StatusBadRequest, "data tidak valid: "+err.Error())
		return
	}

	email := strings.ToLower(strings.TrimSpace(req.Email))
	user, err := h.users.FindByEmail(c.Request.Context(), email)
	// Pesan sengaja sama untuk email salah maupun password salah (mencegah user enumeration).
	if err != nil || !utils.CheckPassword(user.PasswordHash, req.Password) {
		if err != nil && !errors.Is(err, repository.ErrNotFound) {
			utils.Fail(c, http.StatusInternalServerError, "terjadi kesalahan server")
			return
		}
		utils.Fail(c, http.StatusUnauthorized, "email atau password salah")
		return
	}

	h.respondWithToken(c, http.StatusOK, "login berhasil", user)
}

// Me: GET /api/auth/me (butuh token)
func (h *AuthHandler) Me(c *gin.Context) {
	user, err := h.users.FindByID(c.Request.Context(), middleware.UserID(c))
	if err != nil {
		if errors.Is(err, repository.ErrNotFound) {
			utils.Fail(c, http.StatusUnauthorized, "user tidak ditemukan")
			return
		}
		utils.Fail(c, http.StatusInternalServerError, "terjadi kesalahan server")
		return
	}
	utils.OK(c, http.StatusOK, "berhasil", user)
}

func (h *AuthHandler) respondWithToken(c *gin.Context, status int, message string, user *models.User) {
	token, err := utils.GenerateToken(user.ID, h.jwtSecret, h.jwtExpire)
	if err != nil {
		utils.Fail(c, http.StatusInternalServerError, "gagal membuat token")
		return
	}
	utils.OK(c, status, message, models.AuthResponse{Token: token, User: user})
}
```

## Penjelasan

### Struktur handler
`AuthHandler` menyimpan dependency (`users`, `jwtSecret`, `jwtExpire`). Ini disebut **dependency injection** sederhana: dependency diberikan lewat constructor `NewAuthHandler`, bukan dibuat di dalam handler. Hasilnya mudah dites.

### Register
1. `c.ShouldBindJSON(&req)` membaca body JSON dan menjalankan validasi tag `binding`.
2. Password di-hash dengan bcrypt.
3. Email dinormalisasi (`ToLower`, `TrimSpace`) agar `A@x.com` dan `a@x.com` dianggap sama.
4. `repository.ErrEmailTaken` dipetakan ke HTTP **409 Conflict**.
5. Sukses -> HTTP **201 Created** + token.

### Login
- Email salah dan password salah menghasilkan **pesan yang sama**. Ini mencegah penyerang mengetahui email mana yang terdaftar (*user enumeration*).
- Status **401 Unauthorized** untuk kredensial salah.

### Me
Mengambil `userID` dari context (diisi oleh middleware di langkah berikutnya), lalu mengambil data user.

### Kode status HTTP yang dipakai
| Kode | Arti | Dipakai saat |
|---|---|---|
| 200 | OK | login sukses, get data |
| 201 | Created | register / buat task sukses |
| 400 | Bad Request | input tidak valid |
| 401 | Unauthorized | token/kredensial salah |
| 404 | Not Found | data tidak ada |
| 409 | Conflict | email duplikat |
| 500 | Internal Server Error | kesalahan tak terduga |

## Cek Hasil
Handler ini butuh `middleware` (untuk `Me`). Kita compile bersama di langkah 08.

## Kesalahan Umum
- Mengembalikan `err.Error()` dari database langsung ke client. Bocor detail internal. Kita hanya mengirim pesan umum untuk error 500.
- Lupa `return` setelah mengirim response -> handler lanjut dan mengirim response dua kali.

## Latihan
Tambahkan validasi bahwa password harus mengandung minimal satu angka (tulis fungsi bantu di `utils`).

## Catatan Instruktur
Estimasi 60 menit. Diskusikan 401 vs 403 dan alasan pesan error login yang sengaja umum.
