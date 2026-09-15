import { useState, useEffect } from "react";

// Custom hook = fungsi biasa yang MANGGIL hook lain di dalamnya,
// terus di-reuse di banyak component. Ini "menarik keluar" logic
// loading/error/success yang tadinya kamu tulis manual tiap fetch.
export default function useFetch(url) {
  const [data, setData] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    let batal = false; // flag buat cleanup, jaga-jaga component unmount sebelum fetch selesai

    async function load() {
      try {
        setLoading(true);
        const res = await fetch(url);
        if (!res.ok) throw new Error("Gagal ambil data");
        const json = await res.json();
        if (!batal) setData(json);
      } catch (err) {
        if (!batal) setError(err.message);
      } finally {
        if (!batal) setLoading(false);
      }
    }

    load();

    return () => {
      batal = true; // cleanup function -- jalan pas component unmount / url berubah
    };
  }, [url]);

  return { data, loading, error };
}
