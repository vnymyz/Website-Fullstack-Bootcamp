# Sesi 2 — JSX & Component

Tujuan: paham aturan sintaks JSX, cara bikin & nyusun component, dan setup Tailwind di `learn-react/`.

**Hasil akhir sesi ini:**
- Tailwind jalan di `learn-react/`.
- Ada 3 component (`Navbar`, `Card`, `Footer`) di `src/components/`, disusun di `App.jsx`.
- Kamu ngerti 3 aturan JSX: `className`, satu root element, nama component PascalCase.

**Sebelum mulai:** `npm run dev` dari sesi 1 harus bisa jalan di `localhost:5173`.

**Peta langkah:**

| Langkah | Isi |
|---|---|
| Konsep | 3 aturan JSX yang sering bikin bingung |
| 1 | Pasang Tailwind |
| 2 | Bikin `Navbar`, `Card`, `Footer` |
| 3 | Susun di `App.jsx` |
| Latihan | Rebuild portofolio jadi component |

---

## Konsep: 3 Aturan JSX

JSX = tulisan mirip HTML di dalam file JavaScript. Sebenernya ini JS dengan sintaks khusus. 3 aturan di bawah ini penyebab error paling sering di awal.

### Aturan 1 — `className`, bukan `class`

Di JSX, `class` gak bisa dipakai karena itu keyword reserved di JavaScript (buat bikin class OOP). Makanya React pakai `className`. Sama halnya `for` di `<label for="...">` HTML jadi `htmlFor` di JSX.

```jsx
<div className="p-4">   {/* benar */}
<div class="p-4">        {/* salah: muncul warning di Console */}
```

### Aturan 2 — Satu Root Element (atau Fragment `<>...</>`)

JSX itu sebenernya dikompilasi jadi pemanggilan fungsi JS (`React.createElement(...)`). Sebuah fungsi cuma bisa `return` **satu nilai**. Jadi kalau kamu mau return beberapa elemen sejajar, harus dibungkus satu wadah — boleh `<div>`, boleh juga `<>...</>` (Fragment) kalau kamu gak mau nambah elemen HTML ekstra yang gak perlu.

```jsx
// SALAH -- dua elemen sejajar tanpa pembungkus
return (
  <h1>Halo</h1>
  <p>Dunia</p>
);

// BENAR
return (
  <>
    <h1>Halo</h1>
    <p>Dunia</p>
  </>
);
```

### Aturan 3 — Nama Component Harus PascalCase

React nentuin "ini tag HTML biasa atau component custom saya?" dari huruf pertama. Huruf besar (`<Navbar />`) = component kamu. Huruf kecil (`<navbar />`) = dianggap tag HTML bawaan browser, dan bakal error/gak ke-render bener karena `<navbar>` bukan tag HTML asli.

### Bonus — Kurung Kurawal `{}` = "keluar ke JavaScript"

Di dalam JSX, apa pun yang ada di `{ }` dianggap ekspresi JavaScript:

```jsx
const nama = "Vanya";
<h1>Halo, {nama}</h1>                    {/* Halo, Vanya */}
<p>{2 + 3}</p>                           {/* 5 */}
<p>© {new Date().getFullYear()}</p>      {/* © 2026 */}
```

---

## Langkah 1 — Setup Tailwind di `learn-react/`

Tailwind = kumpulan class CSS siap pakai (`p-4`, `text-white`, `flex`), jadi kamu gak nulis file CSS terpisah.

**1a.** Pasang Tailwind. Di terminal, dari folder `learn-react/`:

```
npm install -D tailwindcss @tailwindcss/vite
```

(`-D` = simpen sebagai devDependency, yaitu library yang cuma dipakai pas development.)

**1b.** Edit `vite.config.js`. Tambah 2 hal: import `tailwindcss`, dan masukin ke `plugins`:

```js
import { defineConfig } from "vite";
import react from "@vitejs/plugin-react";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
  plugins: [react(), tailwindcss()],
});
```

**1c.** Ganti isi `src/index.css` jadi satu baris ini (hapus semua isi lama):

```css
@import "tailwindcss";
```

**1d.** Pastikan `src/main.jsx` meng-import css-nya (biasanya udah ada dari template Vite):

```jsx
import "./index.css";
```

