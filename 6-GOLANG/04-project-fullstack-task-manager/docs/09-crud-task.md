# 09 - CRUD Task

## Tujuan Pembelajaran
- Membuat handler CRUD lengkap (Create, Read, Update, Delete).
- Membaca query string (`?status=&page=&limit=`) dan path param (`/:id`).
- Membuat pagination sederhana.

## Langkah
Buat `backend/internal/handlers/task_handler.go`:

```go
// File: backend/internal/handlers/task_handler.go
package handlers

import (
	"errors"
	"net/http"
	"strconv"
	"strings"
	"time"

	"github.com/gin-gonic/gin"

	"taskmanager/internal/middleware"
	"taskmanager/internal/models"
	"taskmanager/internal/repository"
	"taskmanager/internal/utils"
)

// TaskHandler menangani CRUD task.
type TaskHandler struct {
	tasks *repository.TaskRepository
}

func NewTaskHandler(tasks *repository.TaskRepository) *TaskHandler {
	return &TaskHandler{tasks: tasks}
}

// List: GET /api/tasks?status=todo&page=1&limit=10
func (h *TaskHandler) List(c *gin.Context) {
	status := c.Query("status")
	if status != "" && !validStatus(status) {
		utils.Fail(c, http.StatusBadRequest, "status harus todo, in_progress, atau done")
		return
	}
	page := atoiDefault(c.Query("page"), 1)
	limit := atoiDefault(c.Query("limit"), 10)
	if page < 1 {
		page = 1
	}
	if limit < 1 || limit > 100 {
		limit = 10
	}

	result, err := h.tasks.List(c.Request.Context(), middleware.UserID(c), status, page, limit)
	if err != nil {
		utils.Fail(c, http.StatusInternalServerError, "gagal mengambil data task")
		return
	}
	utils.OK(c, http.StatusOK, "berhasil", result)
}

// Get: GET /api/tasks/:id
func (h *TaskHandler) Get(c *gin.Context) {
	id, ok := parseID(c)
	if !ok {
		return
	}
	task, err := h.tasks.FindByID(c.Request.Context(), id, middleware.UserID(c))
	if err != nil {
		h.handleRepoError(c, err)
		return
	}
	utils.OK(c, http.StatusOK, "berhasil", task)
}

// Create: POST /api/tasks
func (h *TaskHandler) Create(c *gin.Context) {
	var req models.TaskRequest
	if err := c.ShouldBindJSON(&req); err != nil {
		utils.Fail(c, http.StatusBadRequest, "data tidak valid: "+err.Error())
		return
	}
	due, err := parseDueDate(req.DueDate)
	if err != nil {
		utils.Fail(c, http.StatusBadRequest, "due_date harus berformat YYYY-MM-DD")
		return
	}
	status := req.Status
	if status == "" {
		status = models.StatusTodo
	}

	task := &models.Task{
		UserID:      middleware.UserID(c),
		Title:       strings.TrimSpace(req.Title),
		Description: req.Description,
		Status:      status,
		DueDate:     due,
	}
	if err := h.tasks.Create(c.Request.Context(), task); err != nil {
		utils.Fail(c, http.StatusInternalServerError, "gagal membuat task")
		return
	}
	utils.OK(c, http.StatusCreated, "task berhasil dibuat", task)
}

// Update: PUT /api/tasks/:id
func (h *TaskHandler) Update(c *gin.Context) {
	id, ok := parseID(c)
	if !ok {
		return
	}
	var req models.TaskRequest
	if err := c.ShouldBindJSON(&req); err != nil {
		utils.Fail(c, http.StatusBadRequest, "data tidak valid: "+err.Error())
		return
	}
	due, err := parseDueDate(req.DueDate)
	if err != nil {
		utils.Fail(c, http.StatusBadRequest, "due_date harus berformat YYYY-MM-DD")
		return
	}

	userID := middleware.UserID(c)
	task, err := h.tasks.FindByID(c.Request.Context(), id, userID)
	if err != nil {
		h.handleRepoError(c, err)
		return
	}

	task.Title = strings.TrimSpace(req.Title)
	task.Description = req.Description
	if req.Status != "" {
		task.Status = req.Status
	}
	task.DueDate = due

	if err := h.tasks.Update(c.Request.Context(), task); err != nil {
		h.handleRepoError(c, err)
		return
	}
	utils.OK(c, http.StatusOK, "task berhasil diubah", task)
}

// Delete: DELETE /api/tasks/:id
func (h *TaskHandler) Delete(c *gin.Context) {
	id, ok := parseID(c)
	if !ok {
		return
	}
	if err := h.tasks.Delete(c.Request.Context(), id, middleware.UserID(c)); err != nil {
		h.handleRepoError(c, err)
		return
	}
	utils.OK(c, http.StatusOK, "task berhasil dihapus", nil)
}

func (h *TaskHandler) handleRepoError(c *gin.Context, err error) {
	if errors.Is(err, repository.ErrNotFound) {
		utils.Fail(c, http.StatusNotFound, "task tidak ditemukan")
		return
	}
	utils.Fail(c, http.StatusInternalServerError, "terjadi kesalahan server")
}

func parseID(c *gin.Context) (int64, bool) {
	id, err := strconv.ParseInt(c.Param("id"), 10, 64)
	if err != nil || id < 1 {
		utils.Fail(c, http.StatusBadRequest, "id tidak valid")
		return 0, false
	}
	return id, true
}

func parseDueDate(s string) (*time.Time, error) {
	if s == "" {
		return nil, nil
	}
	t, err := time.Parse("2006-01-02", s)
	if err != nil {
		return nil, err
	}
	return &t, nil
}

func validStatus(s string) bool {
	return s == models.StatusTodo || s == models.StatusInProgress || s == models.StatusDone
}

func atoiDefault(s string, def int) int {
	n, err := strconv.Atoi(s)
	if err != nil {
		return def
	}
	return n
}
```

