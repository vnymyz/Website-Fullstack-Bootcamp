# Sesi 8 — Custom Hook & Context

Tujuan: nge-reuse logic antar component, dan berbagi state tanpa "prop drilling" ke mana-mana.

## Rules of Hooks (Wajib Diikuti)

1. Hook (`useState`, `useEffect`, dll) cuma boleh dipanggil di **top level** function component — jangan di dalam `if`, `for`, atau function biasa yang nested.
2. Nama custom hook HARUS diawali `use` (contoh: `useFetch`) — ini konvensi, bukan aturan teknis, tapi React DevTools & linter bergantung ke penamaan ini.

## Langkah 1 — Custom Hook `useFetch`

`src/hooks/useFetch.js`:

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

Pemakaian (bandingin sama fetch manual di sesi 6 — jauh lebih ringkas):

```jsx
import useFetch from "./hooks/useFetch.js";

function TaskList() {
  const { data: tasks, loading, error } = useFetch("http://localhost:3001/tasks");

  if (loading) return <p>Loading...</p>;
  if (error) return <p>Error: {error}</p>;
  return <ul>{tasks.map((t) => <li key={t.id}>{t.judul}</li>)}</ul>;
}
```

## Langkah 2 — Custom Hook `useLocalStorage`

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

Pemakaian, persis kayak `useState` biasa tapi otomatis ke-save ke localStorage:

```jsx
const [theme, setTheme] = useLocalStorage("theme", "light");
```

## Langkah 3 — `useContext` buat Auth

Tanpa Context, data auth ("siapa yang login") harus dikirim lewat props dari component paling atas turun ke semua anak yang butuh — capek dan berantakan (disebut "prop drilling"). Context nyediain cara "siram" data ke semua component di bawahnya tanpa lewat props satu-satu.

`src/context/AuthContext.jsx`:

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

Bungkus `App` dengan `AuthProvider` di `main.jsx`:

```jsx
import { AuthProvider } from "./context/AuthContext.jsx";

createRoot(document.getElementById("root")).render(
  <StrictMode>
    <AuthProvider>
      <App />
    </AuthProvider>
  </StrictMode>
);
```

Pemakaian di component manapun, tanpa props:

```jsx
import { useAuth } from "./context/AuthContext.jsx";

function Navbar() {
  const { user, logout } = useAuth();
  return user ? (
    <div>Halo, {user.nama} <button onClick={logout}>Logout</button></div>
  ) : (
    <div>Belum login</div>
  );
}
```

**Kapan JANGAN pakai Context:** Context itu bukan pengganti props secara umum. Kalau data cuma dipakai 1-2 level ke bawah, props biasa lebih simpel dan lebih gampang dilacak. Context cocok buat data yang beneran dibutuhin BANYAK component tersebar (auth, theme), bukan buat semua state.

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

## Catatan buat Kamu

1. Ganti semua fetch manual di sesi 6 pakai `useFetch` — kodenya harusnya jadi lebih pendek.
2. Tambah `logout()` beneran manggil `useLocalStorage` buat hapus token (preview buat sesi 10).
3. Coba jelasin ke diri sendiri: kenapa custom hook HARUS dipanggil di top level component, gak boleh di dalam `if`?

Lanjut ke [09-project-taskflow-lite.md](09-project-taskflow-lite.md).
