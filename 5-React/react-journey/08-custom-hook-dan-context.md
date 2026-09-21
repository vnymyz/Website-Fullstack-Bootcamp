# Sesi 8 — Custom Hook & Context

Tujuan: nge-reuse logic antar component (**custom hook**), dan berbagi data ke banyak component tanpa "prop drilling" (**Context**).

Sesi ini kelihatan banyak istilah baru, tapi cuma ada **dua ide besar**. Kerjain berurutan, jangan loncat:

| Bagian | Ide | Masalah yang dipecahin |
|---|---|---|
| **A. Custom Hook** | Bungkus logic yang diulang-ulang jadi satu fungsi `useXxx` | Nulis loading/error/fetch yang sama berkali-kali |
| **B. Context** | "Papan pengumuman" yang bisa dibaca component mana pun | Ngirim data (misal "siapa yang login") lewat props berlapis-lapis |

Di akhir, kamu bakal punya alur login-logout yang jalan di `learn-react/`. Tiap langkah ada bagian **"Cek"** — jangan lanjut ke langkah berikutnya sebelum "Cek"-nya cocok.

---

## Rules of Hooks (Wajib Diikuti)

1. Hook (`useState`, `useEffect`, `useContext`, dan custom hook `useXxx`) cuma boleh dipanggil di **top level** function component — jangan di dalam `if`, `for`, atau function biasa yang nested.
2. Nama custom hook HARUS diawali `use` (contoh: `useFetch`) — ini konvensi, bukan aturan teknis, tapi React DevTools & linter bergantung ke penamaan ini.

**Kenapa rule 1 ada?** React nginget state kamu berdasarkan URUTAN hook dipanggil di tiap render (hook ke-1, ke-2, ke-3...). Kalau hook dipanggil di dalam `if`, urutannya bisa geser antar render, dan React "salah nyocokin" state ke hook.

```jsx
// SALAH -- hook di dalam if
function Contoh({ tampil }) {
  if (tampil) {
    const [x, setX] = useState(0); // urutan hook bisa berubah antar render
  }
}

// BENAR -- hook selalu di top level, kondisinya ditaruh SETELAH hook
function Contoh({ tampil }) {
  const [x, setX] = useState(0);
  if (!tampil) return null;
  return <p>{x}</p>;
}
```

---

# Bagian A — Custom Hook

## Konsep: Apa itu Custom Hook?

Ingat sesi 6, tiap kali fetch kamu nulis 3 state (`data`, `loading`, `error`) + `useEffect` + `try/catch/finally`. Kalau ada 5 halaman yang butuh fetch, kamu copy-paste blok itu 5 kali.

**Custom hook = fungsi biasa yang di dalamnya manggil hook lain**, lalu di-reuse. Itu aja. Gak ada sihir: kamu cuma "menarik keluar" blok yang diulang ke fungsi sendiri.

Analoginya: kalau kamu sering nulis 5 baris yang sama, kamu bikin fungsi. Custom hook itu fungsi yang isinya boleh pakai `useState`/`useEffect`.

## Langkah 1 — Custom Hook `useFetch`

### 1a. Bikin file

Bikin folder `src/hooks/`, lalu file `src/hooks/useFetch.js`:

```js
import { useState, useEffect } from "react";

// Custom hook = fungsi biasa yang MANGGIL hook lain di dalamnya,
// terus di-reuse di banyak component. Ini "menarik keluar" logic
// loading/error/success yang tadinya kamu tulis manual tiap fetch.
export default function useFetch(url) {
  const [data, setData] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    let batal = false; // flag buat cleanup, jaga-jaga component unmount sebelum fetch selesai

    async function load() {
      try {
        setLoading(true);
        const res = await fetch(url);
        if (!res.ok) throw new Error("Gagal ambil data");
        const json = await res.json();
        if (!batal) setData(json);
      } catch (err) {
        if (!batal) setError(err.message);
      } finally {
        if (!batal) setLoading(false);
      }
    }

    load();

    return () => {
      batal = true; // cleanup function -- jalan pas component unmount / url berubah
    };
  }, [url]);

  return { data, loading, error };
}
```

### 1b. Baca per bagian (jangan langsung copy tanpa ngerti)

