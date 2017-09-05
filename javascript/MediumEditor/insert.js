var editor = new MediumEditor('.entry-form', {
  placeholder: {
    text: 'Start your story here...',
    hideOnClick: false,
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
  },
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
        acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i
      }
    }
  }
})

editor.subscribe('editableInput', function (event, editable) {
  //console.log(event) console.log(editable)
  const content = this.getContent()
  console.log(content)
}.bind(editor))

$('#save').click(function () {
  console.log(editor.getContent())
})
