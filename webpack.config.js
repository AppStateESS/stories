var setup = require('./exports.js')
var webpack = require('webpack')
var Promise = require('es6-promise').polyfill()
var Shared = require('./Shared.js')

module.exports = {
  entry: setup.entry,
  output: {
    path: setup.path.join(setup.APP_DIR, 'dev'),
    filename: '[name].js'
  },
  externals: {
    $: 'jQuery',
    EntryForm: 'EntryForm'
  },
  resolve: {
    extensions: [
      '.js', '.jsx',
    ],
    alias: {
      'jquery-ui/widget': 'blueimp-file-upload/js/vendor/jquery.ui.widget.js'
    },
  },
  plugins: [
    new webpack.optimize.CommonsChunkPlugin(
      {name: 'vendor', filename: 'vendor.js'}
    ),
    new webpack.ProvidePlugin({EntryForm: 'EntryForm'}),
  ],
  module: {
    rules: [
      {
        test: require.resolve('blueimp-file-upload'),
        loader: 'imports-loader?define=>false'
      }, {
        test: require.resolve('medium-editor-insert-plugin'),
        loader: 'imports-loader?define=>false'
      }, {
        test: /\.jsx?$/,
        enforce: 'pre',
        loader: 'jshint-loader',
        exclude: '/node_modules/',
        include: setup.APP_DIR + '/dev'
      }, {
        test: /\.(png|woff|woff2|eot|ttf|svg)$/,
        loader: 'url-loader?limit=100000'
      }, {
        test: /\.jsx?/,
        include: setup.APP_DIR,
        loader: 'babel-loader',
        query: {
          presets: ['es2015', 'react',]
        }
      }, {
        test: /\.css$/,
        loader: 'style-loader!css-loader'
      },
    ]
  },
  devtool: 'source-map'
}
