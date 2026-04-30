import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import tailwindcss from '@tailwindcss/vite';
import inertia from '@inertiajs/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.ts',
            refresh: true,
        }),
        vue(),
        tailwindcss(),
        inertia({ ssr: false }),
    ],
    resolve: {
        extensions: ['.js', '.json', '.ts', '.vue'],
    }
});