| Bagian | Artinya |
|---|---|
| `useFetch(url)` | Terima URL sebagai parameter — inilah yang bikin hook ini bisa dipakai buat API apa aja |
| 3x `useState` | Sama persis kayak sesi 6, cuma sekarang tinggal di dalam hook |
| `[url]` di ujung `useEffect` | Fetch ulang kalau `url` berubah |
| `batal = true` di cleanup | Kalau component ilang sebelum fetch selesai, jangan update state (biar gak ada warning) |
| `return { data, loading, error }` | Hook "nyerahin" tiga nilai itu ke component yang manggil |

### 1c. Pakai di component

Bandingin sama fetch manual di sesi 6 — jauh lebih ringkas:

```jsx
import useFetch from "./hooks/useFetch.js";

function TaskList() {
  const { data: tasks, loading, error } = useFetch("http://localhost:3001/tasks");

  if (loading) return <p>Loading...</p>;
  if (error) return <p>Error: {error}</p>;
  return <ul>{tasks.map((t) => <li key={t.id}>{t.judul}</li>)}</ul>;
}
```

`{ data: tasks }` artinya "ambil `data`, tapi namain `tasks` di sini" (destructuring dengan rename).

### 1d. Cek

- [ ] `npm run api` jalan → daftar tugas muncul.
- [ ] Matiin `npm run api`, refresh → muncul `Error: ...`, bukan halaman blank.

> **Catatan:** hook ini cuma buat **ambil** data (GET). Tambah/toggle/hapus (POST/PATCH/DELETE) tetap fungsi terpisah kayak di sesi 6.

## Langkah 2 — Custom Hook `useLocalStorage`

Masalah: `useState` biasa hilang tiap refresh. `localStorage` nyimpen data di browser, tapi nulis `localStorage.getItem`/`setItem` manual tiap kali itu capek. Bikin hook-nya:

### 2a. Bikin file

`src/hooks/useLocalStorage.js`:

```js
import { useState } from "react";

export default function useLocalStorage(key, initialValue) {
  const [value, setValue] = useState(() => {
    const saved = localStorage.getItem(key);
    return saved ? JSON.parse(saved) : initialValue;
  });

  function setStoredValue(newValue) {
    setValue(newValue);
    localStorage.setItem(key, JSON.stringify(newValue));
  }

  return [value, setStoredValue];
}
```

### 2b. Baca per bagian

- `useState(() => {...})` — kasih **fungsi** ke `useState` (disebut *lazy initializer*): fungsi itu cuma jalan **sekali**, pas component pertama muncul. Jadi `localStorage` dibaca sekali, bukan tiap render.
- `JSON.parse` / `JSON.stringify` — `localStorage` cuma bisa nyimpen **string**. Object/array harus diubah dulu.
- `return [value, setStoredValue]` — array dua isi, biar cara pakainya persis kayak `useState`.

### 2c. Pakai

Persis kayak `useState` biasa, tapi otomatis ke-save:

```jsx
const [theme, setTheme] = useLocalStorage("theme", "light");
```

### 2d. Cek

1. Pakai hook ini di component apa aja (misal tombol ganti tema yang manggil `setTheme("dark")`).
2. Klik tombolnya, lalu **refresh** — nilainya harus tetap.
3. Buka DevTools → tab **Application** → **Local Storage** → lihat key `theme` beneran ada di sana.

---

# Bagian B — Context (Auth)

## Konsep: Apa Masalahnya?

Bayangin data "siapa yang login" dibutuhin di `Navbar`, `Dashboard`, dan `Profile`. Tanpa Context, `App` harus ngirim lewat props ke tiap component, dan kalau ada component perantara, dia juga harus ikut nerusin props yang bahkan gak dia pakai. Itu namanya **prop drilling**:

```
App (punya user)
 └─ Layout          <- gak butuh user, tapi harus nerusin
     └─ Header      <- gak butuh user, tapi harus nerusin
         └─ Navbar  <- baru ini yang beneran pakai user
```

**Context** = "papan pengumuman". Satu tempat naruh data, dan component **mana pun** di bawahnya bisa langsung baca tanpa lewat props.

## 4 Potongan yang Harus Kamu Kenali

