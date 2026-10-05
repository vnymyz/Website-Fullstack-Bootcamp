# 14 - Halaman Login dan Register

## Tujuan Pembelajaran
- Membuat form terkontrol (*controlled component*) di React.
- Memanggil API, menangani loading dan error.
- Menyusun routing aplikasi.

## Langkah

### 1. `main.jsx`
Ganti isi `frontend/src/main.jsx`:

```jsx
// File: frontend/src/main.jsx
import { StrictMode } from 'react'
import { createRoot } from 'react-dom/client'
import { BrowserRouter } from 'react-router-dom'
import App from './App.jsx'
import { AuthProvider } from './context/AuthContext.jsx'
import './index.css'

createRoot(document.getElementById('root')).render(
  <StrictMode>
    <BrowserRouter>
      <AuthProvider>
        <App />
      </AuthProvider>
    </BrowserRouter>
  </StrictMode>,
)
```

Urutan pembungkus: `BrowserRouter` -> `AuthProvider` -> `App`. `AuthProvider` memakai router (untuk navigasi), jadi harus berada di dalamnya.

### 2. `App.jsx`
Ganti isi `frontend/src/App.jsx`:

```jsx
// File: frontend/src/App.jsx
import { Navigate, Route, Routes } from 'react-router-dom'
import Navbar from './components/Navbar.jsx'
import ProtectedRoute from './components/ProtectedRoute.jsx'
import Dashboard from './pages/Dashboard.jsx'
import Login from './pages/Login.jsx'
import NotFound from './pages/NotFound.jsx'
import Register from './pages/Register.jsx'

export default function App() {
  return (
    <>
      <Navbar />
      <main className="container">
        <Routes>
          <Route path="/" element={<Navigate to="/dashboard" replace />} />
          <Route path="/login" element={<Login />} />
          <Route path="/register" element={<Register />} />
          <Route
            path="/dashboard"
            element={
              <ProtectedRoute>
                <Dashboard />
              </ProtectedRoute>
            }
          />
          <Route path="*" element={<NotFound />} />
        </Routes>
      </main>
    </>
  )
}
```

### 3. Halaman Login
Buat `frontend/src/pages/Login.jsx`:

```jsx
// File: frontend/src/pages/Login.jsx
import { useState } from 'react'
import { Link, Navigate, useNavigate } from 'react-router-dom'
import { getErrorMessage } from '../api/axios.js'
import { useAuth } from '../context/AuthContext.jsx'

export default function Login() {
  const { user, login } = useAuth()
  const navigate = useNavigate()
  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')
  const [error, setError] = useState('')
  const [submitting, setSubmitting] = useState(false)

  if (user) return <Navigate to="/dashboard" replace />

  const handleSubmit = async (e) => {
    e.preventDefault()
    setError('')
    setSubmitting(true)
    try {
      await login(email, password)
      navigate('/dashboard')
    } catch (err) {
      setError(getErrorMessage(err))
    } finally {
      setSubmitting(false)
    }
  }

  return (
    <form className="card form auth-card" onSubmit={handleSubmit}>
      <h2>Masuk</h2>
      {error && <div className="alert">{error}</div>}

      <label>Email</label>
      <input type="email" value={email} onChange={(e) => setEmail(e.target.value)} required />

      <label>Password</label>
      <input type="password" value={password} onChange={(e) => setPassword(e.target.value)} required />

      <button className="btn" type="submit" disabled={submitting}>
        {submitting ? 'Memproses...' : 'Masuk'}
      </button>
      <p className="muted">
        Belum punya akun? <Link to="/register">Daftar</Link>
      </p>
    </form>
  )
}
```

**Penjelasan**
- `useState` menyimpan nilai input; `onChange` memperbaruinya (controlled component).
- `e.preventDefault()` mencegah form memuat ulang halaman.
- `try / catch / finally`: error ditampilkan, tombol dikembalikan aktif di `finally`.
- Jika user sudah login, `<Navigate>` mengalihkannya ke dashboard.

### 4. Halaman Register
Buat `frontend/src/pages/Register.jsx`:

```jsx
// File: frontend/src/pages/Register.jsx
import { useState } from 'react'
import { Link, Navigate, useNavigate } from 'react-router-dom'
import { getErrorMessage } from '../api/axios.js'
import { useAuth } from '../context/AuthContext.jsx'

export default function Register() {
  const { user, register } = useAuth()
  const navigate = useNavigate()
  const [form, setForm] = useState({ name: '', email: '', password: '' })
  const [error, setError] = useState('')
  const [submitting, setSubmitting] = useState(false)

  if (user) return <Navigate to="/dashboard" replace />

  const handleChange = (e) => setForm({ ...form, [e.target.name]: e.target.value })

  const handleSubmit = async (e) => {
    e.preventDefault()
    setError('')
    setSubmitting(true)
    try {
      await register(form.name, form.email, form.password)
      navigate('/dashboard')
    } catch (err) {
      setError(getErrorMessage(err))
    } finally {
      setSubmitting(false)
    }
  }

  return (
    <form className="card form auth-card" onSubmit={handleSubmit}>
      <h2>Daftar</h2>
      {error && <div className="alert">{error}</div>}

      <label>Nama</label>
      <input name="name" value={form.name} onChange={handleChange} required minLength={2} />

      <label>Email</label>
      <input type="email" name="email" value={form.email} onChange={handleChange} required />

      <label>Password (minimal 6 karakter)</label>
      <input type="password" name="password" value={form.password} onChange={handleChange} required minLength={6} />

      <button className="btn" type="submit" disabled={submitting}>
        {submitting ? 'Memproses...' : 'Daftar'}
      </button>
      <p className="muted">
        Sudah punya akun? <Link to="/login">Masuk</Link>
      </p>
    </form>
  )
}
```

