package config

import (
	"fmt"
	"os"
	"strconv"
	"time"

	"github.com/joho/godotenv"
)

// Config menyimpan semua pengaturan aplikasi yang dibaca dari environment.
type Config struct {
	AppPort    string
	AppEnv     string
	DBHost     string
	DBPort     string
	DBUser     string
	DBPass     string
	DBName     string
	JWTSecret  string
	JWTExpire  time.Duration
	CORSOrigin string
}

// Load membaca file .env (jika ada) lalu mengisi struct Config.
func Load() (*Config, error) {
	// Abaikan error: di server produksi biasanya env diset langsung, bukan lewat file.
	_ = godotenv.Load()

	expireHours, err := strconv.Atoi(getEnv("JWT_EXPIRE_HOURS", "24"))
	if err != nil {
		return nil, fmt.Errorf("JWT_EXPIRE_HOURS harus berupa angka: %w", err)
	}

	cfg := &Config{
		AppPort:    getEnv("APP_PORT", "8080"),
		AppEnv:     getEnv("APP_ENV", "development"),
		DBHost:     getEnv("DB_HOST", "127.0.0.1"),
		DBPort:     getEnv("DB_PORT", "3306"),
		DBUser:     getEnv("DB_USER", "root"),
		DBPass:     getEnv("DB_PASS", ""),
		DBName:     getEnv("DB_NAME", "task_manager"),
		JWTSecret:  getEnv("JWT_SECRET", ""),
		JWTExpire:  time.Duration(expireHours) * time.Hour,
		CORSOrigin: getEnv("CORS_ORIGIN", "http://localhost:5173"),
	}

	if cfg.JWTSecret == "" {
		return nil, fmt.Errorf("JWT_SECRET wajib diisi")
	}
	return cfg, nil
}

// DSN membentuk Data Source Name untuk driver MySQL.
func (c *Config) DSN() string {
	return fmt.Sprintf("%s:%s@tcp(%s:%s)/%s?parseTime=true&charset=utf8mb4&loc=Local",
		c.DBUser, c.DBPass, c.DBHost, c.DBPort, c.DBName)
}

func getEnv(key, fallback string) string {
	if v, ok := os.LookupEnv(key); ok && v != "" {
		return v
	}
	return fallback
}
