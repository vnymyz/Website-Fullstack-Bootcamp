// Component = fungsi JS biasa yang return JSX.
export default function Navbar() {
  return (
    <nav className="flex items-center justify-between bg-slate-900 px-6 py-4 text-white">
      <span className="text-lg font-bold">Portofolio Vanya</span>
      <ul className="flex gap-4 text-sm">
        <li>Beranda</li>
        <li>Tentang</li>
        <li>Kontak</li>
      </ul>
    </nav>
  );
}
