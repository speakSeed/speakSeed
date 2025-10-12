import { defineConfig } from "vite";
import vue from "@vitejs/plugin-vue";
import path from "path";

// https://vite.dev/config/
export default defineConfig(({ command }) => ({
  plugins: [vue()],
  resolve: {
    alias: {
      "@": path.resolve(__dirname, "./src"),
    },
  },
  server: {
    port: 5173,
    strictPort: false,
    host: true,
    hmr: {
      protocol: 'wss',
      host: typeof process !== 'undefined' && process.env.VITE_HMR_HOST,
      port: 443,
    },
  },
  build: {
    target: "esnext",
  },
  // Allow all origins in development
  preview: {
    host: true,
  },
}));