Hampir semua Context punya 4 bagian ini. Hafalin polanya:

| # | Potongan | Fungsinya | Contoh |
|---|---|---|---|
| 1 | `createContext` | Bikin "papan pengumumannya" | `const AuthContext = createContext(null)` |
| 2 | `Provider` | Component yang **naruh** data di papan | `<AuthContext.Provider value={...}>` |
| 3 | `value` | Data + fungsi yang dipajang di papan | `{ user, login, logout }` |
| 4 | `useContext` | Component **baca** dari papan | `useContext(AuthContext)` |

Yang naruh = **Provider** (satu, di paling atas). Yang baca = **`useAuth()`** (banyak, di mana-mana).

## Langkah 3 — Bikin `AuthContext`

### 3a. Bikin file

Bikin folder `src/context/`, lalu `src/context/AuthContext.jsx`:

```jsx
import { createContext, useContext, useState } from "react";

const AuthContext = createContext(null);

export function AuthProvider({ children }) {
  const [user, setUser] = useState(null);

  function login(nama) {
    setUser({ nama });
  }

  function logout() {
    setUser(null);
  }

  return (
    <AuthContext.Provider value={{ user, login, logout }}>
      {children}
    </AuthContext.Provider>
  );
}

// Custom hook wrapper -- biar pemakaiannya `useAuth()`, bukan
// `useContext(AuthContext)` berulang-ulang di tiap file.
export function useAuth() {
  return useContext(AuthContext);
}
```

### 3b. Baca per bagian

- `useState(null)` — `user` mulai `null` = "belum ada yang login".
- `login(nama)` — set `user` jadi object `{ nama }`. Sekarang `user` gak `null` lagi = "sudah login".
- `logout()` — balikin `user` jadi `null`.
- `value={{ user, login, logout }}` — **dua kurung kurawal itu bukan typo**: yang luar = "ini JavaScript di dalam JSX", yang dalam = "ini object". Isi papan pengumuman: data (`user`) + dua fungsi (`login`, `logout`).
- `{children}` — apa pun yang dibungkus `<AuthProvider>...</AuthProvider>` bakal dirender di sini.
- `useAuth()` — cuma pembungkus tipis biar tulisnya pendek.

### 3c. Ada 2 export di 1 file — kenapa?

`AuthProvider` dan `useAuth` sama-sama `export function` (bukan `export default`), jadi importnya pakai kurung kurawal: `import { AuthProvider } from ...` dan `import { useAuth } from ...`.

### 3d. Cek

Belum ada yang bisa dilihat di browser (belum dipasang). Cukup pastikan file tersimpan dan gak ada garis merah di VS Code.

## Langkah 4 — Pasang Provider di `main.jsx`

Bungkus `App` dengan `AuthProvider`. **Urutan pembungkus penting:**

```jsx
import { StrictMode } from "react";
import { createRoot } from "react-dom/client";
import { BrowserRouter } from "react-router-dom";
import { AuthProvider } from "./context/AuthContext.jsx";
import "./index.css";
import App from "./App.jsx";

createRoot(document.getElementById("root")).render(
  <StrictMode>
    <BrowserRouter>
      <AuthProvider>
        <App />
      </AuthProvider>
    </BrowserRouter>
  </StrictMode>
);
```

**Aturan emasnya: component cuma bisa baca Context kalau dia ada DI DALAM Provider-nya.** `App` dan semua anaknya ada di dalam `<AuthProvider>`, jadi aman. Component yang di luar (atau gak dibungkus) dapat `null`.

Kalau `BrowserRouter` lupa dipasang, semua halaman blank dengan error `useRoutes() may be used only in the context of a <Router>` (dari sesi 7).

### Cek

Halaman tetap tampil normal seperti sebelumnya, gak ada error di Console.

## Langkah 5 — Baca Context di `Navbar`

Sekarang pakai papan pengumumannya. Di `src/components/Navbar.jsx`, tambahin bagian "Halo, nama / Logout":

