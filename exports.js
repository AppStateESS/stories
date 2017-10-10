exports.path = require('path')
exports.APP_DIR = exports.path.resolve(__dirname, 'javascript')

exports.entry = {
  MediumEditorPack: exports.APP_DIR + '/EntryForm/MediumEditorPack.js',
  EntryList: exports.APP_DIR + '/EntryList/index.jsx',
  EntryForm: exports.APP_DIR + '/EntryForm/EntryForm.js',
  PublishBar: exports.APP_DIR + '/PublishBar/index.jsx',
  TagBar: exports.APP_DIR + '/TagBar/index.jsx',
  vendor: ['react', 'react-dom']
}
