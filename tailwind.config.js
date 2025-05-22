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
        my_white: '#f8f8f5', // для Фона
        my_black: '#121211', // для Текст
        my_gray: 'rgba(51, 65, 85)',
        my_gray_op: 'rgba(51, 65, 85, 0.3)',
        my_pink: 'rgba(244, 63, 94)',
        my_pink_op: 'rgba(244, 63, 94, 0.3)',
        my_crem: 'rgba(117, 110, 106)',
        my_crem_op: 'rgba(117, 110, 106, 0.7)',
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
    },
  },

  plugins: [forms],
}
