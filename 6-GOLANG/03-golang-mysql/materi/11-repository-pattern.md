# 11 - Repository Pattern

## Tujuan Pembelajaran
- Memisahkan kode database dari logika aplikasi.
- Mendefinisikan interface repository dan implementasi MySQL-nya.
- Memahami keuntungannya untuk testing.

## Konsep
Tanpa pola ini, SQL tersebar di handler, controller, dan fungsi lain. Mengubah satu kolom berarti mengubah banyak tempat.

**Repository** = satu lapisan khusus yang menyembunyikan detail penyimpanan.

```
Handler (HTTP)  -->  Repository (interface)  -->  Implementasi MySQL
                          ^
                          +--  Implementasi palsu (mock) untuk test
```

Keuntungan:
1. **Satu tempat** untuk SQL.
2. **Mudah dites**: handler dites dengan repository palsu, tanpa database.
3. **Mudah diganti**: pindah ke PostgreSQL cukup mengganti implementasi.

Interface di Go bersifat **implisit**: tipe otomatis memenuhi interface jika punya method yang sama; tidak ada kata `implements`.

## Langkah
Buat `contoh-kode/11-repository/main.go` (semua dalam satu file agar mudah dibaca; di project nyata dipisah per file/paket seperti di materi 4):

```go
// File: contoh-kode/11-repository/main.go
package main

import (
	"context"
	"database/sql"
	"errors"
	"fmt"
	"log"

	"golangmysql/internal/koneksi"
)

// ---------- Model ----------

type Product struct {
	ID    int64
	Name  string
	Price float64
	Stock int
}

var ErrNotFound = errors.New("produk tidak ditemukan")

// ---------- Interface (kontrak) ----------

// ProductRepository adalah kontrak. Service/handler hanya bergantung pada interface ini,
// sehingga mudah diganti dengan implementasi palsu (mock) saat testing.
type ProductRepository interface {
	Create(ctx context.Context, p *Product) error
	FindByID(ctx context.Context, id int64) (*Product, error)
	List(ctx context.Context) ([]Product, error)
	Update(ctx context.Context, p *Product) error
	Delete(ctx context.Context, id int64) error
}

// ---------- Implementasi MySQL ----------

type mysqlProductRepo struct {
	db *sql.DB
}

func NewProductRepository(db *sql.DB) ProductRepository {
	return &mysqlProductRepo{db: db}
}

func (r *mysqlProductRepo) Create(ctx context.Context, p *Product) error {
	res, err := r.db.ExecContext(ctx,
		`INSERT INTO products (name, price, stock) VALUES (?, ?, ?)`, p.Name, p.Price, p.Stock)
	if err != nil {
		return err
	}
	p.ID, err = res.LastInsertId()
	return err
}

func (r *mysqlProductRepo) FindByID(ctx context.Context, id int64) (*Product, error) {
	var p Product
	err := r.db.QueryRowContext(ctx,
		`SELECT id, name, price, stock FROM products WHERE id = ?`, id).
		Scan(&p.ID, &p.Name, &p.Price, &p.Stock)
	if errors.Is(err, sql.ErrNoRows) {
		return nil, ErrNotFound
	}
	if err != nil {
		return nil, err
	}
	return &p, nil
}

func (r *mysqlProductRepo) List(ctx context.Context) ([]Product, error) {
	rows, err := r.db.QueryContext(ctx, `SELECT id, name, price, stock FROM products ORDER BY id`)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	var out []Product
	for rows.Next() {
		var p Product
		if err := rows.Scan(&p.ID, &p.Name, &p.Price, &p.Stock); err != nil {
			return nil, err
		}
		out = append(out, p)
	}
	return out, rows.Err()
}

func (r *mysqlProductRepo) Update(ctx context.Context, p *Product) error {
	_, err := r.db.ExecContext(ctx,
		`UPDATE products SET name = ?, price = ?, stock = ? WHERE id = ?`, p.Name, p.Price, p.Stock, p.ID)
	return err
}

func (r *mysqlProductRepo) Delete(ctx context.Context, id int64) error {
	res, err := r.db.ExecContext(ctx, `DELETE FROM products WHERE id = ?`, id)
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

// ---------- Pemakaian ----------

func main() {
	db, err := koneksi.Buka()
	if err != nil {
		log.Fatal(err)
	}
	defer db.Close()

	var repo ProductRepository = NewProductRepository(db)
	ctx := context.Background()

	// Create
	p := &Product{Name: "Webcam HD", Price: 275000, Stock: 12}
	if err := repo.Create(ctx, p); err != nil {
		log.Fatal(err)
	}
	fmt.Println("Dibuat dengan ID:", p.ID)

	// Read
	got, err := repo.FindByID(ctx, p.ID)
	if err != nil {
		log.Fatal(err)
	}
	fmt.Printf("Ditemukan: %+v\n", *got)

	// Update
	got.Price = 250000
	if err := repo.Update(ctx, got); err != nil {
		log.Fatal(err)
	}

	// List
	list, err := repo.List(ctx)
	if err != nil {
		log.Fatal(err)
	}
	fmt.Println("Total produk:", len(list))

	// Delete
	if err := repo.Delete(ctx, p.ID); err != nil {
		log.Fatal(err)
	}
	if _, err := repo.FindByID(ctx, p.ID); errors.Is(err, ErrNotFound) {
		fmt.Println("Setelah dihapus: produk tidak ditemukan (sesuai harapan)")
	}
}
```

Jalankan:
```powershell
go run ./contoh-kode/11-repository
```

## Output yang Diharapkan
```
Dibuat dengan ID: 12
Ditemukan: {ID:12 Name:Webcam HD Price:275000 Stock:12}
Total produk: 4
Setelah dihapus: produk tidak ditemukan (sesuai harapan)
```

## Penjelasan
- `ErrNotFound` adalah error domain milik aplikasi. Lapisan atas tidak perlu mengenal `sql.ErrNoRows`.
- `NewProductRepository` mengembalikan **interface**, sedangkan struct `mysqlProductRepo` tidak diekspor (huruf kecil). Pengguna hanya melihat kontraknya.
- Setiap method menerima `ctx` sebagai argumen pertama (konvensi Go).

## Contoh Repository Palsu untuk Testing
```go
type fakeRepo struct{ data map[int64]Product }

func (f *fakeRepo) FindByID(_ context.Context, id int64) (*Product, error) {
    p, ok := f.data[id]
    if !ok { return nil, ErrNotFound }
    return &p, nil
}
// ... method lain mengikuti ...
```
Handler yang menerima `ProductRepository` dapat diuji dengan `fakeRepo` tanpa MySQL.

## Kesalahan Umum
- Interface terlalu besar. Buat sekecil yang dibutuhkan konsumennya.
- Repository berisi logika bisnis (aturan diskon, dsb). Taruh itu di lapisan *service*.

## Latihan
Tambahkan method `FindByName(ctx, name) ([]Product, error)` ke interface dan implementasinya.

## Catatan Instruktur
Estimasi 60 menit. Ini jembatan langsung ke project materi 4 (`backend/internal/repository`).