## Penjelasan

### Path param dan query string
| Bagian | Cara membaca | Contoh |
|---|---|---|
| Path param | `c.Param("id")` | `/api/tasks/5` |
| Query string | `c.Query("status")` | `/api/tasks?status=done&page=2` |
| Body JSON | `c.ShouldBindJSON(&req)` | `POST` / `PUT` |

### Pagination
- `OFFSET = (page - 1) * limit`.
- `limit` dibatasi maksimal 100 agar client tidak meminta jutaan baris.
- Response berisi `total` supaya frontend bisa menghitung jumlah halaman.

### Kepemilikan data
Semua operasi memakai `middleware.UserID(c)`. Jika user mencoba mengakses task milik orang lain, repository mengembalikan `ErrNotFound` dan client menerima **404** (bukan 403, agar tidak membocorkan bahwa task tersebut ada).

### Update
Kita ambil task dulu (`FindByID`) untuk memastikan miliknya dan ada, ubah field, baru simpan. Sengaja memakai pola ini agar mudah dipahami.

### Tanggal
`due_date` dikirim sebagai string `YYYY-MM-DD`. Layout Go untuk format itu adalah `"2006-01-02"` (tanggal referensi Go; ini bukan typo).

## Cek Hasil
```powershell
go build ./internal/...
```

## Kesalahan Umum
- `strconv.Atoi` pada query kosong menghasilkan error -> kita memakai `atoiDefault` dengan nilai default.
- Menulis `time.Parse("YYYY-MM-DD", ...)`: salah. Go memakai layout `2006-01-02`.

## Latihan
1. Tambahkan pencarian `?q=kata` yang memfilter `title` dengan `LIKE ?` (ingat: parameter `"%" + q + "%"` tetap lewat placeholder).
2. Tambahkan endpoint `PATCH /api/tasks/:id/status` yang hanya mengubah status.

## Catatan Instruktur
Estimasi 75 menit. Pecah menjadi dua sesi: Create+List dulu, lalu Get+Update+Delete.
