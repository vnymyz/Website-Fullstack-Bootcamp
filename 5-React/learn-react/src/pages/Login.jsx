import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { useAuth } from "../context/AuthContext.jsx";

export default function Login() {
  const [username, setUsername] = useState("");
  const navigate = useNavigate();
  const { login } = useAuth(); // <- ambil fungsi login dari papan pengumuman

  function handleSubmit(e) {
    e.preventDefault();
    if (!username.trim()) return;
    login(username); // <- ini yang bikin user keisi
    navigate("/dashboard");
  }

  return (
    <div className="p-6 max-w-sm mx-auto">
      <h1 className="text-2xl font-bold mb-4">Login</h1>
      <form onSubmit={handleSubmit} className="flex flex-col gap-3">
        <input
          type="text"
          placeholder="Username"
          value={username}
          onChange={(e) => setUsername(e.target.value)}
          className="border rounded px-3 py-2"
        />
        <button
          type="submit"
          className="bg-blue-600 text-white rounded px-3 py-2"
        >
          Login
        </button>
      </form>
    </div>
  );
}
