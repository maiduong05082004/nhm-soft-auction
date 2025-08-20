import { defineConfig } from 'vite'
import laravel, { refreshPaths } from 'laravel-vite-plugin'
import tailwindcss from 'tailwindcss' // Sử dụng import thay vì require
import autoprefixer from 'autoprefixer' // Sử dụng import thay vì require
export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/partials/slide.js',
                'resources/js/partials/filter.js',
                'resources/js/admin/buy-membership.js',
                'resources/js/partials/wishlist.js'
            ],
            refresh: [
                ...refreshPaths,
                'app/Filament/**',
                'app/Forms/Components/**',
                'app/Livewire/**',
                'app/Infolists/Components/**',
                'app/Providers/Filament/**',
                'app/Tables/Columns/**',
            ],
        }),
    ],
    css: {
        postcss: {
            plugins: [
                tailwindcss,  // Sử dụng require để gọi tailwindcss
                autoprefixer,
            ],
        },
    },
})
