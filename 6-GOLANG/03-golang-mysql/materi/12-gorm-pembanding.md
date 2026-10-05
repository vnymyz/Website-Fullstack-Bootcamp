# 12 - GORM sebagai Pembanding

## Tujuan Pembelajaran
- Mengenal ORM dan GORM.
- Melakukan CRUD yang sama dengan GORM.
- Menilai kelebihan dan kekurangan ORM dibanding `database/sql`.

## Konsep
**ORM** (*Object-Relational Mapper*) memetakan struct Go ke tabel, sehingga Anda memanipulasi objek, bukan menulis SQL. **GORM** adalah ORM paling populer di Go.

| Aspek | `database/sql` | GORM |
|---|---|---|
| Penulisan CRUD | Manual, panjang | Singkat |
| Kontrol SQL | Penuh | Terbatas, kadang "ajaib" |
| Performa | Terbaik | Sedikit overhead |
| Kurva belajar | Perlu paham SQL | Cepat mulai, tetapi tetap perlu paham SQL |
| Migrasi | Manual | `AutoMigrate` |
| Debug query | Terlihat jelas | Perlu `db.Debug()` |
| Cocok untuk | Query kompleks, kontrol penuh | CRUD cepat, prototipe |

**Saran:** pahami `database/sql` dulu (supaya mengerti apa yang terjadi), baru pakai ORM bila membantu. Kita memakai `database/sql` di project materi 4.

## Langkah

### 1. Pasang GORM
```powershell
go get gorm.io/gorm
go get gorm.io/driver/mysql
```

### 2. Program
Buat `contoh-kode/12-gorm/main.go`:

```go
// File: contoh-kode/12-gorm/main.go
package main

import (
	"fmt"
	"log"
	"time"

	"gorm.io/driver/mysql"
	"gorm.io/gorm"

	"golangmysql/internal/koneksi"
)

// Nama tabel default GORM = bentuk jamak snake_case dari nama struct ("Item" -> "items").
// Kita pakai tabel baru agar tidak bentrok dengan tabel "products" dari contoh lain.
type Item struct {
	ID          uint   `gorm:"primaryKey"`
	Name        string `gorm:"size:100;not null"`
	Price       float64
	Stock       int
	Description *string // pointer = kolom boleh NULL
	CreatedAt   time.Time
	UpdatedAt   time.Time
}

func main() {
	db, err := gorm.Open(mysql.Open(koneksi.DSN()), &gorm.Config{})
	if err != nil {
		log.Fatal(err)
	}

	// AutoMigrate: membuat/menyesuaikan tabel dari struct (praktis untuk belajar,
	// di produksi lebih aman memakai tool migrasi bernomor).
	if err := db.AutoMigrate(&Item{}); err != nil {
		log.Fatal(err)
	}

	// CREATE
	item := Item{Name: "Flashdisk 64GB", Price: 85000, Stock: 30}
	if err := db.Create(&item).Error; err != nil {
		log.Fatal(err)
	}
	fmt.Println("Dibuat, ID:", item.ID)

	// READ satu
	var got Item
	if err := db.First(&got, item.ID).Error; err != nil {
		log.Fatal(err)
	}
	fmt.Printf("First: %+v\n", got.Name)

	// READ banyak dengan kondisi
	var murah []Item
	db.Where("price < ?", 100000).Order("price asc").Find(&murah)
	fmt.Println("Item murah:", len(murah))

	// UPDATE
	db.Model(&got).Updates(map[string]any{"price": 80000, "stock": 25})

	// DELETE
	db.Delete(&Item{}, item.ID)

	var total int64
	db.Model(&Item{}).Count(&total)
	fmt.Println("Sisa item:", total)
}
```

Jalankan:
```powershell
go run ./contoh-kode/12-gorm
```

## Output yang Diharapkan
```
Dibuat, ID: 1
First: Flashdisk 64GB
Item murah: 1
Sisa item: 0
```
Di phpMyAdmin akan muncul tabel baru `items` (dibuat otomatis).

## Perbandingan Langsung

| Operasi | `database/sql` | GORM |
|---|---|---|
| Create | `db.ExecContext(ctx, "INSERT ... VALUES (?,?)", ...)` | `db.Create(&item)` |
| Ambil satu | `QueryRowContext(...).Scan(&a,&b,...)` | `db.First(&item, id)` |
| Ambil banyak | `QueryContext` + loop `Next/Scan` | `db.Where("price < ?", 100000).Find(&items)` |
| Update | `ExecContext("UPDATE ...")` | `db.Model(&item).Updates(map[string]any{...})` |
| Delete | `ExecContext("DELETE ...")` | `db.Delete(&Item{}, id)` |

## Hal yang Perlu Diwaspadai
- `db.Create(&x).Error`: error ada di field `.Error`; jangan lupa memeriksanya.
- `Updates` dengan **struct** mengabaikan nilai nol (`0`, `""`, `false`). Pakai `map` bila ingin mengisi nilai nol.
- `db.Delete` tanpa kondisi akan ditolak GORM (`ErrMissingWhereClause`); itu bagus.
- Lihat SQL yang dibuat: `db.Debug().First(&item, 1)`.
- `AutoMigrate` tidak menghapus kolom; cocok untuk belajar, di produksi pakai tool migrasi.

## Latihan
Ubah `main.go` agar mencetak SQL yang dihasilkan memakai `db.Debug()`, lalu bandingkan dengan SQL yang Anda tulis manual di materi 04-06.

## Catatan Instruktur
Estimasi 60 menit. Jangan mengklaim satu pendekatan "paling benar". Tunjukkan trade-off dan biarkan siswa memilih sesuai kasus.
