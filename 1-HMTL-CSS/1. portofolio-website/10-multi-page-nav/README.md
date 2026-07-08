# Multi-Page Navigation — Level 10

Dari folder 1 sampai 9, website kita cuma **satu file HTML** — nav-nya pakai `#anchor` yang scroll ke section dalam halaman yang sama. Stage ini beda: **beberapa file HTML terpisah**, dan nav pindah antar file beneran.

Buka `index.html`, klik nav-nya, perhatikan **URL di browser berubah**.

---

## Beda Anchor Nav vs Multi-Page Nav

```
ANCHOR NAV (folder 1-9):                    MULTI-PAGE NAV (folder ini):
─────────────────────────────               ─────────────────────────────
Satu file: index.html                       Tiga file terpisah:
                                             index.html, about.html, contact.html

<a href="#about">About</a>                  <a href="about.html">About</a>
      │                                            │
      ▼                                            ▼
Browser SCROLL ke <section id="about">      Browser BUKA FILE about.html
di halaman yang sama                        (halaman benar-benar berganti)

URL jadi: page.html#about                   URL jadi: about.html
```

Keduanya valid, dipakai buat kebutuhan beda:
- **Anchor** (`#section`) → cocok kalau semua konten muat di satu halaman (portfolio kecil)
- **Multi-page** (`about.html`) → cocok kalau tiap halaman punya konten banyak/beda topik (blog, toko online, company profile)

---

## 1. Link Antar File — href relative path

```
Struktur folder:
10-multi-page-nav/
├── index.html
├── about.html
├── contact.html
├── style.css
└── script.js
```

```html
<!-- di dalam index.html -->
<a href="about.html">About</a>
<!-- artinya: "buka file about.html yang ada di folder SAMA dengan file ini" -->
```

Kalau struktur foldernya lebih dalam:

```
href="about.html"        → file di folder yang sama
href="pages/about.html"  → masuk ke folder "pages", cari about.html di situ
href="../index.html"     → naik SATU folder ke atas, cari index.html
```

---

## 2. Shared Header/Nav — Kenapa Di-copy ke Tiap File?

```
index.html                about.html                 contact.html
┌───────────────┐         ┌───────────────┐          ┌───────────────┐
│ <header>      │         │ <header>      │          │ <header>      │
│  <nav>...     │  SAMA   │  <nav>...     │  SAMA    │  <nav>...     │
│ </header>     │ ◀─────▶ │ </header>     │ ◀──────▶ │ </header>     │
│               │         │               │          │               │
│ konten Home   │         │ konten About  │          │ konten Contact│
└───────────────┘         └───────────────┘          └───────────────┘
```

HTML murni **tidak punya** cara "import" komponen antar file — jadi header/nav yang sama harus di-copy manual ke tiap file HTML. (Ini salah satu alasan framework kayak React/Next.js ada — biar nav cukup ditulis sekali, dipakai di semua halaman otomatis. Tapi itu belajar nanti.)

---

## 3. Active Link — Kasih Tahu User Lagi di Halaman Mana

```js
// window.location.pathname = alamat file yang lagi dibuka
// contoh: "/10-multi-page-nav/about.html"

const halamanSekarang = window.location.pathname.split("/").pop();
// split("/") → ["", "10-multi-page-nav", "about.html"]
// .pop()     → ambil elemen TERAKHIR = "about.html"

document.querySelectorAll("nav a").forEach(function (link) {
  if (link.getAttribute("href") === halamanSekarang) {
    link.classList.add("active"); // kasih style beda buat link ini
  }
});
```

Hasilnya: link nav yang sesuai halaman yang lagi dibuka dapat garis bawah biru — user tahu posisi dia di mana.

---

## Ringkasan — Cheat Sheet

```
┌─────────────────────────────────────────────────────────────┐
│  LINK ANTAR FILE                                              │
│  <a href="nama-file.html">Teks</a>   → pindah halaman         │
│  href="../file.html"                 → naik satu folder        │
│  href="folder/file.html"             → masuk folder            │
├─────────────────────────────────────────────────────────────┤
│  DETEKSI HALAMAN AKTIF                                        │
│  window.location.pathname            → path lengkap URL        │
│  path.split("/").pop()               → ambil nama file terakhir│
└─────────────────────────────────────────────────────────────┘
```

---

## Coba Sendiri

1. Buka `index.html` → klik About, Contact → perhatikan URL berubah tiap kali
2. Perhatikan link nav yang aktif dapat garis bawah biru sesuai halaman
3. Buka DevTools (F12) → tab Elements → cari `<nav>` → lihat class `active` nempel di link yang bener
4. Coba bikin file baru `projects.html` — copy struktur dari `about.html`, ganti kontennya, tambahkan link ke nav di SEMUA file (index, about, contact, projects)
5. **Catatan**: kalau buka file langsung pakai double-click (`file://...`), fitur active-link kadang nggak sempurna di beberapa browser karena cara baca `pathname`-nya beda. Ini normal — nanti pas belajar server sungguhan ini otomatis rapi.

---

## Apa Selanjutnya?

**Stage 11 — Todo List (capstone)**: gabungin semua yang sudah dipelajari dari Stage 1-10 — HTML/CSS/JS, DOM, events, localStorage — jadi 1 aplikasi kecil yang beneran berfungsi.
