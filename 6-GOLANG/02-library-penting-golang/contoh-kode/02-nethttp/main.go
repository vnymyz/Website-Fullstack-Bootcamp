package main

import (
	"encoding/json"
	"log"
	"net/http"
	"strconv"
	"sync"
)

type Buku struct {
	ID    int    `json:"id"`
	Judul string `json:"judul"`
}

// Penyimpanan sementara di memori. Mutex wajib karena handler dijalankan di banyak goroutine.
var (
	mu     sync.Mutex
	daftar = []Buku{{1, "Belajar Go"}, {2, "Pemrograman Web"}}
	nextID = 3
)

func tulisJSON(w http.ResponseWriter, status int, data any) {
	w.Header().Set("Content-Type", "application/json")
	w.WriteHeader(status)
	json.NewEncoder(w).Encode(data)
}

func main() {
	mux := http.NewServeMux()

	// Go 1.22+: pola "METHOD /path/{param}"
	mux.HandleFunc("GET /", func(w http.ResponseWriter, r *http.Request) {
		w.Write([]byte("Halo dari net/http!"))
	})

	mux.HandleFunc("GET /buku", func(w http.ResponseWriter, r *http.Request) {
		mu.Lock()
		defer mu.Unlock()
		tulisJSON(w, http.StatusOK, daftar)
	})

	mux.HandleFunc("GET /buku/{id}", func(w http.ResponseWriter, r *http.Request) {
		id, err := strconv.Atoi(r.PathValue("id"))
		if err != nil {
			tulisJSON(w, http.StatusBadRequest, map[string]string{"error": "id harus angka"})
			return
		}
		mu.Lock()
		defer mu.Unlock()
		for _, b := range daftar {
			if b.ID == id {
				tulisJSON(w, http.StatusOK, b)
				return
			}
		}
		tulisJSON(w, http.StatusNotFound, map[string]string{"error": "buku tidak ditemukan"})
	})

	mux.HandleFunc("POST /buku", func(w http.ResponseWriter, r *http.Request) {
		var in struct {
			Judul string `json:"judul"`
		}
		if err := json.NewDecoder(r.Body).Decode(&in); err != nil || in.Judul == "" {
			tulisJSON(w, http.StatusBadRequest, map[string]string{"error": "judul wajib diisi"})
			return
		}
		mu.Lock()
		defer mu.Unlock()
		b := Buku{ID: nextID, Judul: in.Judul}
		nextID++
		daftar = append(daftar, b)
		tulisJSON(w, http.StatusCreated, b)
	})

	log.Println("Server di http://localhost:8080")
	log.Fatal(http.ListenAndServe(":8080", mux))
}
