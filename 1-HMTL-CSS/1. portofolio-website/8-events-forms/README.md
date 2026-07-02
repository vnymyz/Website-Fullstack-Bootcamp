# Events & Forms + fetch() — Level 8

Ini stage tentang bikin form yang **beneran jalan bener** (nggak reload sembarangan) dan cara JavaScript **ambil data dari internet**.

Buka `index.html` di browser, coba semua tombol/form, terus baca penjelasan di bawah.

---

## Kenapa Stage Ini Penting?

Di Stage 7 kita pakai trik `type="button"` biar form nggak reload. Itu cara **darurat**. Stage ini kasih cara yang **benar**: `preventDefault()`.

Terus kita juga belajar `fetch()` — ini gerbang ke dunia nyata: cuaca, berita, data user, semuanya diambil pakai `fetch()`. Setelah ini, kamu punya semua bahan buat bikin project asli.

---

## 1. preventDefault() — Stop Form Reload

```
TANPA preventDefault():                DENGAN preventDefault():
──────────────────────────             ──────────────────────────
User klik Submit                       User klik Submit
      │                                       │
      ▼                                       ▼
Browser reload halaman                 e.preventDefault() jalan
(semua data JS hilang!)                      │
                                              ▼
                                        Halaman TETAP, JS lanjut jalan
```

```js
form.addEventListener("submit", function (e) {
  e.preventDefault();   // ← WAJIB baris pertama, sebelum kode lain
  // ...kode kamu di sini aman, halaman nggak reload
});
```

`e` = **event object**. Setiap event (`click`, `submit`, `input`, dll) otomatis kasih `e` ke function kamu. `e.preventDefault()` artinya: *"batalkan perilaku default browser buat event ini."*

---

## 2. Event "submit" vs Event "click"

```
Cara di Stage 7 (darurat):              Cara yang benar (Stage 8):
─────────────────────────────           ─────────────────────────────
<button type="button"                   <form id="form">
  onclick="cekForm()">                    <button type="submit">
                                         </form>
Cuma jalan kalau tombol diklik          form.addEventListener("submit", fn)
Enter di keyboard TIDAK jalan           Klik ATAU tekan Enter — dua-duanya jalan
```

```js
document.querySelector("#form-login").addEventListener("submit", function (e) {
  e.preventDefault();
  // cek input, tampilkan pesan, dll
});
```

---

## 3. fetch() — Minta Data dari Server Lain

Bayangkan `fetch()` kayak nelpon warung online:

```
Kamu (JavaScript)                    Server (API)
       │                                   │
       │──── fetch("url") ────────────────▶│   "Halo, minta data fakta kucing dong"
       │                                   │
       │◀─── response (mentah) ───────────│   server jawab, tapi bentuknya belum siap pakai
       │                                   │
   .json() ubah jadi object JS
       │
       ▼
  data siap dipakai: data.fact
```

```js
fetch("https://catfact.ninja/fact")
  .then((response) => response.json())   // langkah 1: ubah jadi object
  .then((data) => {
    console.log(data.fact);              // langkah 2: pakai datanya
  })
  .catch((error) => {
    console.log("Gagal:", error);        // kalau ada error
  });
```

`.then()` artinya *"terus/kemudian"* — karena `fetch()` butuh waktu (nunggu internet), JS nggak bisa langsung dapat hasilnya. `.then()` itu janji: *"kalau udah selesai, jalankan ini."*

---

## 4. async/await — Cara Nulis fetch() yang Lebih Rapi

`.then().then().catch()` kepanjangan kalau makin kompleks. `async/await` cara nulis yang sama tapi kebaca dari atas ke bawah kayak kode biasa.

```
.then() style:                          async/await style:
────────────────────────                ────────────────────────
fetch(url)                              async function ambilData() {
  .then(res => res.json())                const res = await fetch(url);
  .then(data => {                         const data = await res.json();
    console.log(data);                    console.log(data);
  });                                    }
```

```js
async function ambilSaran() {
  try {
    const response = await fetch("https://api.adviceslip.com/advice");
    const data = await response.json();
    console.log(data.slip.advice);
  } catch (error) {
    console.log("Gagal ambil saran:", error);
  }
}
```

- `async` di depan `function` = tandain function ini boleh pakai `await`
- `await` = "tunggu baris ini selesai dulu, baru lanjut ke baris berikutnya"
- `try { }` = kode yang mau dicoba
- `catch (error) { }` = kalau kode di `try` gagal (internet mati, server down), ini yang jalan — website nggak "diam" atau error di layar putih

---

## Ringkasan — Cheat Sheet

```
┌─────────────────────────────────────────────────────────────┐
│  FORM YANG BENAR                                             │
│  form.addEventListener("submit", fn)   → dengar submit form │
│  e.preventDefault()                    → stop reload        │
├─────────────────────────────────────────────────────────────┤
│  FETCH DASAR                                                 │
│  fetch(url)                            → minta data         │
│  .then(res => res.json())              → ubah jadi object   │
│  .then(data => ...)                    → pakai data-nya     │
│  .catch(err => ...)                    → tangkap error      │
├─────────────────────────────────────────────────────────────┤
│  FETCH DENGAN ASYNC/AWAIT                                    │
│  async function nama() { }             → tandain function   │
│  await fetch(url)                      → tunggu request     │
│  await response.json()                 → tunggu convert     │
│  try { } catch (error) { }             → tangani kegagalan  │
└─────────────────────────────────────────────────────────────┘
```

---

## Coba Sendiri

1. Buka `index.html` → coba semua form dan tombol fetch
2. Buka DevTools (F12) → tab **Network** → klik tombol fetch → lihat request yang muncul
3. Di Lesson 3, ganti URL `https://catfact.ninja/fact` jadi `https://dog.ceo/api/breeds/image/random` (API foto anjing random) — cek struktur data-nya beda, sesuaikan `data.fact` jadi `data.message`
4. Matikan WiFi sebentar, klik tombol fetch → lihat pesan error muncul (bukan website nge-freeze)
5. Di Lesson 2, coba tekan Enter di keyboard setelah isi form login — harus tetap jalan tanpa klik tombol

---

## Apa Selanjutnya?

Setelah ini, fundamental HTML + CSS + JS sudah **lengkap**:

```
HTML (struktur) → CSS (tampilan) → JS syntax → DOM → Events & fetch()
        ↑                                                    │
        └──────────────── SEMUA SUDAH DIKUASAI ──────────────┘
```

Next: pakai AI buat bantu bikin/percepat project portfolio asli. Karena fundamental sudah kuat, kamu bisa **baca dan ngerti** kode yang AI kasih — bukan cuma copy-paste tanpa tahu isinya.