```jsx
import { Link } from "react-router-dom";
import { useAuth } from "../context/AuthContext.jsx";

export default function Navbar() {
  const { user, logout } = useAuth();

  return (
    <nav className="flex items-center justify-between bg-slate-900 px-6 py-4 text-white">
      <span className="text-lg font-bold">Portofolio Vanya</span>
      <ul className="flex items-center gap-4 text-sm">
        <li><Link to="/">Beranda</Link></li>
        <li><Link to="/tasks">Tugas</Link></li>
        <li>
          {user ? (
            <span className="flex items-center gap-4">
              <span>Halo, {user.nama}</span>
              <button onClick={logout} className="text-red-500 hover:text-red-400">
                Logout
              </button>
            </span>
          ) : (
            <Link to="/login">Login</Link>
          )}
        </li>
      </ul>
    </nav>
  );
}
```

Perhatiin: `Navbar` **gak nerima props apa pun**, tapi tau siapa yang login. Itu inti Context.

`user ? (...) : (...)` = "kalau `user` ada (sudah login), tampilin bagian pertama; kalau `null`, tampilin bagian kedua".

### Cek

Buka halaman mana pun → di navbar kelihatan link **Login** (karena `user` masih `null`).

## Langkah 6 — Halaman Login Manggil `login()`

Di `src/pages/Login.jsx`, ini bagian yang **paling sering kelewat**. Kalau halaman login gak manggil `login()` dari context, `user` gak pernah keisi, dan navbar selamanya nampilin "Login".

```jsx
import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { useAuth } from "../context/AuthContext.jsx";

export default function Login() {
  const [username, setUsername] = useState("");
  const navigate = useNavigate();
  const { login } = useAuth(); // <- ambil fungsi login dari papan pengumuman

  function handleSubmit(e) {
    e.preventDefault();
    if (!username.trim()) return;
    login(username);          // <- ini yang bikin user keisi
    navigate("/dashboard");
  }

  return (
    <div className="p-6 max-w-sm mx-auto">
      <h1 className="text-2xl font-bold mb-4">Login</h1>
      <form onSubmit={handleSubmit} className="flex flex-col gap-3">
        <input
          type="text"
          placeholder="Username"
          value={username}
          onChange={(e) => setUsername(e.target.value)}
          className="border rounded px-3 py-2"
        />
        <button type="submit" className="bg-blue-600 text-white rounded px-3 py-2">
          Login
        </button>
      </form>
    </div>
  );
}
```

### Cek

Ketik nama di form, klik Login → pindah ke `/dashboard`, dan navbar sekarang nampilin **"Halo, {nama}"** + tombol **Logout**.

## Langkah 7 — `ProtectedRoute` Pakai `user` dari Context

Di sesi 7, `ProtectedRoute` pakai nilai palsu (`isLoggedIn = false`). Sekarang gantiin dengan data asli dari Context. Di `src/App.jsx`:

```jsx
import { Routes, Route, Navigate } from "react-router-dom";
import { useAuth } from "./context/AuthContext.jsx";
// ...import halaman lainnya...

// WAJIB ditulis DI LUAR function App -- kalau di dalam, dia dibikin ulang
// tiap App render dan state di dalamnya ke-reset (VS Code juga bakal nandain error).
function ProtectedRoute({ children }) {
  const { user } = useAuth();
  if (!user) return <Navigate to="/login" />;
  return children;
}

export default function App() {
  return (
    <Routes>
      <Route path="/" element={<Layout />}>
        <Route index element={<Home />} />
        <Route path="login" element={<Login />} />
        <Route
          path="dashboard"
          element={
            <ProtectedRoute>
              <Dashboard />
            </ProtectedRoute>
          }
        />
        {/* ...route lain... */}
      </Route>
    </Routes>
  );
}
```

**Yang dijaga cuma route yang dibungkus `<ProtectedRoute>`.** Route lain tetap terbuka buat siapa aja. Mau ngunci halaman lain? Bungkus juga dengan cara yang sama.

## Langkah 8 — Tes Alur Lengkap

Jalanin `npm run dev`, lalu cek satu per satu:

| # | Yang kamu lakuin | Yang harus terjadi |
|---|---|---|
| 1 | Buka `/dashboard` langsung (belum login) | Dilempar ke `/login` |
| 2 | Isi username, klik Login | Masuk `/dashboard`, navbar: "Halo, {nama}" |
| 3 | Pindah ke Beranda lewat link navbar | Navbar tetap "Halo, {nama}" (data nyangkut di Context) |
| 4 | Klik Logout | Navbar balik ke link "Login" |
| 5 | Buka `/dashboard` lagi | Dilempar ke `/login` lagi |

