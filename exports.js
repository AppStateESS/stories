exports.path = require('path')
exports.APP_DIR = exports.path.resolve(__dirname, 'javascript')

exports.entry = {
  MediumEditorPack: exports.APP_DIR + '/EntryForm/MediumEditorPack.js',
  EntryList: exports.APP_DIR + '/EntryList/index.jsx',
  AuthorList: exports.APP_DIR + '/AuthorList/index.jsx',
  EntryForm: exports.APP_DIR + '/EntryForm/EntryForm.js',
  Publish: exports.APP_DIR + '/Publish/index.jsx',
  TagBar: exports.APP_DIR + '/TagBar/index.jsx',
  ListView : exports.APP_DIR + '/ListView/index.jsx',
  Settings: exports.APP_DIR + '/Settings/index.jsx',
  Feature: exports.APP_DIR + '/Feature/index.jsx',
  Navbar: exports.APP_DIR + '/Navbar/index.jsx',
  AuthorBar: exports.APP_DIR + '/AuthorBar/index.jsx',
  Tooltip: exports.APP_DIR + '/Tooltip/index.js',
  Caption: exports.APP_DIR + '/Caption/index.js',
  Sortable: exports.APP_DIR + '/Sortable/index.js',
  ShareHost: exports.APP_DIR + '/ShareHost/index.jsx',
  ImageOrientation: exports.APP_DIR + '/ImageOrientation/index.jsx'
}
