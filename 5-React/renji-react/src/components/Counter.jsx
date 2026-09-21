import { useState } from "react";

export default function Counter() {
  const [count, setCount] = useState(0);
  // useState(0) -> nilai awal 0. Return array 2 elemen:
  // [nilai_sekarang, fungsi_buat_ubah_nilai].

  function naikkan() {
    setCount(count + 1);
  }

  return (
    <div className="rounded-lg border p-4 text-center">
      <p className="mb-2 text-3xl font-bold">{count}</p>
      <button
        onClick={naikkan}
        className="rounded mt-5 bg-blue-600 px-4 py-1 text-white"
      >
        Tambah
      </button>
    </div>
  );
}
