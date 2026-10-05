# 05 - Models dan Repository

## Tujuan Pembelajaran
- Membuat struct yang merepresentasikan tabel dan request JSON.
- Memakai struct tag `json` dan `binding` (validasi).
- Menulis query SQL di lapisan repository dengan `database/sql`.

## Konsep
- **Model**: struct Go yang bentuknya mirip tabel / body JSON.
- **Repository**: satu-satunya tempat yang boleh menulis SQL. Handler tidak tahu SQL; ia hanya memanggil method repository.

## Langkah

### 1. Model User
Buat `backend/internal/models/user.go`:

```go
// File: backend/internal/models/user.go
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
```

**Penjelasan**
- `` `json:"-"` `` pada `PasswordHash`: field ini **tidak pernah** muncul di response JSON.
- `binding:"required,email"`: validasi otomatis oleh Gin (library validator). Jika gagal, `ShouldBindJSON` mengembalikan error.
- `max=72` pada password: bcrypt hanya memproses 72 byte pertama.

### 2. Model Task
Buat `backend/internal/models/task.go`:

```go
// File: backend/internal/models/task.go
package models

import "time"

// Status task yang valid.
const (
	StatusTodo       = "todo"
	StatusInProgress = "in_progress"
	StatusDone       = "done"
)

// Task merepresentasikan satu baris pada tabel tasks.
type Task struct {
	ID          int64      `json:"id"`
	UserID      int64      `json:"user_id"`
	Title       string     `json:"title"`
	Description string     `json:"description"`
	Status      string     `json:"status"`
	DueDate     *time.Time `json:"due_date"` // pointer: bisa null
	CreatedAt   time.Time  `json:"created_at"`
	UpdatedAt   time.Time  `json:"updated_at"`
}

// TaskRequest dipakai untuk membuat dan mengubah task.
// DueDate berformat "2006-01-02" (YYYY-MM-DD) dan boleh kosong.
type TaskRequest struct {
	Title       string `json:"title" binding:"required,min=1,max=200"`
	Description string `json:"description" binding:"max=2000"`
	Status      string `json:"status" binding:"omitempty,oneof=todo in_progress done"`
	DueDate     string `json:"due_date"`
}

// TaskListResult adalah hasil pencarian task beserta info pagination.
type TaskListResult struct {
	Items []Task `json:"items"`
	Page  int    `json:"page"`
	Limit int    `json:"limit"`
	Total int    `json:"total"`
}
```

**Penjelasan**
- `DueDate *time.Time`: pointer agar bisa bernilai `nil` (mewakili `NULL` di database / `null` di JSON).
- `oneof=todo in_progress done`: hanya tiga nilai itu yang diterima.
- `omitempty` pada status: boleh dikosongkan (default `todo`).

### 3. Repository User
Buat `backend/internal/repository/user_repository.go`:

```go
// File: backend/internal/repository/user_repository.go
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
```

**Penjelasan**
- Placeholder `?` mencegah **SQL injection**. Jangan pernah menyambung string SQL dengan input user.
- `errors.As(err, &me)` mengecek apakah error berasal dari MySQL dengan kode **1062** (duplicate entry), lalu kita ubah menjadi `ErrEmailTaken`.
- `sql.ErrNoRows` diubah menjadi `ErrNotFound` agar lapisan atas tidak perlu tahu detail `database/sql`.
- Semua method menerima `context.Context` supaya query bisa dibatalkan jika request client terputus.

### 4. Repository Task
Buat `backend/internal/repository/task_repository.go`:

