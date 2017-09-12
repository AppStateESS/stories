/* global $, MediumEditor, entryId, EntryFormClass */

const EntryForm = new EntryFormClass($('#story-status'))
EntryForm.entry.id = entryId
EntryForm.load()

var editor = new MediumEditor('.entry-form', {
  placeholder: {
    text: 'Start your story here...',
    hideOnClick: true
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
      deleteScript: EntryForm.deleteUrl(),
      deleteMethod : 'DELETE',
      captions: true,
      autoGrid: 3,
      fileUploadOptions: {
        url: EntryForm.uploadUrl(),
        type: 'post',
        formData: {entryId : EntryForm.entry.id},
        acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
      },
    }
  },
})


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
const throttledAutoSave = MediumEditor.util.throttle(triggerAutoSave, 3000);
editor.subscribe('editableInput', throttledAutoSave);
editor.subscribe('blur', blurContentSave);
