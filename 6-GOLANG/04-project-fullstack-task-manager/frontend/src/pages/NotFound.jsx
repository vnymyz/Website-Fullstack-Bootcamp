import { Link } from 'react-router-dom'

export default function NotFound() {
  return (
    <div className="card">
      <h2>404 - Halaman tidak ditemukan</h2>
      <Link to="/">Kembali ke beranda</Link>
    </div>
  )
}
