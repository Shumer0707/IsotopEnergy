import defaultTheme from 'tailwindcss/defaultTheme'
import forms from '@tailwindcss/forms'

/** @type {import('tailwindcss').Config} */
export default {
  content: ['./resources/**/*.blade.php', './resources/**/*.vue', './resources/**/*.js'],

  safelist: [
    {
      pattern: /modal-open/,
    },
  ],

  theme: {
    extend: {
      fontFamily: {
        sans: ['Figtree', ...defaultTheme.fontFamily.sans],
      },
      colors: {
        my_white: '#F2F2F2', // для Фона
        my_black: '#121211', // для Текст
        main: 'rgba(0, 50, 45)',
        more: 'rgba(49, 75, 73)',
        bg_sc: 'rgba(101, 177, 25)',
        my_pink: 'rgba(244, 63, 94)',
        my_pink_op: 'rgba(244, 63, 94, 0.3)',
      },
      animation: {
        'fade-in': 'fadeIn 0.5s ease-out',
      },
      keyframes: {
        fadeIn: {
          from: { opacity: '0', transform: 'translateY(5px)' },
          to: { opacity: '1', transform: 'translateY(0)' },
        },
      },
      gridTemplateColumns: {
        14: 'repeat(14, minmax(0, 1fr))',
      },
      maxWidth: {
      '3xl': '1920px',
    },
    },
  },

  plugins: [forms],
}
