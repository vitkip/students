module.exports = {
  content: [
    "./templates/**/*.php",
    "./public/assets/js/**/*.js",
    "./public/assets/css/**/*.css",
  ],
  theme: {
    extend: {
      colors: {
        primary: '#4A90E2',
        secondary: '#D0021B',
      },
      fontFamily: {
        sans: ['Helvetica', 'Arial', 'sans-serif'],
      },
    },
  },
  plugins: [],
}