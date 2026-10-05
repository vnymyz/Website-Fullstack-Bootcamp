# 13 - Axios dan Auth Context

## Tujuan Pembelajaran
- Membuat instance Axios dengan interceptor.
- Menyimpan status login global memakai React Context.
- Membuat komponen `ProtectedRoute`.

## Langkah

### 1. Instance Axios
Buat `frontend/src/api/axios.js`:

```js
// File: frontend/src/api/axios.js
import axios from 'axios'

const TOKEN_KEY = 'token'

export const getToken = () => localStorage.getItem(TOKEN_KEY)
export const setToken = (token) => localStorage.setItem(TOKEN_KEY, token)
export const clearToken = () => localStorage.removeItem(TOKEN_KEY)

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || '/api',
  headers: { 'Content-Type': 'application/json' },
})

// Sebelum request dikirim: tempelkan token jika ada.
api.interceptors.request.use((config) => {
  const token = getToken()
  if (token) config.headers.Authorization = `Bearer ${token}`
  return config
})

// Setelah response diterima: jika 401 (token salah/kedaluwarsa), logout otomatis.
api.interceptors.response.use(
  (res) => res,
  (error) => {
    const isAuthEndpoint = error.config?.url?.startsWith('/auth/login') ||
      error.config?.url?.startsWith('/auth/register')
    if (error.response?.status === 401 && !isAuthEndpoint) {
      clearToken()
      window.location.href = '/login'
    }
    return Promise.reject(error)
  },
)

// Ambil pesan error yang ramah dari response backend.
export function getErrorMessage(error) {
  return error.response?.data?.message || 'Tidak dapat terhubung ke server'
}

export default api
```

**Penjelasan**
- `baseURL: '/api'` + proxy Vite = semua request ke backend.
- **Request interceptor**: sebelum request dikirim, token dari `localStorage` ditempel ke header `Authorization`.
- **Response interceptor**: jika backend membalas **401** (token kedaluwarsa), token dihapus dan user diarahkan ke `/login`. Endpoint login/register dikecualikan karena 401 di sana berarti password salah.
- `getErrorMessage` mengambil `message` dari response backend agar bisa ditampilkan ke user.

> **Catatan keamanan:** menyimpan token di `localStorage` mudah, tetapi rentan jika situs terkena XSS. Alternatif yang lebih aman: cookie `httpOnly` (dibahas di `18-pengembangan-lanjutan.md`).

### 2. Fungsi API
`frontend/src/api/auth.js`:

```js
// File: frontend/src/api/auth.js
import api from './axios.js'

export const registerRequest = (data) => api.post('/auth/register', data)
export const loginRequest = (data) => api.post('/auth/login', data)
export const meRequest = () => api.get('/auth/me')
```

`frontend/src/api/tasks.js`:

```js
// File: frontend/src/api/tasks.js
import api from './axios.js'

export const getTasks = (params) => api.get('/tasks', { params })
export const createTask = (data) => api.post('/tasks', data)
export const updateTask = (id, data) => api.put(`/tasks/${id}`, data)
export const deleteTask = (id) => api.delete(`/tasks/${id}`)
```

Memisahkan pemanggilan API dari komponen membuat komponen lebih bersih dan API mudah diganti.

### 3. Auth Context
Buat `frontend/src/context/AuthContext.jsx`:

```jsx
// File: frontend/src/context/AuthContext.jsx
import { createContext, useCallback, useContext, useEffect, useState } from 'react'
import { loginRequest, meRequest, registerRequest } from '../api/auth.js'
import { clearToken, getToken, setToken } from '../api/axios.js'

const AuthContext = createContext(null)

export function AuthProvider({ children }) {
  const [user, setUser] = useState(null)
  const [loading, setLoading] = useState(true)

  // Saat aplikasi dibuka: jika ada token, ambil data user.
  useEffect(() => {
    if (!getToken()) {
      setLoading(false)
      return
    }
    meRequest()
      .then((res) => setUser(res.data.data))
      .catch(() => clearToken())
      .finally(() => setLoading(false))
  }, [])

  const login = useCallback(async (email, password) => {
    const res = await loginRequest({ email, password })
    setToken(res.data.data.token)
    setUser(res.data.data.user)
  }, [])

  const register = useCallback(async (name, email, password) => {
    const res = await registerRequest({ name, email, password })
    setToken(res.data.data.token)
    setUser(res.data.data.user)
  }, [])

  const logout = useCallback(() => {
    clearToken()
    setUser(null)
  }, [])

  return (
    <AuthContext.Provider value={{ user, loading, login, register, logout }}>
      {children}
    </AuthContext.Provider>
  )
}

// eslint-disable-next-line react-refresh/only-export-components
export function useAuth() {
  const ctx = useContext(AuthContext)
  if (!ctx) throw new Error('useAuth harus dipakai di dalam AuthProvider')
  return ctx
}
```

**Penjelasan**
- **Context** membagikan data (`user`, `login`, `logout`) ke seluruh komponen tanpa *props drilling*.
- `useEffect` saat aplikasi dibuka: jika ada token, panggil `/auth/me` untuk memulihkan sesi setelah halaman di-refresh.
- `loading` mencegah halaman "berkedip" ke `/login` sebelum pengecekan token selesai.
- `useCallback` menjaga identitas fungsi tetap stabil antar render.
- Hook `useAuth()` membungkus `useContext` plus pengecekan error.

### 4. ProtectedRoute
Buat `frontend/src/components/ProtectedRoute.jsx`:

```jsx
// File: frontend/src/components/ProtectedRoute.jsx
import { Navigate } from 'react-router-dom'
import { useAuth } from '../context/AuthContext.jsx'

export default function ProtectedRoute({ children }) {
  const { user, loading } = useAuth()

  if (loading) return <p>Memuat...</p>
  if (!user) return <Navigate to="/login" replace />
  return children
}
```

Jika belum login, user dialihkan ke `/login`. `replace` mengganti riwayat agar tombol Back tidak kembali ke halaman terlarang.

### 5. Navbar
Buat `frontend/src/components/Navbar.jsx`:

```jsx
// File: frontend/src/components/Navbar.jsx
import { Link, useNavigate } from 'react-router-dom'
import { useAuth } from '../context/AuthContext.jsx'

export default function Navbar() {
  const { user, logout } = useAuth()
  const navigate = useNavigate()

  const handleLogout = () => {
    logout()
    navigate('/login')
  }

  return (
    <nav className="navbar">
      <Link to="/" className="brand">Task Manager</Link>
      <div className="nav-right">
        {user ? (
          <>
            <span>Halo, {user.name}</span>
            <button className="btn btn-outline" onClick={handleLogout}>Keluar</button>
          </>
        ) : (
          <>
            <Link to="/login">Masuk</Link>
            <Link to="/register">Daftar</Link>
          </>
        )}
      </div>
    </nav>
  )
}
```

## Cek Hasil
Belum ada tampilan baru; kita rangkai di langkah 14.

## Kesalahan Umum
- `useAuth harus dipakai di dalam AuthProvider` -> `AuthProvider` belum membungkus `<App />` di `main.jsx`.
- Token tidak terkirim -> cek nama key `localStorage` (`token`) konsisten.

## Latihan
Tambahkan interceptor yang mencetak `console.log` untuk setiap request (method dan URL). Hapus lagi setelah selesai.

## Catatan Instruktur
Estimasi 60 menit. Konsep Context dan interceptor biasanya baru bagi siswa; gambarkan alurnya.
