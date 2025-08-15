import preset from './vendor/filament/support/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './app/Filament/**/*.php',
        './resources/views/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
        './vendor/blade-ui-kit/blade-heroicons/resources/views/**/*.blade.php',
    ],
    plugins: [
        require('@tailwindcss/typography'),
        require('daisyui'),
    ],

    daisyui: {
        themes: ["dark"],
    },
}
