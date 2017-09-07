/* global $, MediumEditor, entryId, EntryFormClass */

var editor = new MediumEditor('.entry-form', {
  placeholder: {
    text: 'Start your story here...',
    hideOnClick: false
  },
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
  }
})

$('.entry-form').mediumInsert({
  editor: editor,
  addons: {
    images: {
      deleteScript: 'delete.php',
      fileDeleteOptions: {
        fileName: 'scrub'
      },
      captions: true,
      autoGrid: 3,
      fileUploadOptions: {
        url: 'upload.php',
        acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
      },
    }
  },
})

const EntryForm = new EntryFormClass($('#story-status'))
EntryForm.entry.id = entryId
EntryForm.load()

$('#entry-title').blur(function(){
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

let currentTimeout

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
const throttledAutoSave = MediumEditor.util.throttle(triggerAutoSave, 5000);
editor.subscribe('editableInput', throttledAutoSave);
editor.subscribe('blur', blurContentSave);
