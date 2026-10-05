# 01 - Standard Library yang Sering Dipakai

## Tujuan Pembelajaran
- Mengenal paket bawaan Go yang dipakai hampir di setiap project.
- Mampu memakai `fmt`, `strings`, `strconv`, `time`, `sort`, `encoding/json`, `errors`, `os`/`bufio`, `log/slog`, dan `context`.

## Konsep
Go terkenal dengan **standard library** yang kaya ("batteries included"). Banyak pekerjaan tidak butuh library pihak ketiga. Aturan praktis: **cari di standard library dulu**.

| Paket | Fungsi utama |
|---|---|
| `fmt` | Cetak dan format teks (`Printf`, `Sprintf`, `Errorf`) |
| `strings` | Manipulasi string (`Split`, `Join`, `Contains`, `Builder`) |
| `strconv` | Konversi string <-> angka/bool |
| `time` | Waktu, durasi, format tanggal |
| `sort` / `slices` | Mengurutkan dan mencari |
| `encoding/json` | JSON <-> struct |
| `errors` | `New`, `Is`, `As`, pembungkus error |
| `os`, `bufio`, `io` | File, argumen program, environment, baca/tulis |
| `log/slog` | Logging terstruktur (Go 1.21+) |
| `context` | Timeout dan pembatalan |
| `net/http` | Client dan server HTTP (materi 02) |
| `sync` | Mutex, WaitGroup |
| `testing` | Unit test |

### Layout waktu Go
Go tidak memakai `YYYY-MM-DD`. Ia memakai **tanggal referensi**: `Mon Jan 2 15:04:05 MST 2006`, yaitu 01/02 03:04:05PM '06 -0700. Maka format `2006-01-02 15:04:05` berarti `tahun-bulan-hari jam:menit:detik`.

### Struct tag JSON
| Tag | Arti |
|---|---|
| `json:"nama"` | Nama key di JSON |
| `json:"email,omitempty"` | Hilang jika nilai kosong |
| `json:"-"` | Tidak pernah dimasukkan |

## Langkah
Buat `contoh-kode/01-stdlib/main.go`:

```go
// File: contoh-kode/01-stdlib/main.go
package main

import (
	"bufio"
	"context"
	"encoding/json"
	"errors"
	"fmt"
	"log/slog"
	"os"
	"sort"
	"strconv"
	"strings"
	"time"
)

type Siswa struct {
	Nama    string   `json:"nama"`
	Umur    int      `json:"umur"`
	Email   string   `json:"email,omitempty"` // dihilangkan jika kosong
	Rahasia string   `json:"-"`               // tidak pernah ikut
	Hobi    []string `json:"hobi"`
}

func main() {
	// ---------- fmt ----------
	nama, umur, ipk := "Sari", 20, 3.756
	fmt.Printf("Nama: %s, Umur: %d, IPK: %.2f\n", nama, umur, ipk)
	fmt.Printf("%v | %+v | %T\n", umur, Siswa{Nama: nama}, ipk)
	teks := fmt.Sprintf("%05d|%-8s|%8s|", 42, "kiri", "kanan")
	fmt.Println(teks)

	// ---------- strings ----------
	s := "  Belajar Golang Itu Menyenangkan  "
	fmt.Println(strings.TrimSpace(s))
	fmt.Println(strings.ToUpper(s), strings.ToLower(s))
	fmt.Println(strings.Contains(s, "Golang"), strings.HasPrefix(strings.TrimSpace(s), "Belajar"))
	fmt.Println(strings.Split("a,b,c", ","), strings.Join([]string{"x", "y", "z"}, "-"))
	fmt.Println(strings.ReplaceAll("halo halo", "halo", "hai"))
	var sb strings.Builder // efisien untuk menyambung banyak string
	for i := 1; i <= 3; i++ {
		fmt.Fprintf(&sb, "[%d]", i)
	}
	fmt.Println(sb.String())

	// ---------- strconv ----------
	n, err := strconv.Atoi("123")
	fmt.Println(n+1, err)
	_, err = strconv.Atoi("abc")
	fmt.Println("error:", err)
	f, _ := strconv.ParseFloat("3.14", 64)
	b, _ := strconv.ParseBool("true")
	fmt.Println(f, b, strconv.Itoa(99)+" tahun", strconv.Quote("hai"))

	// ---------- time ----------
	sekarang := time.Now()
	fmt.Println(sekarang.Format("2006-01-02 15:04:05")) // layout referensi Go
	tgl, _ := time.Parse("2006-01-02", "2026-12-31")
	fmt.Println(tgl.Weekday(), tgl.Sub(sekarang).Hours() > 0)
	fmt.Println(sekarang.Add(36 * time.Hour).Format(time.RFC3339))
	fmt.Println(2*time.Second+500*time.Millisecond, time.Duration(90)*time.Minute)

	// ---------- sort ----------
	angka := []int{5, 2, 8, 1}
	sort.Ints(angka)
	fmt.Println(angka)
	orang := []Siswa{{Nama: "Zaki", Umur: 22}, {Nama: "Ani", Umur: 19}}
	sort.Slice(orang, func(i, j int) bool { return orang[i].Umur < orang[j].Umur })
	fmt.Println(orang[0].Nama)

	// ---------- encoding/json ----------
	siswa := Siswa{Nama: "Budi", Umur: 21, Rahasia: "xxx", Hobi: []string{"main bola", "coding"}}
	data, _ := json.Marshal(siswa) // struct -> JSON
	fmt.Println(string(data))
	indah, _ := json.MarshalIndent(siswa, "", "  ")
	fmt.Println(string(indah))

	var hasil Siswa // JSON -> struct
	if err := json.Unmarshal([]byte(`{"nama":"Cici","umur":23,"hobi":["baca"]}`), &hasil); err != nil {
		fmt.Println("error:", err)
	}
	fmt.Printf("%+v\n", hasil)

	var bebas map[string]any // JSON dengan bentuk tidak pasti
	_ = json.Unmarshal([]byte(`{"a":1,"b":"dua","c":[1,2]}`), &bebas)
	fmt.Println(bebas["a"], bebas["b"])

	// ---------- errors ----------
	errDasar := errors.New("koneksi putus")
	errBungkus := fmt.Errorf("simpan data gagal: %w", errDasar)
	fmt.Println(errBungkus, errors.Is(errBungkus, errDasar))

	// ---------- os + bufio ----------
	fmt.Println("Argumen program:", os.Args[1:])
	fmt.Println("HOME/USERPROFILE ada?", os.Getenv("USERPROFILE") != "" || os.Getenv("HOME") != "")
	if err := os.WriteFile("contoh.txt", []byte("baris satu\nbaris dua\nbaris tiga\n"), 0o644); err != nil {
		fmt.Println("gagal tulis:", err)
		return
	}
	defer os.Remove("contoh.txt")
	file, err := os.Open("contoh.txt")
	if err != nil {
		fmt.Println("gagal buka:", err)
		return
	}
	defer file.Close()
	scanner := bufio.NewScanner(file)
	for scanner.Scan() {
		fmt.Println("baca:", scanner.Text())
	}

	// ---------- log/slog (logging terstruktur) ----------
	logger := slog.New(slog.NewTextHandler(os.Stdout, nil))
	logger.Info("user login", "user_id", 7, "ip", "127.0.0.1")
	logger.Warn("disk hampir penuh", "sisa_persen", 8)

	// ---------- context ----------
	ctx, cancel := context.WithTimeout(context.Background(), 100*time.Millisecond)
	defer cancel()
	select {
	case <-time.After(time.Second):
		fmt.Println("selesai")
	case <-ctx.Done():
		fmt.Println("dibatalkan:", ctx.Err())
	}
}
```

Jalankan:
```powershell
go run ./contoh-kode/01-stdlib argumen1 argumen2
```

## Output yang Diharapkan (sebagian)
```
Nama: Sari, Umur: 20, IPK: 3.76
00042|kiri    |   kanan|
...
error: strconv.Atoi: parsing "abc": invalid syntax
...
{"nama":"Budi","umur":21,"hobi":["main bola","coding"]}
...
simpan data gagal: koneksi putus true
Argumen program: [argumen1 argumen2]
...
time=2026-10-05T11:17:39.139+07:00 level=INFO msg="user login" user_id=7 ip=127.0.0.1
dibatalkan: context deadline exceeded
```
Waktu dan tanggal akan berbeda di komputer Anda.

## Kesalahan Umum
- Mengabaikan error dengan `_` pada hal penting (misalnya `Atoi`) -> bug sulit dilacak.
- Field struct berhuruf kecil tidak ikut di JSON (harus **diekspor**, huruf besar).
- Menyambung string dengan `+` di dalam loop besar -> pakai `strings.Builder`.

## Latihan
1. Baca file teks per baris, hitung jumlah kata di tiap baris.
2. Simpan slice struct `Siswa` ke file `siswa.json` lalu baca kembali.

## Catatan Instruktur
Estimasi 90 menit (bagi dua sesi). Bukan untuk dihafal; ajarkan siswa membiasakan membaca dokumentasi di https://pkg.go.dev/std.
