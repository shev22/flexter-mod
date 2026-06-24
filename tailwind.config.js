import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                display: ['Sora', 'Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                accent: 'rgb(var(--accent) / <alpha-value>)',
                accent2: 'rgb(var(--accent-2) / <alpha-value>)',
                bg: 'rgb(var(--bg) / <alpha-value>)',
                surface: 'rgb(var(--surface) / <alpha-value>)',
                surface2: 'rgb(var(--surface-2) / <alpha-value>)',
                hair: 'rgb(var(--hair) / <alpha-value>)',
                ink: 'rgb(var(--text) / <alpha-value>)',
                muted: 'rgb(var(--muted) / <alpha-value>)',
            },
            backgroundImage: {
                'aurora': 'linear-gradient(135deg, rgb(var(--accent)), rgb(var(--accent-2)))',
                'aurora-soft': 'linear-gradient(135deg, rgb(var(--accent) / 0.18), rgb(var(--accent-2) / 0.18))',
            },
            boxShadow: {
                glow: '0 0 0 1px rgb(var(--accent) / 0.35), 0 18px 50px -12px rgb(var(--accent) / 0.45)',
                card: '0 22px 60px -22px rgba(0,0,0,0.75)',
            },
            borderRadius: {
                '2.5xl': '1.25rem',
                '4xl': '2rem',
            },
            keyframes: {
                kenburns: {
                    '0%': { transform: 'scale(1) translate3d(0,0,0)' },
                    '100%': { transform: 'scale(1.12) translate3d(-1.5%, -1.5%, 0)' },
                },
                'fade-up': {
                    '0%': { opacity: '0', transform: 'translateY(18px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                'fade-in': {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                shimmer: {
                    '100%': { transform: 'translateX(100%)' },
                },
                float: {
                    '0%,100%': { transform: 'translateY(0)' },
                    '50%': { transform: 'translateY(-8px)' },
                },
            },
            animation: {
                kenburns: 'kenburns 16s ease-out forwards',
                'fade-up': 'fade-up 0.6s cubic-bezier(0.22,1,0.36,1) both',
                'fade-in': 'fade-in 0.5s ease both',
                float: 'float 6s ease-in-out infinite',
            },
        },
    },
    plugins: [],
};
