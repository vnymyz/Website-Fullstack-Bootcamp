# 02 - Web Server dengan `net/http`

## Tujuan Pembelajaran
- Memahami cara kerja HTTP request/response.
- Membuat REST API sederhana hanya dengan standard library.
- Memakai routing method + path parameter (Go 1.22+).

## Konsep
Setiap request HTTP berisi **method** (GET/POST/PUT/DELETE), **URL**, **header**, dan opsional **body**. Server membalas dengan **status code**, **header**, dan **body**.

```
Client --GET /buku/1--> Server
Client <--200 {"id":1,...}-- Server
```

| Status | Arti |
|---|---|
| 200 OK / 201 Created | Sukses |
| 400 Bad Request | Input salah |
| 401 / 403 | Belum login / tidak berhak |
| 404 Not Found | Tidak ada |
| 500 Internal Server Error | Salah di server |

Handler di Go berbentuk `func(w http.ResponseWriter, r *http.Request)`:
- `r` -> data masuk (method, URL, header, body)
- `w` -> tempat menulis balasan

Sejak **Go 1.22**, `http.ServeMux` mendukung pola `"GET /buku/{id}"` dan `r.PathValue("id")`, sehingga sering tidak perlu router pihak ketiga.

**Penting:** handler dijalankan di banyak goroutine sekaligus. Data bersama (variabel global) harus dilindungi `sync.Mutex`.

## Langkah
Buat `contoh-kode/02-nethttp/main.go`:

```go
// File: contoh-kode/02-nethttp/main.go
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
```

Jalankan server:
```powershell
go run ./contoh-kode/02-nethttp
```

Di terminal lain, uji:
```powershell
Invoke-RestMethod http://localhost:8080/buku
Invoke-RestMethod http://localhost:8080/buku/1
Invoke-RestMethod -Method Post -Uri http://localhost:8080/buku -ContentType "application/json" -Body '{"judul":"Go Lanjut"}'
Invoke-RestMethod http://localhost:8080/buku/999
```

## Output yang Diharapkan
```
id judul
-- -----
 1 Belajar Go
 2 Pemrograman Web
```
Permintaan `/buku/999` menghasilkan error 404 dengan isi `{"error":"buku tidak ditemukan"}`.

## Penjelasan
- `tulisJSON` membungkus tiga langkah wajib: set header, tulis status, encode body. **Urutan penting**: header harus sebelum `WriteHeader`.
- `json.NewDecoder(r.Body).Decode(&in)` membaca JSON dari body.
- `r.PathValue("id")` hanya tersedia dengan pola `{id}` (Go 1.22+).

## Kesalahan Umum
- `WriteHeader` dipanggil setelah `Write` -> status tidak berubah (selalu 200).
- Lupa `return` setelah mengirim error -> response ganda.
- Tidak memakai Mutex pada data bersama -> *data race* (cek dengan `go run -race`).

## Latihan
Tambahkan `DELETE /buku/{id}` dan `PUT /buku/{id}`.

## Catatan Instruktur
Estimasi 60 menit. Ini pondasi: Gin hanya membungkus hal yang sama dengan lebih nyaman.
