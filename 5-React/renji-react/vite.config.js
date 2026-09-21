import { defineConfig } from "vite";
import react from "@vitejs/plugin-react";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
  plugins: [react(), tailwindcss()],
  server: {
    // json-server nulis ke db.json tiap add/toggle/hapus; tanpa ini Vite
    // nganggep itu perubahan kode dan reload seluruh halaman.
    watch: { ignored: ["**/db.json"] },
  },
});
