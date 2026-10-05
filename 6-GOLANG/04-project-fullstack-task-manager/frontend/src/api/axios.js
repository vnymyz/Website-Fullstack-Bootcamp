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
