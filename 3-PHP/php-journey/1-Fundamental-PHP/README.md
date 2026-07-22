# Sesi 1 — Fundamental PHP

Tujuan: paham mekanik dasar bahasa PHP (tag, variabel, tipe data, operator, percabangan, perulangan, array, fungsi, include/require) sebelum lanjut ke form & database.

## Cara Menjalankan (Laragon)

PHP itu bahasa server-side — file `.php` **tidak bisa** dibuka langsung dengan cara double-click atau `file:///...` di browser (nanti isinya cuma kode mentah, bukan hasil olahannya). Harus lewat server.

1. Buka **Laragon**, klik **Start All** (nyalain Apache + MySQL).
2. Pastikan folder project ini (`php-journey`) ada di dalam folder root Laragon, biasanya: `C:\laragon\www\`
   - Kalau project kamu sekarang ada di lokasi lain, copy/pindahin folder `php-journey` ke `C:\laragon\www\php-journey`, atau bikin symlink lewat Laragon (klik kanan Laragon tray icon → `www` → buka folder itu).
3. Buka browser, akses:
   ```
   http://localhost/php-journey/1-Fundamental-PHP/index.php
   ```
4. Ganti nama file di URL buat buka file lain, contoh:
   ```
   http://localhost/php-journey/1-Fundamental-PHP/variables.php
   http://localhost/php-journey/1-Fundamental-PHP/loops.php
   http://localhost/php-journey/1-Fundamental-PHP/arrays.php
   http://localhost/php-journey/1-Fundamental-PHP/functions.php
   http://localhost/php-journey/1-Fundamental-PHP/includes-demo/page.php
   http://localhost/php-journey/1-Fundamental-PHP/latihan/fizzbuzz.php
   http://localhost/php-journey/1-Fundamental-PHP/latihan/kalkulator-struk.php
   ```

**Kalau muncul error "Not Found"**: cek Apache udah nyala (warna hijau di Laragon), dan folder project bener-bener ada di `www`.

**Kalau kode PHP malah muncul mentah di browser** (kelihatan `<?php echo...`): berarti kamu buka file-nya langsung dari File Explorer (double click), bukan lewat `http://localhost/...`. Wajib lewat browser + alamat localhost.

## Urutan Belajar (Step by Step)

Pelajari sesuai urutan ini, jangan loncat — tiap file ada catatan "lanjut ke..." di bagian bawah kode.

| Urutan | File | Yang Dipelajari |
|---|---|---|
| 1 | `index.php` | Tag `<?php ?>`, `echo`/`print`, komentar, operator, `if/elseif/else`, `switch` |
| 2 | `variables.php` | Variabel, tipe data, `var_dump()`, type juggling, string interpolation vs concatenation |
| 3 | `loops.php` | `for`, `while`, `do-while`, `foreach`, `break`/`continue` |
| 4 | `arrays.php` | Array indexed, array asosiatif, array 2 dimensi, fungsi array bawaan (`sort`, `array_sum`, dll) |
| 5 | `functions.php` | Fungsi, parameter, default value, return value, type hint |
| 6 | `includes-demo/page.php` | `include`/`require` — cara pecah halaman jadi header/footer biar reusable |
| 7 | `latihan/fizzbuzz.php` | Latihan gabungan: loop + percabangan |
| 8 | `latihan/kalkulator-struk.php` | Latihan gabungan: loop + array + operator |
| 9 | `latihan/fizzbuzz-jawaban.php` | Contoh jawaban — cek setelah nyoba sendiri |
| 10 | `latihan/kalkulator-struk-jawaban.php` | Contoh jawaban — cek setelah nyoba sendiri |

**Cara belajar tiap file:**
1. Buka file-nya di code editor (VS Code dll), baca komentarnya dari atas ke bawah.
2. Jalankan lewat browser sambil baca kode-nya bareng-bareng — pahami output-nya berasal dari baris mana.
3. Coba ubah-ubah nilai variabelnya sendiri (contoh: ganti isi `$warna` di `arrays.php`), refresh browser, lihat efeknya.
4. Ada catatan "Catatan buat kamu" di tiap file — itu tugas kecil buat latihan sendiri, kerjakan sebelum lanjut ke file berikutnya.

## File yang Perlu Dibuat/Ada di Folder Ini

Sudah tersedia semua (tinggal buka & pelajari):
```
1-Fundamental-PHP/
  README.md                      <- panduan ini
  index.php                      <- mulai dari sini
  variables.php
  loops.php
  arrays.php
  functions.php
  includes-demo/
    header.php
    footer.php
    page.php                     <- buka ini (bukan header/footer langsung)
  latihan/
    fizzbuzz.php                 <- ada TODO, isi sendiri
    kalkulator-struk.php         <- ada TODO, isi sendiri
    fizzbuzz-jawaban.php         <- contoh jawaban, cek terakhir
    kalkulator-struk-jawaban.php <- contoh jawaban, cek terakhir
```

Yang **perlu kamu isi sendiri** (jangan minta AI buat isiin, ini latihan closed-book):
- `latihan/fizzbuzz.php` — cari bagian `// TODO: tulis logic FizzBuzz di sini`
- `latihan/kalkulator-struk.php` — cari bagian-bagian `// TODO`

Jangan buka file `-jawaban.php` duluan. Selesein punya kamu sendiri dulu, baru cocokin ke jawaban buat ngecek logic-nya bener atau nggak.

## Checkpoint Sebelum Lanjut ke Sesi 2

Sebelum lanjut ke `2-Forms-and-Superglobals/`, pastikan bisa jawab/lakuin ini tanpa buka catatan/AI:
- Jelasin bedanya `echo` sama `print`.
- Jelasin bedanya `==` sama `===`, kasih contoh kasusnya.
- Jelasin bedanya array indexed sama array asosiatif.
- Bikin fungsi baru dari nol (bukan nyontek yang udah ada) yang nerima 2 parameter dan return hasilnya.
- Selesein `fizzbuzz.php` dan `kalkulator-struk.php` sampai outputnya bener.
