/* global $, MediumEditor */
require("expose-loader?$!jquery")
import 'blueimp-file-upload/js/vendor/jquery.ui.widget'
import 'blueimp-file-upload/js/jquery.iframe-transport'
import 'blueimp-file-upload/js/jquery.fileupload'
import 'handlebars/dist/handlebars.min.js'

require('medium-editor-insert-plugin/dist/js/medium-editor-insert-plugin')($)
require('medium-editor-insert-plugin/src/js/templates')
require('medium-editor-insert-plugin/src/js/core')
require('medium-editor-insert-plugin/src/js/embeds')
require('medium-editor-insert-plugin/src/js/images')

import 'medium-editor-insert-plugin/dist/css/medium-editor-insert-plugin.min.css'
import 'medium-editor/dist/css/medium-editor.css'
import 'medium-editor/dist/css/themes/default.css'

MediumEditor.extensions.button.prototype.defaults.h2.contentFA = '<i class="fa fa-heading"></i><sup>2</sup>'
MediumEditor.extensions.button.prototype.defaults.h3.contentFA = '<i class="fa fa-heading"></i><sup>3</sup>'
MediumEditor.extensions.button.prototype.defaults.h4.contentFA = '<i class="fa fa-heading"></i><sup>4</sup>'
