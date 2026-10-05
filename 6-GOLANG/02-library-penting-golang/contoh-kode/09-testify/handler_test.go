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