```go
// File: backend/internal/repository/task_repository.go
package repository

import (
	"context"
	"database/sql"
	"errors"

	"taskmanager/internal/models"
)

// TaskRepository mengelola akses tabel tasks.
// Semua query selalu memfilter user_id agar user tidak bisa menyentuh task milik orang lain.
type TaskRepository struct {
	db *sql.DB
}

func NewTaskRepository(db *sql.DB) *TaskRepository {
	return &TaskRepository{db: db}
}

const taskColumns = `id, user_id, title, COALESCE(description, ''), status, due_date, created_at, updated_at`

func scanTask(row interface{ Scan(dest ...any) error }, t *models.Task) error {
	return row.Scan(&t.ID, &t.UserID, &t.Title, &t.Description, &t.Status, &t.DueDate, &t.CreatedAt, &t.UpdatedAt)
}

// Create menyimpan task baru lalu memuat ulang datanya dari database.
func (r *TaskRepository) Create(ctx context.Context, t *models.Task) error {
	const q = `INSERT INTO tasks (user_id, title, description, status, due_date) VALUES (?, ?, ?, ?, ?)`
	res, err := r.db.ExecContext(ctx, q, t.UserID, t.Title, t.Description, t.Status, t.DueDate)
	if err != nil {
		return err
	}
	id, err := res.LastInsertId()
	if err != nil {
		return err
	}
	got, err := r.FindByID(ctx, id, t.UserID)
	if err != nil {
		return err
	}
	*t = *got
	return nil
}

// List mengambil task milik user dengan filter status (opsional) dan pagination.
func (r *TaskRepository) List(ctx context.Context, userID int64, status string, page, limit int) (*models.TaskListResult, error) {
	where := `WHERE user_id = ?`
	args := []any{userID}
	if status != "" {
		where += ` AND status = ?`
		args = append(args, status)
	}

	var total int
	if err := r.db.QueryRowContext(ctx, `SELECT COUNT(*) FROM tasks `+where, args...).Scan(&total); err != nil {
		return nil, err
	}

	offset := (page - 1) * limit
	listArgs := append(append([]any{}, args...), limit, offset)
	rows, err := r.db.QueryContext(ctx,
		`SELECT `+taskColumns+` FROM tasks `+where+` ORDER BY created_at DESC, id DESC LIMIT ? OFFSET ?`,
		listArgs...)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	items := make([]models.Task, 0)
	for rows.Next() {
		var t models.Task
		if err := scanTask(rows, &t); err != nil {
			return nil, err
		}
		items = append(items, t)
	}
	if err := rows.Err(); err != nil {
		return nil, err
	}
	return &models.TaskListResult{Items: items, Page: page, Limit: limit, Total: total}, nil
}

// FindByID mengambil satu task milik user.
func (r *TaskRepository) FindByID(ctx context.Context, id, userID int64) (*models.Task, error) {
	q := `SELECT ` + taskColumns + ` FROM tasks WHERE id = ? AND user_id = ?`
	var t models.Task
	err := scanTask(r.db.QueryRowContext(ctx, q, id, userID), &t)
	if errors.Is(err, sql.ErrNoRows) {
		return nil, ErrNotFound
	}
	if err != nil {
		return nil, err
	}
	return &t, nil
}

// Update mengubah task milik user.
func (r *TaskRepository) Update(ctx context.Context, t *models.Task) error {
	const q = `UPDATE tasks SET title = ?, description = ?, status = ?, due_date = ? WHERE id = ? AND user_id = ?`
	res, err := r.db.ExecContext(ctx, q, t.Title, t.Description, t.Status, t.DueDate, t.ID, t.UserID)
	if err != nil {
		return err
	}
	// Catatan: MySQL mengembalikan 0 jika nilai tidak berubah, jadi cek keberadaan lewat FindByID.
	_ = res
	got, err := r.FindByID(ctx, t.ID, t.UserID)
	if err != nil {
		return err
	}
	*t = *got
	return nil
}

// Delete menghapus task milik user.
func (r *TaskRepository) Delete(ctx context.Context, id, userID int64) error {
	res, err := r.db.ExecContext(ctx, `DELETE FROM tasks WHERE id = ? AND user_id = ?`, id, userID)
	if err != nil {
		return err
	}
	n, err := res.RowsAffected()
	if err != nil {
		return err
	}
	if n == 0 {
		return ErrNotFound
	}
	return nil
}
```

**Penjelasan**
- Setiap query memakai `WHERE ... AND user_id = ?` agar user **tidak bisa** membaca/mengubah task orang lain.
- `COALESCE(description, '')` mengubah `NULL` menjadi string kosong supaya `Scan` ke `string` tidak error.
- `scanTask` menerima `*sql.Row` maupun `*sql.Rows` karena keduanya punya method `Scan`.
- `List` membangun klausa `WHERE` dinamis, tetap dengan placeholder. Nilai `status` tidak pernah disambung ke string SQL.
- `defer rows.Close()` + `rows.Err()` wajib untuk query berbaris banyak.
- `Delete` memakai `RowsAffected()`: 0 baris berarti task tidak ada (atau bukan milik user).

## Cek Hasil
```powershell
go build ./internal/models ./internal/repository
```

## Kesalahan Umum
- `Scan error: converting NULL to string is unsupported` -> lupa `COALESCE` atau tipe pointer.
- `unsupported Scan, storing driver.Value type []uint8 into type *time.Time` -> lupa `parseTime=true` di DSN.

## Latihan
Tambahkan method `CountByUser(ctx, userID)` pada `TaskRepository` yang mengembalikan jumlah task milik user.

## Catatan Instruktur
Estimasi 60 menit. Poin penting: pointer untuk nilai nullable, dan alasan memisahkan repository dari handler.
