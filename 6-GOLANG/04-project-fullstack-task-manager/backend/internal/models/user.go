package models

import "time"

// User merepresentasikan satu baris pada tabel users.
type User struct {
	ID           int64     `json:"id"`
	Name         string    `json:"name"`
	Email        string    `json:"email"`
	PasswordHash string    `json:"-"` // tanda "-" = tidak pernah ikut di JSON
	CreatedAt    time.Time `json:"created_at"`
}

// RegisterRequest adalah body JSON untuk pendaftaran.
type RegisterRequest struct {
	Name     string `json:"name" binding:"required,min=2,max=100"`
	Email    string `json:"email" binding:"required,email,max=150"`
	Password string `json:"password" binding:"required,min=6,max=72"`
}

// LoginRequest adalah body JSON untuk login.
type LoginRequest struct {
	Email    string `json:"email" binding:"required,email"`
	Password string `json:"password" binding:"required"`
}

// AuthResponse dikirim setelah register/login berhasil.
type AuthResponse struct {
	Token string `json:"token"`
	User  *User  `json:"user"`
}
