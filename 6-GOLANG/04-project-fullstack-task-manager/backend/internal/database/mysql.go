package database

import (
	"context"
	"database/sql"
	"fmt"
	"time"

	_ "github.com/go-sql-driver/mysql" // registrasi driver MySQL
)

// NewMySQL membuka koneksi pool ke MySQL dan memastikan database bisa dihubungi.
func NewMySQL(dsn string) (*sql.DB, error) {
	db, err := sql.Open("mysql", dsn)
	if err != nil {
		return nil, fmt.Errorf("gagal membuka koneksi: %w", err)
	}

	// Pengaturan connection pool.
	db.SetMaxOpenConns(25)
	db.SetMaxIdleConns(10)
	db.SetConnMaxLifetime(5 * time.Minute)

	// sql.Open belum benar-benar konek, jadi Ping untuk memastikan.
	ctx, cancel := context.WithTimeout(context.Background(), 5*time.Second)
	defer cancel()
	if err := db.PingContext(ctx); err != nil {
		db.Close()
		return nil, fmt.Errorf("gagal ping database (apakah MySQL sudah menyala?): %w", err)
	}
	return db, nil
}
