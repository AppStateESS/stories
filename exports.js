exports.path = require('path')
exports.APP_DIR = exports.path.resolve(__dirname, 'javascript')

exports.entry = {
  vendor: ['react', 'react-dom']
}
