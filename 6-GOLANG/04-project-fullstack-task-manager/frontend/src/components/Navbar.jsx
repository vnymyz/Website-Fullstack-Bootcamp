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
