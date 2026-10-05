package repository

import (
	"context"
	"database/sql"
	"errors"

	"github.com/go-sql-driver/mysql"

	"taskmanager/internal/models"
)

// Error domain yang dipakai handler.
var (
	ErrNotFound   = errors.New("data tidak ditemukan")
	ErrEmailTaken = errors.New("email sudah terdaftar")
)

// Kode error MySQL untuk duplicate entry pada kolom UNIQUE.
const errMySQLDuplicate uint16 = 1062

// UserRepository mengelola akses tabel users.
type UserRepository struct {
	db *sql.DB
}

func NewUserRepository(db *sql.DB) *UserRepository {
	return &UserRepository{db: db}
}

// Create menyimpan user baru dan mengisi ID serta CreatedAt-nya.
func (r *UserRepository) Create(ctx context.Context, u *models.User) error {
	const q = `INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)`
	res, err := r.db.ExecContext(ctx, q, u.Name, u.Email, u.PasswordHash)
	if err != nil {
		var me *mysql.MySQLError
		if errors.As(err, &me) && me.Number == errMySQLDuplicate {
			return ErrEmailTaken
		}
		return err
	}
	id, err := res.LastInsertId()
	if err != nil {
		return err
	}
	u.ID = id
	return r.db.QueryRowContext(ctx, `SELECT created_at FROM users WHERE id = ?`, id).Scan(&u.CreatedAt)
}

// FindByEmail mencari user berdasarkan email.
func (r *UserRepository) FindByEmail(ctx context.Context, email string) (*models.User, error) {
	const q = `SELECT id, name, email, password_hash, created_at FROM users WHERE email = ?`
	var u models.User
	err := r.db.QueryRowContext(ctx, q, email).
		Scan(&u.ID, &u.Name, &u.Email, &u.PasswordHash, &u.CreatedAt)
	if errors.Is(err, sql.ErrNoRows) {
		return nil, ErrNotFound
	}
	if err != nil {
		return nil, err
	}
	return &u, nil
}

// FindByID mencari user berdasarkan ID.
func (r *UserRepository) FindByID(ctx context.Context, id int64) (*models.User, error) {
	const q = `SELECT id, name, email, password_hash, created_at FROM users WHERE id = ?`
	var u models.User
	err := r.db.QueryRowContext(ctx, q, id).
		Scan(&u.ID, &u.Name, &u.Email, &u.PasswordHash, &u.CreatedAt)
	if errors.Is(err, sql.ErrNoRows) {
		return nil, ErrNotFound
	}
	if err != nil {
		return nil, err
	}
	return &u, nil
}
