import { Routes, Route, Navigate } from "react-router-dom";
import { useAuth } from "./context/AuthContext.jsx";
import Layout from "./Layout.jsx";
import Login from "./pages/Login.jsx";
import NotesPage from "./pages/NotesPage.jsx"; // BARU: dibikin di langkah 9

// Ditulis DI LUAR App
function ProtectedRoute({ children }) {
  const { token } = useAuth(); // BARU: token, bukan user
  if (!token) return <Navigate to="/login" />; // belum login -> tendang
  return children;
}

export default function App() {
  return (
    <Routes>
      <Route path="/" element={<Layout />}>
        <Route index element={<Navigate to="/notes" />} />
        <Route path="login" element={<Login />} />
        <Route
          path="notes"
          element={
            <ProtectedRoute>
              <NotesPage />
            </ProtectedRoute>
          }
        />
      </Route>
    </Routes>
  );
}
