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
