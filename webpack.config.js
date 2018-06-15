const webpack = require('webpack')
const setup = require('./exports.js')
const Promise = require('es6-promise').polyfill()

module.exports = (env, argv) => {
  const inProduction = argv.mode === 'production'
  const inDevelopment = argv.mode === 'development'

  const settings = {
    entry: setup.entry,
    output: {
      path: setup.path.join(setup.APP_DIR, 'dev'),
      filename: '[name].js',
    },
    externals: {
      $: 'jQuery',
      jquery: 'jQuery',
      EntryForm: 'EntryForm',
    },
    optimization: {
      splitChunks: {
        minChunks: 3,
        cacheGroups: {
          vendors: {
            test: /[\\/]node_modules[\\/]/,
            minChunks: 3,
            name: 'vendor',
            enforce: true,
            chunks: 'all',
          }
        }
      }
    },
    resolve: {
      extensions: [
        '.js', '.jsx',
      ],
      alias: {
        'jquery-ui/widget': 'blueimp-file-upload/js/vendor/jquery.ui.widget.js'
      }
    },
    plugins: [
      new webpack.ProvidePlugin({EntryForm: 'EntryForm'}),
      new webpack.IgnorePlugin(/^\.\/locale$/, /moment$/),
    ],
    module: {
      rules: [
        {
          test: require.resolve('blueimp-file-upload'),
          loader: 'imports-loader?define=>false',
        }, {
          test: require.resolve('medium-editor-insert-plugin'),
          loader: 'imports-loader?define=>false',
        }, {
          test: /\.(png|woff|woff2|eot|ttf|svg)$/,
          loader: 'url-loader?limit=100000',
        }, {
          test: /\.jsx?/,
          include: setup.APP_DIR,
          loader: 'babel-loader',
          query: {
            presets: ['env', 'react',]
          },
        }, {
          test: /\.css$/,
          loader: 'style-loader!css-loader',
        },
      ]
    }
  }

  if (inDevelopment) {
    const BrowserSyncPlugin = require('browser-sync-webpack-plugin')
    settings.plugins.push(new BrowserSyncPlugin({
      host: 'localhost',
      notify: false,
      port: 3000,
      files: ['./javascript/dev/*.js'],
      proxy: 'localhost/canopy',
    }))
    settings.devtool = 'inline-source-map'
    settings.output = {
      path: setup.path.join(setup.APP_DIR, 'dev'),
      filename: '[name].js',
    }
  }

  if (inProduction) {
    //const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin
    //settings.plugins.push(new BundleAnalyzerPlugin())

    const AssetsPlugin = require('assets-webpack-plugin')
    const UglifyJsPlugin = require('uglifyjs-webpack-plugin')
    settings.plugins.push(
      new webpack.DefinePlugin({'process.env.NODE_ENV': JSON.stringify('production')})
    )
    settings.plugins.push(new UglifyJsPlugin({extractComments: true}))
    settings.plugins.push(
      new AssetsPlugin({filename: 'assets.json', prettyPrint: true,})
    )
    settings.output = {
      path: setup.path.join(setup.APP_DIR, 'build'),
      filename: '[name].[chunkhash:8].min.js',
      chunkFilename: '[name].[chunkhash:8].chunk.js',
    }
  }
  return settings
}
