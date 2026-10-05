package main

import (
	"net/http"
	"net/http/httptest"
	"strings"
	"testing"

	"github.com/stretchr/testify/assert"
	"github.com/stretchr/testify/require"
)

func kirim(r http.Handler, method, path, body string) *httptest.ResponseRecorder {
	req := httptest.NewRequest(method, path, strings.NewReader(body))
	req.Header.Set("Content-Type", "application/json")
	w := httptest.NewRecorder()
	r.ServeHTTP(w, req)
	return w
}

func TestTodoCRUD(t *testing.T) {
	r := SetupRouter(NewStore())

	// Create
	w := kirim(r, "POST", "/todos", `{"judul":"Belajar Gin"}`)
	require.Equal(t, http.StatusCreated, w.Code)
	assert.JSONEq(t, `{"id":1,"judul":"Belajar Gin","selesai":false}`, w.Body.String())

	// Validasi gagal
	w = kirim(r, "POST", "/todos", `{"judul":"ab"}`)
	assert.Equal(t, http.StatusBadRequest, w.Code)

	// List
	w = kirim(r, "GET", "/todos", "")
	assert.Equal(t, http.StatusOK, w.Code)
	assert.JSONEq(t, `[{"id":1,"judul":"Belajar Gin","selesai":false}]`, w.Body.String())

	// Update
	w = kirim(r, "PUT", "/todos/1", `{"judul":"Belajar Gin","selesai":true}`)
	assert.Equal(t, http.StatusOK, w.Code)
	assert.JSONEq(t, `{"id":1,"judul":"Belajar Gin","selesai":true}`, w.Body.String())

	// Update id tidak ada
	w = kirim(r, "PUT", "/todos/99", `{"judul":"Tidak ada"}`)
	assert.Equal(t, http.StatusNotFound, w.Code)

	// Delete
	w = kirim(r, "DELETE", "/todos/1", "")
	assert.Equal(t, http.StatusNoContent, w.Code)

	w = kirim(r, "DELETE", "/todos/1", "")
	assert.Equal(t, http.StatusNotFound, w.Code)
}
