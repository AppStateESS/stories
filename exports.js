exports.path = require('path')
exports.APP_DIR = exports.path.resolve(__dirname, 'javascript')

exports.entry = {
  MediumEditorPack: exports.APP_DIR + '/EntryForm/MediumEditorPack.js',
  EntryList: exports.APP_DIR + '/EntryList/index.jsx',
  AuthorList: exports.APP_DIR + '/AuthorList/index.jsx',
  EntryForm: exports.APP_DIR + '/EntryForm/EntryForm.js',
  PublishBar: exports.APP_DIR + '/PublishBar/index.jsx',
  TagBar: exports.APP_DIR + '/TagBar/index.jsx',
  Settings: exports.APP_DIR + '/Settings/index.jsx',
  Feature: exports.APP_DIR + '/Feature/index.jsx',
  Navbar: exports.APP_DIR + '/Navbar/index.jsx',
  AuthorBar: exports.APP_DIR + '/AuthorBar/index.jsx',
  Tooltip: exports.APP_DIR + '/Tooltip/index.js',
  Caption: exports.APP_DIR + '/Caption/index.js',
  vendor: ['react', 'react-dom']
}
