/* global $, MediumEditor */
import 'blueimp-file-upload/js/vendor/jquery.ui.widget'
import 'blueimp-file-upload/js/jquery.iframe-transport'
import 'blueimp-file-upload/js/jquery.fileupload'
import 'handlebars/dist/handlebars.min.js'

require('@essappstate/medium-editor-insert-plugin/dist/js/medium-editor-insert-plugin')($)
require('@essappstate/medium-editor-insert-plugin/src/js/templates')
require('@essappstate/medium-editor-insert-plugin/src/js/core')
require('@essappstate/medium-editor-insert-plugin/src/js/embeds')
require('@essappstate/medium-editor-insert-plugin/src/js/images')

import '@essappstate/medium-editor-insert-plugin/dist/css/medium-editor-insert-plugin.min.css'
import 'medium-editor/dist/css/medium-editor.css'
import 'medium-editor/dist/css/themes/default.css'

MediumEditor.extensions.button.prototype.defaults.h2.contentFA = '<i class="fa fa-heading"></i><sup>2</sup>'
MediumEditor.extensions.button.prototype.defaults.h3.contentFA = '<i class="fa fa-heading"></i><sup>3</sup>'
MediumEditor.extensions.button.prototype.defaults.h4.contentFA = '<i class="fa fa-heading"></i><sup>4</sup>'
