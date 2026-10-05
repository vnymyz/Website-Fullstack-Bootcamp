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
