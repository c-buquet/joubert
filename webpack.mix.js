const mix = require('laravel-mix');

require('dotenv').config();
const tailwindcss = require('tailwindcss');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your WordPlate application. By default, we are compiling the Sass
 | file for your application, as well as bundling up your JavaScript files.
 |
 */

const theme = process.env.WP_THEME;
const domain = process.env.BROWSERSYNC_DOMAIN;

const distURL = `./web/app/themes/${theme}/resources/assets`


mix.setPublicPath(distURL);

mix
  .options({
    autoprefixer: {
      options: {
        grid: true
      }
    },
    processCssUrls: false,
    postCss: [tailwindcss('./tailwind.config.js')]
  })
  .js(`resources/scripts/main.js`, `scripts`)
  .sass(`resources/styles/main.scss`, `styles`)
  .copyDirectory(`resources/images`, `${distURL}/images`)
  .copyDirectory(`resources/fonts`, `${distURL}/fonts`)
  .version()
  .browserSync({
    proxy: domain,
    files: [
      `web/app/themes/${theme}`
    ]
  });
