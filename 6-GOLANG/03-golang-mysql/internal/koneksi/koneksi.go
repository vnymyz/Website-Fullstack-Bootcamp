// Package koneksi berisi helper agar semua contoh memakai cara koneksi yang sama.
package koneksi

import (
	"context"
	"database/sql"
	"fmt"
	"os"
	"time"

	"github.com/joho/godotenv"

	_ "github.com/go-sql-driver/mysql" // mendaftarkan driver "mysql"
)

func env(key, fallback string) string {
	if v := os.Getenv(key); v != "" {
		return v
	}
	return fallback
}

// DSN membangun connection string dari environment (.env).
func DSN() string {
	_ = godotenv.Load() // abaikan error jika .env tidak ada
	return fmt.Sprintf("%s:%s@tcp(%s:%s)/%s?parseTime=true&charset=utf8mb4&loc=Local",
		env("DB_USER", "root"),
		env("DB_PASS", ""),
		env("DB_HOST", "127.0.0.1"),
		env("DB_PORT", "3306"),
		env("DB_NAME", "belajar_go"),
	)
}

// Buka membuka koneksi pool dan memastikan database dapat dihubungi.
func Buka() (*sql.DB, error) {
	db, err := sql.Open("mysql", DSN())
	if err != nil {
		return nil, err
	}
	db.SetMaxOpenConns(10)
	db.SetMaxIdleConns(5)
	db.SetConnMaxLifetime(5 * time.Minute)

	ctx, cancel := context.WithTimeout(context.Background(), 5*time.Second)
	defer cancel()
	if err := db.PingContext(ctx); err != nil {
		db.Close()
		return nil, fmt.Errorf("tidak bisa terhubung ke MySQL: %w", err)
	}
	return db, nil
}
