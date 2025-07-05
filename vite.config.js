import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
            // Asegurarse de que los assets se sirvan desde la URL correcta en producción
            buildDirectory: 'build',
            // Ensure manifest is generated in the correct location
            manifestFilePath: 'public/build/manifest.json'
        }),
    ],
    build: {
        outDir: 'public/build',
        // Asegurarse de que los assets se compilen correctamente
        manifest: true,
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
