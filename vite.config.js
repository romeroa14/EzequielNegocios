import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
            // Asegurarse de que los assets se sirvan desde la URL correcta en producción
            buildDirectory: 'build'
        }),
    ],
    build: {
        outDir: 'public/build',
        manifest: 'manifest.json',
        rollupOptions: {
            output: {
                manualChunks: undefined,
            },
        },
    },
    // Configuración específica para Vapor
    server: {
        hmr: {
            host: 'localhost',
        },
    },
});
