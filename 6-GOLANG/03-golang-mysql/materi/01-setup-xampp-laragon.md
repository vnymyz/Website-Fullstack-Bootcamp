# 01 - Setup MySQL dengan XAMPP / Laragon

## Tujuan Pembelajaran
- Memasang dan menyalakan MySQL di komputer lokal.
- Membuat database dan tabel lewat phpMyAdmin.
- Menyiapkan folder project dan file `.env` untuk materi ini.

## Konsep
**MySQL** adalah database relasional: data disimpan di **tabel** (baris dan kolom) dan dikelola dengan bahasa **SQL**. Program Go **tidak** berisi database; ia berbicara ke server MySQL lewat jaringan (TCP port 3306).

```
Program Go  --(TCP :3306, driver mysql)-->  Server MySQL  -->  Data
```

## Langkah

### Opsi A - XAMPP
1. Unduh XAMPP dari https://www.apachefriends.org lalu pasang (cukup komponen **MySQL** dan **phpMyAdmin**).
2. Buka **XAMPP Control Panel** -> klik **Start** pada **MySQL** (tulisan hijau = hidup).
3. Buka `http://localhost/phpmyadmin` (klik **Start** pada Apache juga agar phpMyAdmin dapat dibuka).

### Opsi B - Laragon
1. Unduh Laragon Full dari https://laragon.org lalu pasang.
2. Klik **Start All**.
3. Klik kanan ikon Laragon -> **MySQL** -> **HeidiSQL** atau buka phpMyAdmin bila terpasang.

> Default XAMPP/Laragon: user `root`, **password kosong**, port `3306`. Cukup untuk belajar. **Jangan** dipakai seperti ini di server sungguhan.

### Memeriksa port
Jika MySQL gagal menyala, kemungkinan port 3306 dipakai. Cek di PowerShell:
```powershell
netstat -ano | findstr :3306
```

### Buat database dan tabel
1. Di phpMyAdmin klik tab **SQL**.
2. Tempel isi `database/schema.sql`:

```sql
-- File: database/schema.sql
-- Database latihan materi 3
CREATE DATABASE IF NOT EXISTS belajar_go
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE belajar_go;

DROP TABLE IF EXISTS products;
CREATE TABLE products (
  id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name        VARCHAR(100)    NOT NULL,
  price       DECIMAL(12,2)   NOT NULL,
  stock       INT             NOT NULL DEFAULT 0,
  description TEXT            NULL,
  created_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS accounts;
CREATE TABLE accounts (
  id      BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  owner   VARCHAR(100)    NOT NULL,
  balance DECIMAL(14,2)   NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  CONSTRAINT chk_balance CHECK (balance >= 0)
) ENGINE=InnoDB;
```

3. Klik **Kirim/Go**. Database `belajar_go` berisi tabel `products` dan `accounts`.
4. Jalankan juga `database/seed.sql` (data contoh):

```sql
-- File: database/seed.sql
USE belajar_go;

INSERT INTO products (name, price, stock, description) VALUES
  ('Keyboard Mekanik', 450000, 10, 'Switch biru, layout 87 tombol'),
  ('Mouse Wireless',   150000, 25, NULL),
  ('Monitor 24 inci',  1750000, 5, 'Panel IPS Full HD');

INSERT INTO accounts (owner, balance) VALUES
  ('Andi', 1000000),
  ('Budi', 500000);
```

5. Klik tabel `products` -> **Browse** untuk melihat 3 baris data.

### Menyiapkan project Go
Folder `03-golang-mysql` sudah memiliki `go.mod`. Jika membuat sendiri dari nol:
```powershell
mkdir 03-golang-mysql
cd 03-golang-mysql
go mod init golangmysql
go get github.com/go-sql-driver/mysql
go get github.com/joho/godotenv
```

Buat `.env.example` dan salin menjadi `.env`:

```env
# File: .env.example
DB_HOST=127.0.0.1
DB_PORT=3306
DB_USER=root
DB_PASS=
DB_NAME=belajar_go
```

```powershell
copy .env.example .env
```

Buat juga `.gitignore` berisi `.env` agar rahasia tidak ter-commit.

## Pengantar SQL Singkat (Pengingat)
```sql
INSERT INTO products (name, price, stock) VALUES ('Pulpen', 3000, 100);
SELECT id, name, price FROM products WHERE price > 100000 ORDER BY price DESC;
UPDATE products SET stock = stock - 1 WHERE id = 1;
DELETE FROM products WHERE id = 3;
```
Coba keempat perintah di tab **SQL** phpMyAdmin sebelum lanjut.

## Kesalahan Umum
| Masalah | Solusi |
|---|---|
| MySQL langsung berhenti di XAMPP | Port 3306 terpakai MySQL lain; hentikan service MySQL Windows atau ubah port |
| phpMyAdmin tidak terbuka | Apache belum dinyalakan |
| `#1046 No database selected` | Pilih database dulu atau tulis `USE belajar_go;` |

## Latihan
Tambahkan lewat phpMyAdmin tabel `categories (id, name)` dan tambahkan dua baris data.

## Catatan Instruktur
Estimasi 45 menit. Pastikan **semua** siswa sudah bisa membuka phpMyAdmin dan melihat tabel `products` sebelum melanjutkan; ini sumber masalah terbanyak.
