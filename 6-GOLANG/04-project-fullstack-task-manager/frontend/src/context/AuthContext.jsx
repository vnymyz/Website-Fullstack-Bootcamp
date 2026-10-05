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
