import preset from './vendor/filament/support/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './app/Filament/**/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        './vendor/filament/**/*.blade.php',
        './vendor/blade-ui-kit/blade-heroicons/resources/views/**/*.blade.php',
    ],
    safelist: [
        'grid',
        'grid-cols-3',
        'gap-2',
        'col-span-1',
        'col-span-2',
        'w-full',
        'btn',
        'btn-sm',
        'btn-outline',
        'btn-neutral',
    ],
    plugins: [
        require('@tailwindcss/typography'),
        require('daisyui'),
    ],

    daisyui: {
        themes: ["nord"],
    },
}
