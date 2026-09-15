import { Outlet, Link } from "react-router-dom";
import Navbar from "./components/Navbar.jsx";

export default function Layout() {
  return (
    <div>
      <nav className="flex gap-4 bg-slate-900 p-4 text-white">
        <Link to="/">Beranda</Link>
        <Link to="/tasks">Tugas</Link>
      </nav>
      <Navbar />
      {/* Outlet = "slot" tempat halaman anak dirender */}
      <Outlet />
    </div>
  );
}