> **Penting:** kalau kamu ngetik URL manual atau refresh (F5), browser reload seluruh app → `user` balik `null` → kamu "logout" sendiri. Itu **normal di sesi ini**, karena `user` cuma disimpen di state (memori), bukan di `localStorage`. Solusinya (nyimpen token, dicek server) dibahas di **sesi 10**. Untuk tes langkah 3, pindah halaman **pakai link di navbar**, bukan ngetik URL.

## Kesalahan Umum & Artinya

| Gejala | Biasanya penyebabnya |
|---|---|
| `Cannot destructure property 'user' of useAuth() as it is null` | Component dipakai di luar `<AuthProvider>` (cek `main.jsx`) |
| Navbar selalu "Login" walau sudah submit | `Login.jsx` lupa manggil `login(username)` |
| Semua halaman blank + `useRoutes() may be used only in the context of a <Router>` | `<BrowserRouter>` belum dipasang di `main.jsx` |
| VS Code: "Components created during render will reset their state" | `ProtectedRoute` ditulis di dalam `App` — pindah ke luar |
| Bisa buka `/tasks` tanpa login | Normal — cuma route yang dibungkus `<ProtectedRoute>` yang dijaga |
| `does not provide an export named 'useAuth'` | Salah import: harus `{ useAuth }` (pakai kurung kurawal), bukan tanpa |

**Kapan JANGAN pakai Context:** Context itu bukan pengganti props secara umum. Kalau data cuma dipakai 1-2 level ke bawah, props biasa lebih simpel dan lebih gampang dilacak. Context cocok buat data yang beneran dibutuhin BANYAK component tersebar (auth, theme), bukan buat semua state.

---

# Bagian C — Dua Hook Tambahan

## `useRef` — Nilai yang Gak Bikin Re-render

```jsx
import { useRef } from "react";

function SearchBox() {
  const inputRef = useRef(null);

  function fokusInput() {
    inputRef.current.focus(); // akses elemen DOM asli
  }

  return (
    <>
      <input ref={inputRef} />
      <button onClick={fokusInput}>Fokus ke Input</button>
    </>
  );
}
```

Beda `useRef` sama `useState`: ubah `.current` di `useRef` **TIDAK** memicu re-render. Cocok buat nyimpen nilai yang perlu "diinget" tapi gak perlu ditampilin (contoh: id timer, elemen DOM, nilai render sebelumnya).

## Soal Jujur tentang `useMemo` / `useCallback`

Dua hook ini buat "cache" hasil kalkulasi/fungsi biar gak dibikin ulang tiap render. **Jangan sprinkle di mana-mana secara default** — mereka sendiri juga ada cost (nyimpen cache, bandingin dependency). Pakai HANYA kalau kamu udah ukur ada masalah performa nyata (misal pakai React DevTools Profiler), bukan "jaga-jaga siapa tau lambat".

---

## Checkpoint

Jelasin ke tutor pakai kata-katamu sendiri (tanpa lihat catatan):

1. Apa bedanya `createContext`, `Provider`, dan `useContext`? Siapa yang "naruh" dan siapa yang "baca"?
2. Kenapa `Navbar` bisa tau siapa yang login padahal gak dikasih props?
3. Kenapa `user` jadi `null` lagi kalau halaman di-refresh?

## Catatan buat Kamu

1. Ganti semua fetch manual di sesi 6 pakai `useFetch` — kodenya harusnya jadi lebih pendek.
2. Tambah `logout()` beneran manggil `useLocalStorage` buat hapus token (preview buat sesi 10).
3. Coba jelasin ke diri sendiri: kenapa custom hook HARUS dipanggil di top level component, gak boleh di dalam `if`?
4. Coba bungkus route `/tasks` dengan `<ProtectedRoute>` — apa yang berubah kalau kamu buka `/tasks` tanpa login?

Lanjut ke [09-project-taskflow-lite.md](09-project-taskflow-lite.md).
