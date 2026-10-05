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
