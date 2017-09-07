'use strict'

/* global $ */

export default class EntryForm {
  constructor(status) {
    this.entry = {
      id: 0,
      title: '',
      content: ''
    }
    // status is the jquery node/object for the status text
    this.status = status
  }

  load() {
    if (this.entry.id === 0) {
      return
    }
    $.getJSON('./stories/Entry/' + this.entry.id).done(function(data) {
      this.entry = data.entry
    }.bind(this))
  }

  save() {
    const entry = this.entry
    this.status.text('Saving...')
    let method = 'post'
    let url = './stories/Entry/'
    if (entry.id > 0) {
      method = 'put'
      url = url + entry.id
    }
    $.ajax({
      url: url,
      data: entry,
      dataType: 'json',
      type: method,
      success: function (data) {
        entry.id = data.entryId
        this.status.text('Saved')
      }.bind(this),
      error: function () {
        this.status.text('ERROR')
      }.bind(this)
    })

  }

  /*
  saveContent(editor) {
    $('#story-status').text('Saving...')
    const content = editor.getContent()
    if (entryId > 0) {
      this.patchContent(content, this.id)
    } else {
      this.postContent(content)
    }
  }

  patchContent(content, entryId) {
    $.ajax({
      url: './stories/Entry/' + entryId,
      data: {
        param: 'content',
        value: content
      },
      dataType: 'json',
      type: 'patch',
      success: function () {
        $('#story-status').text('Saved')
      }.bind(this),
      error: function () {
        $('#story-status').text('ERROR')
      }.bind(this)
    })
  }
*/
}

window.EntryFormClass = EntryForm
