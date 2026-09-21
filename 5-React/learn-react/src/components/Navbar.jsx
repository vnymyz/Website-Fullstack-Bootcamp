import { Link } from "react-router-dom";
import { useAuth } from "../context/AuthContext.jsx";

export default function Navbar() {
  const { user, logout } = useAuth();

  return (
    <nav className="flex items-center justify-between bg-slate-900 px-6 py-4 text-white">
      <span className="text-lg font-bold">Portofolio Vanya</span>
      <ul className="flex items-center gap-4 text-sm">
        <li>
          <Link to="/">Beranda</Link>
        </li>
        <li>
          <Link to="/tasks">Tugas</Link>
        </li>
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
