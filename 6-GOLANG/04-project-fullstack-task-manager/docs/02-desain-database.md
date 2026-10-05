# 02 - Desain Database

## Tujuan Pembelajaran
- Merancang dua tabel berelasi (`users` dan `tasks`).
- Membuat database lewat file SQL.

## Rancangan (ERD)

```
 users                          tasks
 ---------------------          ------------------------------
 id (PK)               1 --- *  id (PK)
 name                           user_id (FK -> users.id)
 email (UNIQUE)                 title
 password_hash                  description (boleh NULL)
 created_at                     status (todo | in_progress | done)
                                due_date (boleh NULL)
                                created_at
                                updated_at
```

Satu user punya banyak task. Jika user dihapus, semua task miliknya ikut terhapus (`ON DELETE CASCADE`).

## Langkah

### 1. Nyalakan MySQL
- **XAMPP**: buka XAMPP Control Panel, klik **Start** pada **MySQL**.
- **Laragon**: klik **Start All**.

Buka phpMyAdmin di `http://localhost/phpmyadmin`.

### 2. Buat file schema
Buat folder `database` di dalam `04-project-fullstack-task-manager`, lalu buat file `database/schema.sql`:

```sql
-- File: database/schema.sql
-- Jalankan file ini di phpMyAdmin (tab SQL) atau lewat MySQL CLI.

CREATE DATABASE IF NOT EXISTS task_manager
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE task_manager;

CREATE TABLE IF NOT EXISTS users (
  id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name          VARCHAR(100)    NOT NULL,
  email         VARCHAR(150)    NOT NULL,
  password_hash VARCHAR(255)    NOT NULL,
  created_at    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS tasks (
  id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id     BIGINT UNSIGNED NOT NULL,
  title       VARCHAR(200)    NOT NULL,
  description TEXT            NULL,
  status      ENUM('todo','in_progress','done') NOT NULL DEFAULT 'todo',
  due_date    DATE            NULL,
  created_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_tasks_user_status (user_id, status),
  CONSTRAINT fk_tasks_user FOREIGN KEY (user_id)
    REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB;
```

### 3. Jalankan
1. Buka phpMyAdmin -> tab **SQL**.
2. Tempel seluruh isi `schema.sql` -> klik **Go / Kirim**.
3. Di panel kiri harus muncul database `task_manager` dengan tabel `users` dan `tasks`.

## Penjelasan Penting
| Bagian | Arti |
|---|---|
| `BIGINT UNSIGNED AUTO_INCREMENT` | ID otomatis naik, tidak negatif |
| `UNIQUE KEY uq_users_email` | Tidak boleh ada dua user dengan email sama |
| `password_hash VARCHAR(255)` | Menyimpan **hash** bcrypt, bukan password asli |
| `ENUM(...)` | Kolom hanya boleh berisi nilai yang terdaftar |
| `NULL` pada `description`, `due_date` | Boleh kosong |
| `ON UPDATE CURRENT_TIMESTAMP` | `updated_at` otomatis berubah saat baris di-update |
| `KEY idx_tasks_user_status` | Index agar query filter per user + status cepat |
| `FOREIGN KEY ... ON DELETE CASCADE` | Menjaga konsistensi data antar tabel |

## Kesalahan Umum
- **Access denied for user 'root'**: password root di XAMPP kosong secara default. Di Laragon juga kosong.
- **Table already exists**: aman, kita memakai `IF NOT EXISTS`.

## Latihan
Tambahkan kolom `priority` bertipe `ENUM('low','medium','high')` ke tabel `tasks` (cukup lewat SQL `ALTER TABLE`). Kolom ini tidak dipakai di kode; ini hanya untuk latihan.

## Catatan Instruktur
Estimasi 30 menit. Tunjukkan tabel lewat phpMyAdmin tab **Designer** untuk melihat relasi.
