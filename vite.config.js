import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    base: "/",
    resolve: {
        alias: {
            "@": fileURLToPath(new URL("./src", import.meta.url)),
        },
    },
    build: {
        chunkSizeWarningLimit: 1600,
    },
    plugins: [
        vue(),
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
    ],
});
