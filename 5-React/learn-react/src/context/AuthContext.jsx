import { createContext, useContext, useState } from "react";

const AuthContext = createContext(null);
const API = "http://localhost:8000/api"; // sesuaikan port Laravel-mu

export function AuthProvider({ children }) {
  // BARU: nilai awal dibaca dari localStorage -- makanya refresh gak bikin logout.
  const [token, setToken] = useState(localStorage.getItem("token"));

  // BARU: login sekarang nanya server, dan melempar error kalau gagal
  async function login(email, password) {
    const res = await fetch(`${API}/login`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify({ email, password }),
    });
    if (!res.ok) throw new Error("Login gagal");
    const data = await res.json();
    setToken(data.token); // simpan di state (React langsung update)
    localStorage.setItem("token", data.token); // simpan di browser (awet saat refresh)
  }

  function logout() {
    setToken(null);
    localStorage.removeItem("token");
  }

  return (
    <AuthContext.Provider value={{ token, login, logout }}>
      {children}
    </AuthContext.Provider>
  );
}

export function useAuth() {
  return useContext(AuthContext);
}
