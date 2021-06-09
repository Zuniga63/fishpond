const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
  purge: [
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './vendor/laravel/jetstream/**/*.blade.php',
    './storage/framework/views/*.php',
    './resources/views/**/*.blade.php',
  ],

  theme: {
    extend: {
      fontFamily: {
        sans: ['Nunito', ...defaultTheme.fontFamily.sans],
      },
      colors: {
        'gray-dark': '#343a40',
        'yellow-light': '#f1c40f',
        'dark': '#212529',
        'light': '#f8f9fa',
      },
      height: {
        'screen-20': '20vh',
        'screen-40': '40vh',
        'screen-50': '50vh',
        'screen-60': '60vh',
        'screen-80': '80vh',
        'screen-90': '90vh',
        'screen-95': '95vh',
      },
      maxHeight: {
        'screen-20': '20vh',
        'screen-40': '40vh',
        'screen-50': '50vh',
        'screen-60': '60vh',
        'screen-80': '80vh',
        'screen-90': '90vh',
        'screen-95': '95vh',
      },
      minHeight: {
        'screen-20': '20vh',
        'screen-40': '40vh',
        'screen-50': '50vh',
        'screen-60': '60vh',
        'screen-80': '80vh',
        'screen-90': '90vh',
        'screen-95': '95vh',
      },
      zIndex: {
        'back': '-1',
        'fixed': '100',
        'modal': '1000',
        'preload': '1100',
      },
    },
  },

  variants: {
    extend: {
      opacity: ['disabled'],
    },
  },

  plugins: [require('@tailwindcss/forms'), require('@tailwindcss/typography')],
  // Se habilita el modo DARK
  darkMode: 'media',
};
