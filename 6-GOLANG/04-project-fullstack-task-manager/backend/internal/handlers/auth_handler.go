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
