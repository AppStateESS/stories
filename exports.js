exports.path = require('path')
exports.APP_DIR = exports.path.resolve(__dirname, 'javascript')

exports.entry = {
  EntryForm: exports.APP_DIR + '/EntryForm/editor.js',
  vendor: ['react', 'react-dom']
}
