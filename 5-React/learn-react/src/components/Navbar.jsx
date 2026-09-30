import { Link } from "react-router-dom";
import { useAuth } from "../context/AuthContext.jsx";

export default function Navbar() {
  const { token, logout } = useAuth(); // BARU: token, bukan user

  return (
    <nav className="flex items-center justify-between bg-slate-900 px-6 py-4 text-white">
      <span className="text-lg font-bold">Portofolio Vanya</span>
      <ul className="flex items-center gap-4 text-sm">
        <li>
          <Link to="/notes">Catatan</Link>
        </li>
        <li>
          {token ? (
            <button
              onClick={logout}
              className="text-red-500 hover:text-red-400"
            >
              Logout
            </button>
          ) : (
            <Link to="/login">Login</Link>
          )}
        </li>
      </ul>
    </nav>
  );
}
