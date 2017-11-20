/* global $, MediumEditor, entry, EntryFormClass */

const EntryForm = new EntryFormClass($('#story-status'), entry)

var editor = new MediumEditor('.entry-form', {
  placeholder: {
    text: 'Start your story here...',
    hideOnClick: false,
  },
  disableDoubleReturn : true,
  autoLink: true,
  imageDragging: false,
  toolbar: {
    buttons: [
      'bold',
      'italic',
      'anchor',
      'h3',
      'h4',
      'quote',
      'orderedlist',
      'unorderedlist',
      'removeFormat',
    ]
  },
})

editor.selectElement(document.querySelector('.entry-form'))

$('.entry-form').mediumInsert({
  editor: editor,
  addons: {
    images: {
      deleteScript: EntryForm.deleteUrl(),
      deleteMethod: 'DELETE',
      captions: true,
      autoGrid: 3,
      fileUploadOptions: {
        url: EntryForm.uploadUrl(),
        type: 'post',
        formData: {
          entryId: EntryForm.entry.id
        },
        acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i
      }
    },
    embeds: {
      actions: {
        remove: {
          label: '<span class="fa fa-times"></span>',
          clicked: function (el) {
            var $event = $.Event('keydown')
            $event.which = 8
            $(document).trigger($event)
            EntryForm.cleanUpEmbed($(el[0]))
          },
        }
      }
    },
  }
})

$('#entry-title').blur(function () {
  EntryForm.entry.title = $(this).val()
  EntryForm.save()
})

const blurContentSave = function () {
  saveContent(editor)
}

const saveContent = function (editor) {
  EntryForm.entry.content = editor.getContent()
  EntryForm.save()
}

// Without this, the throttle will run save twice.
let delaySave = true

const triggerAutoSave = function (event, editable) {
  if (delaySave) {
    delaySave = false
    return
  }
  saveContent(editor)
  delaySave = true
}
const throttledAutoSave = MediumEditor.util.throttle(triggerAutoSave, 3000)
editor.subscribe('editableInput', throttledAutoSave)
editor.subscribe('blur', blurContentSave)

editor.subscribe('editableDrop', function(e, element) {
  if (e.dataTransfer.files.length < 1) {
    return true
  } else {
    return handleImageDropped(e, element)
  }
})

var handleImageDropped = function(e, element) {
  var data, file, fileUploadOptions, imagePlugin
  data = e.dataTransfer.files
  imagePlugin = $.data(element, 'plugin_mediumInsertImages')
  file = $(imagePlugin.templates['src/js/templates/images-fileupload.hbs']())
  fileUploadOptions = {
    url: imagePlugin.options.fileUploadOptions.url,
    dataType: 'json',
    acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
    formData: imagePlugin.options.fileUploadOptions.formData,
    dropZone: null,
    add: function(e, data) {
      $.proxy(imagePlugin, 'uploadAdd', e, data)()
    },
    done: function(e, data) {
      $.proxy(imagePlugin, 'uploadDone', e, data)()
    }
  }
  if (new XMLHttpRequest().upload) {
    fileUploadOptions.progress = function(e, data) {
      return $.proxy(imagePlugin, 'uploadProgress', e, data)()
    }
    fileUploadOptions.progressall = function(e, data) {
      return $.proxy(imagePlugin, 'uploadProgressall', e, data)()
    }
  }
  file.fileupload(fileUploadOptions)
  file.fileupload('add', {
    files: data
  })
  return false
}
