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