**1e.** **Restart** `npm run dev`: tekan `Ctrl+C` di terminal, lalu jalanin lagi. (Perubahan `vite.config.js` cuma kebaca saat server dinyalain.)

### Cek

Test cepat. Di `src/App.jsx`, ganti isinya sementara jadi:

```jsx
export default function App() {
  return <h1 className="text-3xl font-bold text-blue-600">Tailwind jalan!</h1>;
}
```

- [ ] Teksnya besar, tebal, dan biru → Tailwind aktif.
- [ ] Kalau teksnya masih kecil dan hitam biasa → lihat "Kalau Error" di bawah.

---

## Langkah 2 — Bikin Component Kecil

**Component = fungsi JavaScript biasa yang me-return JSX.** Nama fungsinya jadi nama tag-nya.

**2a.** Bikin folder `src/components/` (klik kanan `src` → New Folder).

**2b.** Bikin file `src/components/Navbar.jsx`:

```jsx
// Component = fungsi JS biasa yang return JSX.
export default function Navbar() {
  return (
    <nav className="flex items-center justify-between bg-slate-900 px-6 py-4 text-white">
      <span className="text-lg font-bold">Portofolio Vanya</span>
      <ul className="flex gap-4 text-sm">
        <li>Beranda</li>
        <li>Tentang</li>
        <li>Kontak</li>
      </ul>
    </nav>
  );
}
```

*Catatan: bandingin ini sama `<?php include 'navbar.php'; ?>` yang kamu pakai di PHP. Konsepnya sama (pecah halaman jadi potongan reusable), mekanismenya beda: PHP include isi file mentah-mentah, React manggil FUNGSI yang return JSX.*

**Baca per bagian:**

| Bagian | Artinya |
|---|---|
| `export default function Navbar()` | Bikin fungsi bernama `Navbar` dan "buka" supaya file lain bisa import |
| `return ( ... )` | Yang di-return = tampilan component-nya. Kurung `( )` biar bisa nulis JSX beberapa baris |
| `flex items-center justify-between` | Class Tailwind: layout flex, sejajar tengah, ujung kiri-kanan |
| `bg-slate-900 px-6 py-4 text-white` | Latar gelap, padding kiri-kanan & atas-bawah, teks putih |

**2c.** Bikin `src/components/Card.jsx`:

```jsx
// `children` itu prop spesial: apapun yang kamu taruh DI ANTARA
// <Card>...</Card> pas dipakai, otomatis masuk ke sini sebagai `children`.
export default function Card({ title, children }) {
  return (
    <div className="rounded-lg border border-slate-200 p-4 shadow-sm">
      <h3 className="mb-2 font-semibold text-slate-800">{title}</h3>
      <div className="text-sm text-slate-600">{children}</div>
    </div>
  );
}
```

**Baca per bagian:**

- `({ title, children })` — component nerima data lewat "props" (dibahas penuh di sesi 3). `title` = teks judul yang dikirim pemakai. `children` = isi di antara tag pembuka dan penutup.
- `{title}` dan `{children}` — tempat data itu ditampilkan.

**2d.** Bikin `src/components/Footer.jsx`:

```jsx
export default function Footer() {
  return (
    <footer className="mt-10 border-t border-slate-200 py-4 text-center text-xs text-slate-500">
      © {new Date().getFullYear()} Vanya. Dibuat pakai React.
    </footer>
  );
}
```

### Cek

- [ ] Ada 3 file di `src/components/`: `Navbar.jsx`, `Card.jsx`, `Footer.jsx`.
- [ ] Semua file diawali `export default function NamaPascalCase()`.
- [ ] Belum ada yang tampil di browser (belum dipakai di `App.jsx`) — itu normal.

---

## Langkah 3 — Susun di `App.jsx`

**3a.** Ganti seluruh isi `src/App.jsx`:

