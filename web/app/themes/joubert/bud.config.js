/**
 * @typedef {import('@roots/bud').Bud} bud
 *
 * @param {bud} app
 */
module.exports = async (bud) => {
  bud
    .setPublicPath("/app/themes/joubert/public/")

    /**
     * Application entrypoints
     *
     * Paths are relative to your resources directory
     */
    .entry({
      app: ['@scripts/app', '@styles/app.scss'],
      editor: ['@scripts/editor', '@styles/editor.scss'],
    })

    /**
     * These files should be processed as part of the build
     * even if they are not explicitly imported in application assets.
     */
    .assets(['fonts'])
    .tap(({build}) => {
      build.rules.font.setUse([]);
      build.rules.font.setType('asset/resource');
      build.rules.font.setGenerator((app) => ({
        filename: `app/themes/joubert/public/fonts/[name][ext]`,
      }));
    })
    /**
     * These files will trigger a full page reload
     * when modified.
     */
    .watch('resources/views/**/*', 'app/**/*')

    /**
     * Target URL to be proxied by the dev server.
     *
     * This should be the URL you use to visit your local development server.
     */
    .proxy('http://joubert.test')

    /**
     * Development URL to be used in the browser.
     */
    .serve('http://joubert.test');
};
