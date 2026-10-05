# 11 - Menguji API (curl, PowerShell, Postman)

## Tujuan Pembelajaran
- Menguji setiap endpoint sebelum membuat frontend.
- Memahami alur token: register/login -> pakai token -> akses endpoint terlindungi.

Pastikan backend berjalan (`go run ./cmd/api`). Buka **terminal baru** untuk perintah di bawah.

## Opsi A: PowerShell (`Invoke-RestMethod`)

```powershell
$base = "http://localhost:8080/api"

# 1. Register
$reg = Invoke-RestMethod -Method Post -Uri "$base/auth/register" -ContentType "application/json" `
  -Body '{"name":"Budi","email":"budi@example.com","password":"rahasia123"}'
$reg

# 2. Login dan simpan token
$login = Invoke-RestMethod -Method Post -Uri "$base/auth/login" -ContentType "application/json" `
  -Body '{"email":"budi@example.com","password":"rahasia123"}'
$token = $login.data.token
$headers = @{ Authorization = "Bearer $token" }

# 3. Data user saat ini
Invoke-RestMethod -Uri "$base/auth/me" -Headers $headers

# 4. Buat task
Invoke-RestMethod -Method Post -Uri "$base/tasks" -Headers $headers -ContentType "application/json" `
  -Body '{"title":"Belajar Gin","description":"Baca dokumentasi","due_date":"2026-12-31"}'

# 5. Daftar task (dengan filter dan pagination)
Invoke-RestMethod -Uri "$base/tasks?status=todo&page=1&limit=5" -Headers $headers

# 6. Ubah task id 1
Invoke-RestMethod -Method Put -Uri "$base/tasks/1" -Headers $headers -ContentType "application/json" `
  -Body '{"title":"Belajar Gin","description":"Selesai baca","status":"done","due_date":"2026-12-31"}'

# 7. Hapus task id 1
Invoke-RestMethod -Method Delete -Uri "$base/tasks/1" -Headers $headers
```

## Opsi B: curl (Git Bash / macOS / Linux)

```bash
BASE=http://localhost:8080/api

curl -s -X POST $BASE/auth/register -H "Content-Type: application/json" \
  -d '{"name":"Budi","email":"budi@example.com","password":"rahasia123"}'

TOKEN=$(curl -s -X POST $BASE/auth/login -H "Content-Type: application/json" \
  -d '{"email":"budi@example.com","password":"rahasia123"}' | sed -E 's/.*"token":"([^"]+)".*/\1/')

curl -s $BASE/tasks -H "Authorization: Bearer $TOKEN"

curl -s -X POST $BASE/tasks -H "Authorization: Bearer $TOKEN" -H "Content-Type: application/json" \
  -d '{"title":"Belajar Gin","status":"todo"}'
```

> Di Windows PowerShell, `curl` adalah alias `Invoke-WebRequest`, bukan curl asli. Pakai `curl.exe` atau Opsi A.

## Opsi C: Postman / Thunder Client (VS Code)
1. Buat request `POST http://localhost:8080/api/auth/login`, body **raw JSON**.
2. Salin `data.token` dari response.
3. Pada request lain, buka tab **Authorization** -> **Bearer Token** -> tempel token.

## Skenario Uji yang Wajib Dicoba

| Skenario | Hasil yang diharapkan |
|---|---|
| Register email yang sama dua kali | 409 `email sudah terdaftar` |
| Register password < 6 karakter | 400 `data tidak valid` |
| Login password salah | 401 `email atau password salah` |
| Akses `/api/tasks` tanpa token | 401 `token tidak ditemukan` |
| Akses dengan token ngawur | 401 `token tidak valid atau kedaluwarsa` |
| `GET /api/tasks/9999` | 404 `task tidak ditemukan` |
| Buat task `status` = `selesai` | 400 (hanya todo/in_progress/done) |
| Daftar user kedua, akses task user pertama | 404 (tidak bisa lihat data orang lain) |

## Kesalahan Umum
- `Invoke-RestMethod : Unable to connect` -> backend belum berjalan.
- JSON di PowerShell perlu tanda kutip tunggal di luar, ganda di dalam.

## Latihan
Tulis skrip PowerShell `test-api.ps1` yang menjalankan seluruh skenario di atas dan mencetak PASS/FAIL.

## Catatan Instruktur
Estimasi 45 menit. Biarkan siswa mencoba menjebol keamanan (akses task user lain) agar paham pentingnya filter `user_id`.