### 5. Halaman 404
Buat `frontend/src/pages/NotFound.jsx`:

```jsx
// File: frontend/src/pages/NotFound.jsx
import { Link } from 'react-router-dom'

export default function NotFound() {
  return (
    <div className="card">
      <h2>404 - Halaman tidak ditemukan</h2>
      <Link to="/">Kembali ke beranda</Link>
    </div>
  )
}
```

### 6. CSS
Ganti seluruh isi `frontend/src/index.css`:

```css
/* File: frontend/src/index.css */
* { box-sizing: border-box; }

body {
  margin: 0;
  font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
  background: #f3f4f6;
  color: #1f2937;
}

.container { max-width: 960px; margin: 24px auto; padding: 0 16px; }

/* Navbar */
.navbar {
  display: flex; justify-content: space-between; align-items: center;
  padding: 12px 24px; background: #1e293b; color: #fff;
}
.navbar a { color: #e2e8f0; text-decoration: none; margin-left: 16px; }
.navbar .brand { font-weight: 700; font-size: 1.2rem; margin-left: 0; color: #fff; }
.nav-right { display: flex; align-items: center; gap: 12px; }

/* Card & form */
.card {
  background: #fff; border-radius: 8px; padding: 16px;
  margin-bottom: 16px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}
.form label { display: block; margin: 12px 0 4px; font-size: 0.9rem; font-weight: 600; }
.form input, .form textarea, .form select, .task select {
  width: 100%; padding: 8px 10px; border: 1px solid #d1d5db;
  border-radius: 6px; font: inherit;
}
.task select { width: auto; }
.auth-card { max-width: 400px; margin: 40px auto; }
.auth-card .btn { margin-top: 16px; width: 100%; }
.row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

/* Button */
.btn {
  background: #2563eb; color: #fff; border: 1px solid #2563eb;
  padding: 8px 14px; border-radius: 6px; cursor: pointer; font: inherit;
}
.btn:hover { background: #1d4ed8; }
.btn:disabled { opacity: 0.5; cursor: not-allowed; }
.btn-outline { background: transparent; color: #2563eb; }
.btn-outline:hover { background: #eff6ff; }
.navbar .btn-outline { color: #fff; border-color: #94a3b8; }
.navbar .btn-outline:hover { background: #334155; }
.btn-danger { background: #dc2626; border-color: #dc2626; }
.btn-danger:hover { background: #b91c1c; }
.actions { display: flex; gap: 8px; margin-top: 12px; flex-wrap: wrap; }

/* Dashboard */
.dashboard { display: grid; grid-template-columns: 1fr 1.6fr; gap: 24px; align-items: start; }
@media (max-width: 800px) { .dashboard { grid-template-columns: 1fr; } }

.filters { display: flex; gap: 8px; margin-bottom: 16px; flex-wrap: wrap; }
.chip {
  border: 1px solid #d1d5db; background: #fff; padding: 6px 12px;
  border-radius: 999px; cursor: pointer; font: inherit;
}
.chip-active { background: #2563eb; color: #fff; border-color: #2563eb; }

/* Task */
.task-head { display: flex; justify-content: space-between; align-items: center; gap: 8px; }
.task h4 { margin: 0; }
.task h4.done { text-decoration: line-through; color: #9ca3af; }
.muted { color: #6b7280; margin: 6px 0 0; }
.badge { padding: 2px 10px; border-radius: 999px; font-size: 0.75rem; white-space: nowrap; }
.badge-todo { background: #e5e7eb; }
.badge-in_progress { background: #fef3c7; color: #92400e; }
.badge-done { background: #d1fae5; color: #065f46; }

.alert { background: #fee2e2; color: #991b1b; padding: 10px 12px; border-radius: 6px; margin-bottom: 12px; }
.pagination { display: flex; justify-content: space-between; align-items: center; margin-top: 8px; }
```

> Dashboard belum ada, sehingga setelah login Anda akan melihat error import. Lanjut ke langkah 15, atau buat sementara `Dashboard.jsx` berisi `export default () => <h1>Dashboard</h1>`.

## Cek Hasil
Dengan backend berjalan, buka `http://localhost:5173/register`, daftar akun, dan pastikan Anda diarahkan ke `/dashboard`.

## Kesalahan Umum
- `Network Error` / `ECONNREFUSED` di terminal Vite -> backend tidak berjalan.
- Form tidak mengirim -> pastikan tombol `type="submit"` berada di dalam `<form>`.

## Latihan
Tambahkan input "Konfirmasi Password" di Register dan tampilkan error jika berbeda sebelum memanggil API.

## Catatan Instruktur
Estimasi 60 menit. Minta siswa membuka tab **Network** di DevTools untuk melihat request/response asli.
