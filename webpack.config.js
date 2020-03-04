const Encore = require('@symfony/webpack-encore')
const path = require('path')

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
  Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev')
}

Encore
  .setOutputPath('public/assets/')
  .setPublicPath('/assets')
  // only needed for CDN's or sub-directory deploy
  // .setManifestKeyPrefix('assets/')
  .addEntry('app', './assets/js/app.js')
  .addEntry('admin', './assets/js/admin.js')
  // .splitEntryChunks()
  .enableSingleRuntimeChunk()
  .configureBabel(c => { return {} })
  .addAliases({
    svelte: path.resolve('node_modules', 'svelte'),
    '@fn': path.resolve('assets', 'js', 'functions'),
    '@el': path.resolve('assets', 'js', 'elements')
  })
  .addLoader(
    {
      test: /\.(html|svelte)$/,
      exclude: /node_modules/,
      use: {
        loader: 'svelte-loader',
        options: {
          onwarn: function (warning, onwarn) {
            if (warning.code !== 'avoid-is') {
              onwarn(warning)
            }
          }
        }
      },
    })
  /*
   * FEATURE CONFIG
   *
   * Enable & configure other features below. For a full
   * list of features, see:
   * https://symfony.com/doc/current/frontend.html#adding-more-features
   */
  .cleanupOutputBeforeBuild()
  // .enableBuildNotifications()
  .enableSourceMaps(!Encore.isProduction())
  // enables hashed filenames (e.g. app.abc123.css)
  .enableVersioning(Encore.isProduction())

  // enables Sass/SCSS support
  .enableSassLoader()

// uncomment if you use TypeScript
// .enableTypeScriptLoader()

// uncomment to get integrity="..." attributes on your script & link tags
// requires WebpackEncoreBundle 1.4 or higher
//.enableIntegrityHashes(Encore.isProduction())

// uncomment if you're having problems with a jQuery plugin
//.autoProvidejQuery()

// uncomment if you use API Platform Admin (composer req api-admin)
//.enableReactPreset()
//.addEntry('admin', './assets/js/admin.js')

if (!Encore.isProduction()) {
  Encore.disableCssExtraction()
}

const config = Encore.getWebpackConfig()
config.resolve.extensions.push('.svelte')
config.resolve.mainFields = ['svelte', 'browser', 'module', 'main']

module.exports = config
