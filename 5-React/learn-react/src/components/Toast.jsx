import { useEffect } from "react";

export default function Toast({ message, onDone }) {
  useEffect(() => {
    if (!message) return; // gak ada pesan = gak usah pasang timer
    const timer = setTimeout(onDone, 2000);
    return () => clearTimeout(timer); // cleanup -- penting biar gak numpuk timer
  }, [message, onDone]);

  if (!message) return null;
  return (
    <div className="fixed bottom-4 right-4 rounded bg-slate-900 px-4 py-2 text-white">
      {message}
    </div>
  );
}
