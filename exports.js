exports.path = require('path')
exports.APP_DIR = exports.path.resolve(__dirname, 'javascript')

exports.entry = {
  MediumEditorPack: exports.APP_DIR + '/EntryForm/MediumEditorPack.js',
  EntryForm: exports.APP_DIR + '/EntryForm/EntryForm.js',
  vendor: ['react', 'react-dom']
}
