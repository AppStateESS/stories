var setup = require('./exports.js')
var webpack = require('webpack')
var WebpackStripLoader = require('strip-loader')
var Promise = require('es6-promise').polyfill()
var AssetsPlugin = require('assets-webpack-plugin');

module.exports = {
  entry: setup.entry,
  output: {
    path: setup.path.join(setup.APP_DIR, 'build'),
    filename: '[name].[chunkhash:8].min.js',
    chunkFilename: '[name].[chunkhash:8].chunk.js',
  },
  externals: {
    jquery: 'jQuery',
    $: 'jQuery',
  },
  resolve: {
    extensions: ['.js', '.jsx',],
    alias: {
      'jquery-ui/widget': 'blueimp-file-upload/js/vendor/jquery.ui.widget.js',
    },
  },
  module: {
    rules: [
      {
        test: require.resolve('blueimp-file-upload'),
        loader: 'imports-loader?define=>false'
      },
      {
        test: require.resolve('medium-editor-insert-plugin'),
        loader: 'imports-loader?define=>false'
      },
      {
        test: /\.jsx?/,
        include: setup.APP_DIR,
        loader: 'babel-loader',
        query: {
          presets: ['es2015', 'react',]
        }
      }, {
        test: [
          /\.js$/, /\.es6$/, /\.jsx$/,
        ],
        exclude: /node_modules/,
        loader: WebpackStripLoader.loader('console.log')
      }, {
        test: /\.(png|woff|woff2|eot|ttf|svg)$/,
        loader: 'url-loader?limit=100000'
      }, {
        test: /\.css$/,
        loader: 'style-loader!css-loader'
      },
    ]
  },
  plugins: [
    new webpack.optimize.CommonsChunkPlugin({name: 'vendor', filename: 'vendor.[chunkhash:8].js'}),
    new webpack.DefinePlugin({
      'process.env': {
        'NODE_ENV': JSON.stringify('production')
      }
    }),
    new webpack.optimize.UglifyJsPlugin({
      compress: {
        screw_ie8: true,
        warnings: false
      },
      mangle: {
        screw_ie8: true
      },
      output: {
        comments: false,
        screw_ie8: true
      }
    }),
    new AssetsPlugin({filename: 'assets.json', prettyPrint: true}),
  ]
}