```jsx
import Navbar from "./components/Navbar.jsx";
import Card from "./components/Card.jsx";
import Footer from "./components/Footer.jsx";

export default function App() {
  return (
    <>
      <Navbar />
      <main className="mx-auto max-w-3xl px-6 py-8">
        <h1 className="mb-6 text-2xl font-bold text-slate-900">
          Belajar JSX & Component
        </h1>

        <div className="grid gap-4 sm:grid-cols-2">
          <Card title="HTML/CSS/JS">
            <ul className="list-inside list-disc">
              <li>Sudah paham DOM manipulation</li>
              <li>Sudah paham event listener</li>
            </ul>
          </Card>

          <Card title="PHP + MySQL">
            <ul className="list-inside list-disc">
              <li>Sudah paham CRUD</li>
              <li>Sudah paham session & auth</li>
            </ul>
          </Card>
        </div>
      </main>
      <Footer />
    </>
  );
}
```

**Baca per bagian:**

| Bagian | Artinya |
|---|---|
| `import Navbar from "./components/Navbar.jsx"` | Ambil component dari file lain. Tulis ekstensi `.jsx` lengkap. Awali `./` = "dari folder ini" |
| `<>...</>` | Fragment: pembungkus tak terlihat (aturan 2) |
| `<Navbar />` | Pakai component. Tag tanpa isi ditutup `/>` |
| `<Card title="...">...</Card>` | Pakai component dengan prop `title` dan `children` (isi di antara tag) |
| `sm:grid-cols-2` | Tailwind: 2 kolom kalau layar ≥ `sm`, 1 kolom kalau lebih kecil |

### Cek

- [ ] Halaman nampilin navbar gelap di atas, judul, dua card berdampingan, dan footer di bawah.
- [ ] Perkecil lebar jendela browser: dua card jadi tumpuk 1 kolom (responsive).
- [ ] Console (`F12`) bersih dari error merah.

---

## Latihan — Rebuild Portofolio jadi Component

Ambil halaman portofolio HTML statis kamu (di `1-HMTL-CSS/1. portofolio-website/`), pecah jadi component React di `learn-react/src/components/`.

**Langkah kerja yang disarankan** (satu component per kali, cek di browser tiap selesai satu):

1. `Header.jsx` — nama kamu + tagline. Pasang di `App.jsx`, cek tampil.
2. `SkillCard.jsx` — 1 card per skill, terima props `nama` dan `deskripsi`. Pasang 1 dulu, baru tambah sampai 3.
3. `ProjectCard.jsx` — 1 card per project, terima props `judul`, `gambar` (opsional), `deskripsi`.
4. `App.jsx` — susun semuanya, render beberapa `<SkillCard />` dan `<ProjectCard />` pakai props berbeda-beda.

Syarat:
- Semua styling pakai Tailwind class.
- Minimal 3 `SkillCard` dan 2 `ProjectCard`, propsnya beda-beda (jangan copy-paste isi yang sama).
- Pakai `children` di minimal 1 component.

*Props baru dibahas detail di sesi 3. Kalau bingung cara nerima props, lihat contoh `Card.jsx` di atas: `({ title, children })`.*

## Catatan buat Kamu

1. Kenapa `App.jsx` butuh `<> </>` (Fragment) buat bungkus `<Navbar/>`, `<main>`, dan `<Footer/>`? Coba hapus Fragment-nya, apa errornya?
2. Tambah 1 `<Card>` baru buat skill "Bootstrap & Tailwind".
3. Coba tulis `<navbar />` (huruf kecil) di `App.jsx`. Apa yang muncul di halaman dan di Console? Balikin lagi ke `<Navbar />`.

## Kalau Error

| Gejala | Biasanya penyebabnya |
|---|---|
| Component gak muncul / blank | Nama function bukan PascalCase, atau lupa `export default`, atau lupa di-import di `App.jsx` |
| Class Tailwind gak keefek | `npm run dev` belum di-restart setelah edit `vite.config.js`; atau `@import "tailwindcss";` belum ada di `index.css`; atau `index.css` belum di-import di `main.jsx` |
| `Adjacent JSX elements must be wrapped in an enclosing tag` | Return-nya ada 2 elemen sejajar tanpa pembungkus — tambah `<>...</>` |
| `Failed to resolve import "./components/Navbar.jsx"` | Salah nama file / salah folder / typo huruf besar-kecil |
| Warning `Invalid DOM property class` | Tulis `className`, bukan `class` |
| `Expected corresponding JSX closing tag` | Ada tag yang lupa ditutup (`<div>` tanpa `</div>`, atau `<Navbar>` tanpa `/`) |

Lanjut ke [03-props-dan-rendering-list.md](03-props-dan-rendering-list.md).
