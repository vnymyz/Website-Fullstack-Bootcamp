import { createContext, useContext, useState } from "react";

const AuthContext = createContext(null);

export function AuthProvider({ children }) {
  const [user, setUser] = useState(null);

  function login(nama) {
    setUser({ nama });
  }

  function logout() {
    setUser(null);
  }

  return (
    <AuthContext.Provider value={{ user, login, logout }}>
      {children}
    </AuthContext.Provider>
  );
}

// Custom hook wrapper -- biar pemakaiannya `useAuth()`, bukan
// `useContext(AuthContext)` berulang-ulang di tiap file.
export function useAuth() {
  return useContext(AuthContext);
}
