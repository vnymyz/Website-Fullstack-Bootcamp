# Sesi 2 — JSX & Component

Tujuan: paham aturan sintaks JSX, cara bikin & nyusun component, dan setup Tailwind di `learn-react/`.

## Kenapa `className`, bukan `class`?

Di JSX, `class` gak bisa dipakai karena itu keyword reserved di JavaScript (buat bikin class OOP). Makanya React pakai `className`. Sama halnya `for` di `<label for="...">` HTML jadi `htmlFor` di JSX.

## Kenapa Harus Ada Satu Root Element (atau Fragment `<>...</>`)?

JSX itu sebenernya dikompilasi jadi pemanggilan fungsi JS (`React.createElement(...)`). Sebuah fungsi cuma bisa `return` **satu nilai**. Jadi kalau kamu mau return beberapa elemen sejajar, harus dibungkus satu wadah — boleh `<div>`, boleh juga `<>...</>` (Fragment) kalau kamu gak mau nambah elemen HTML ekstra yang gak perlu.

## Kenapa Nama Component Harus PascalCase?

React nentuin "ini tag HTML biasa atau component custom saya?" dari huruf pertama. Huruf besar (`<Navbar />`) = component kamu. Huruf kecil (`<navbar />`) = dianggap tag HTML bawaan browser, dan bakal error/gak ke-render bener karena `<navbar>` bukan tag HTML asli.

## Langkah 1 — Setup Tailwind di `learn-react/`

```
cd learn-react
npm install -D tailwindcss @tailwindcss/vite
```

Edit `vite.config.js`:

```js
import { defineConfig } from "vite";
import react from "@vitejs/plugin-react";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
  plugins: [react(), tailwindcss()],
});
```

Ganti isi `src/index.css` jadi:

```css
@import "tailwindcss";
```

Restart `npm run dev`. Sekarang kamu bisa pakai class Tailwind langsung di `className`.

## Langkah 2 — Bikin Component Kecil

Bikin folder `src/components/`, lalu file `src/components/Navbar.jsx`:

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

`src/components/Card.jsx`:

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

`src/components/Footer.jsx`:

```jsx
export default function Footer() {
  return (
    <footer className="mt-10 border-t border-slate-200 py-4 text-center text-xs text-slate-500">
      © {new Date().getFullYear()} Vanya. Dibuat pakai React.
    </footer>
  );
}
```

## Langkah 3 — Susun di `App.jsx`

Ganti isi `src/App.jsx`:

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

## Latihan — Rebuild Portofolio jadi Component

Ambil halaman portofolio HTML statis kamu (di `1-HMTL-CSS/1. portofolio-website/`), pecah jadi component React di `learn-react/src/components/`:

1. `Header.jsx` — nama kamu + tagline.
2. `SkillCard.jsx` — 1 card per skill, terima props `nama` dan `deskripsi`.
3. `ProjectCard.jsx` — 1 card per project, terima props `judul`, `gambar` (opsional), `deskripsi`.
4. `App.jsx` — susun semuanya, render beberapa `<SkillCard />` dan `<ProjectCard />` pakai props berbeda-beda.

Syarat:
- Semua styling pakai Tailwind class.
- Minimal 3 `SkillCard` dan 2 `ProjectCard`, propsnya beda-beda (jangan copy-paste isi yang sama).
- Pakai `children` di minimal 1 component.

## Catatan buat Kamu

1. Kenapa `App.jsx` butuh `<> </>` (Fragment) buat bungkus `<Navbar/>`, `<main>`, dan `<Footer/>`? Coba hapus Fragment-nya, apa errornya?
2. Tambah 1 `<Card>` baru buat skill "Bootstrap & Tailwind".
3. Coba render `<TaskCard judul="..." />` (bikin sendiri) tanpa kirim salah satu prop yang ada default value-nya — apa yang muncul?

## Troubleshooting

- **Component gak muncul / blank:** cek nama function-nya PascalCase, dan udah di-`export default`.
- **Class Tailwind gak keefek:** pastikan `npm run dev` masih jalan (Tailwind di-generate on-the-fly), dan `vite.config.js` udah bener.

Lanjut ke [03-props-dan-rendering-list.md](03-props-dan-rendering-list.md).
