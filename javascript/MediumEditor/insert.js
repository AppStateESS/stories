/* global $, MediumEditor, entry, EntryFormClass */

const EntryForm = new EntryFormClass($('#story-status'), entry)

const editor = new MediumEditor('.entry-form', {
  placeholder: {
    text: 'Click here to start your story...',
    hideOnClick: true,
  },
  buttonLabels: 'fontawesome',
  paste: {
    forcePlainTest: true,
  },
  disableDoubleReturn : false,
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
      'indent',
      'outdent',
      'removeFormat',
    ]
  },
})
// This code defaults the cursor to the first line. The cursor ends up above
// the content and causes problems. It is better to have them click to get
// started.
//editor.selectElement(document.querySelector('.entry-form'))
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
      label: '<i class="fab fa-youtube"></i>',
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

const triggerAutoSave = () => {
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
