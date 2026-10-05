# 09 - Testing Handler dengan testify dan httptest

## Tujuan Pembelajaran
- Menguji handler HTTP tanpa menjalankan server sungguhan.
- Memakai `assert` dan `require` dari `testify`.
- Menulis table-driven test.

## Konsep
- **`net/http/httptest`** (standar): membuat request palsu (`NewRequest`) dan penampung response (`NewRecorder`). Router dipanggil langsung lewat `ServeHTTP`.
- **`testify`**: kumpulan assertion yang lebih ringkas dibanding `if got != want { t.Errorf(...) }`.

| Paket | Perilaku saat gagal |
|---|---|
| `assert` | Mencatat kegagalan, **lanjut** |
| `require` | Mencatat kegagalan, **berhenti** (`t.FailNow`) |

Gunakan `require` untuk prasyarat (mis. `NoError` sebelum memakai hasil), `assert` untuk pemeriksaan biasa.

Aturan penamaan Go: file `xxx_test.go`, fungsi `TestXxx(t *testing.T)`.

Agar router bisa diuji, **pisahkan pembuatan router dari `main`** (fungsi `SetupRouter()` yang mengembalikan `*gin.Engine`).

### Pasang
```powershell
go get github.com/stretchr/testify
```

## Langkah
Buat dua file di folder `contoh-kode/09-testify/`.

`handler.go`:

```go
// File: contoh-kode/09-testify/handler.go
package handler

import (
	"net/http"

	"github.com/gin-gonic/gin"
)

// Tambah adalah fungsi murni yang akan kita uji.
func Tambah(a, b int) int { return a + b }

type HitungRequest struct {
	A int `json:"a"`
	B int `json:"b" binding:"required"`
}

// SetupRouter dipisahkan dari main agar bisa dipakai di test.
func SetupRouter() *gin.Engine {
	gin.SetMode(gin.TestMode)
	r := gin.New()

	r.GET("/ping", func(c *gin.Context) {
		c.JSON(http.StatusOK, gin.H{"pesan": "pong"})
	})

	r.POST("/tambah", func(c *gin.Context) {
		var req HitungRequest
		if err := c.ShouldBindJSON(&req); err != nil {
			c.JSON(http.StatusBadRequest, gin.H{"error": "input tidak valid"})
			return
		}
		c.JSON(http.StatusOK, gin.H{"hasil": Tambah(req.A, req.B)})
	})
	return r
}
```

`handler_test.go`:

```go
// File: contoh-kode/09-testify/handler_test.go
package handler

import (
	"encoding/json"
	"net/http"
	"net/http/httptest"
	"strings"
	"testing"

	"github.com/stretchr/testify/assert"
	"github.com/stretchr/testify/require"
)

func TestTambah(t *testing.T) {
	tests := []struct {
		nama       string
		a, b       int
		diharapkan int
	}{
		{"positif", 2, 3, 5},
		{"negatif", -2, -3, -5},
		{"nol", 0, 0, 0},
	}
	for _, tc := range tests {
		t.Run(tc.nama, func(t *testing.T) {
			assert.Equal(t, tc.diharapkan, Tambah(tc.a, tc.b))
		})
	}
}

func TestPing(t *testing.T) {
	r := SetupRouter()

	w := httptest.NewRecorder()
	req := httptest.NewRequest(http.MethodGet, "/ping", nil)
	r.ServeHTTP(w, req)

	assert.Equal(t, http.StatusOK, w.Code)
	assert.JSONEq(t, `{"pesan":"pong"}`, w.Body.String())
}

func TestTambahEndpoint(t *testing.T) {
	r := SetupRouter()

	t.Run("sukses", func(t *testing.T) {
		w := httptest.NewRecorder()
		req := httptest.NewRequest(http.MethodPost, "/tambah", strings.NewReader(`{"a":4,"b":6}`))
		req.Header.Set("Content-Type", "application/json")
		r.ServeHTTP(w, req)

		require.Equal(t, http.StatusOK, w.Code) // require: berhenti jika gagal
		var resp map[string]int
		require.NoError(t, json.Unmarshal(w.Body.Bytes(), &resp))
		assert.Equal(t, 10, resp["hasil"])
	})

	t.Run("input tidak valid", func(t *testing.T) {
		w := httptest.NewRecorder()
		req := httptest.NewRequest(http.MethodPost, "/tambah", strings.NewReader(`{"a":4}`))
		req.Header.Set("Content-Type", "application/json")
		r.ServeHTTP(w, req)

		assert.Equal(t, http.StatusBadRequest, w.Code)
	})
}
```

Jalankan:
```powershell
go test ./contoh-kode/09-testify -v
```

## Output yang Diharapkan
```
=== RUN   TestTambah
=== RUN   TestTambah/positif
...
--- PASS: TestTambah (0.00s)
=== RUN   TestPing
--- PASS: TestPing (0.00s)
=== RUN   TestTambahEndpoint
--- PASS: TestTambahEndpoint (0.00s)
PASS
ok  	libpenting/contoh-kode/09-testify
```

Cakupan (*coverage*):
```powershell
go test ./contoh-kode/09-testify -cover
```

## Penjelasan
- `httptest.NewRecorder()` merekam status dan body.
- `assert.JSONEq` membandingkan JSON tanpa peduli urutan key/spasi.
- `t.Run("nama", ...)` membuat sub-test sehingga laporan jelas.
- Test "input tidak valid" memanfaatkan gotcha `required` pada `int` (nilai `0` ditolak); karena itu `B` wajib ada.

## Kesalahan Umum
- Argumen `assert.Equal(t, diharapkan, aktual)`: urutannya **expected dulu**, baru actual.
- Lupa `req.Header.Set("Content-Type", "application/json")` -> binding gagal.
- Menguji lewat server sungguhan di port tetap -> bentrok antar test.

## Latihan
Tambahkan endpoint `GET /kuadrat/:n` beserta test untuk `n` valid, `n` bukan angka, dan `n` negatif.

## Catatan Instruktur
Estimasi 60 menit. Tunjukkan `go test -race ./...` dan `go test -cover`.
