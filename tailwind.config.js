import defaultTheme from 'tailwindcss/defaultTheme'
import forms from '@tailwindcss/forms'

export default {
  content: ['./resources/**/*.blade.php', './resources/**/*.vue', './resources/**/*.js'],
  safelist: [{ pattern: /modal-open/ }],
  theme: {
    extend: {
      fontFamily: {
        // Текст по умолчанию
        sans: ['Poppins', ...defaultTheme.fontFamily.sans],
        // Для заголовков H1–H3 и т.п.
        heading: ['Raleway', ...defaultTheme.fontFamily.sans],
      },
      colors: {
        my_white: '#F2F2F2',
        my_black: '#121211',
        main: 'rgba(73, 130, 107)',
        main_op: 'rgba(73, 130, 107, 0.9)',
        more: 'rgba(0, 63, 52)',
        more_op: 'rgba(49, 75, 73, 0.5)',
        my_green: 'rgba(101, 176, 25)',
        my_green_op: 'rgba(101, 177, 25, 0.5)',
        my_red: 'rgba(228, 46, 0)',
        my_red_op: 'rgba(228, 46, 0, 0.5)',
      },
      animation: { 'fade-in': 'fadeIn 0.5s ease-out' },
      keyframes: {
        fadeIn: {
          from: { opacity: '0', transform: 'translateY(5px)' },
          to: { opacity: '1', transform: 'translateY(0)' },
        },
      },
      gridTemplateColumns: { 14: 'repeat(14, minmax(0, 1fr))' },
      maxWidth: { '3xl': '1920px' },
    },
  },
  plugins: [forms, require('tailwindcss-textshadow')],
}
