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
