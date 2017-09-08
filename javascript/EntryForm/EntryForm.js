'use strict'

/* global $ */

export default class EntryForm {
  constructor(status) {
    this.entry = {
      id: 0,
      title: '',
      content: '<p class="medium-insert-active"><p>',
    }
    // status is the jquery node/object for the status text
    this.status = status
  }

  load() {
    if (this.entry.id === 0) {
      return
    }
    $.getJSON('./stories/Entry/' + this.entry.id).done(function (data) {
      this.entry = data.entry
    }.bind(this))
  }

  uploadUrl() {
    return './stories/EntryPhoto/'
  }

  deleteUrl() {
    return './stories/EntryPhoto/' + this.entry.id
  }

  save() {
    const entry = this.entry
    // Do not try and save content with an base64 encoded image
    if (entry.content.match(/<img src="data:image\//)) {
      return
    }
    this.status.text('Saving...')
    $.ajax({
      url: './stories/Entry/' + entry.id,
      data: entry,
      dataType: 'json',
      type: 'put',
      success: function (data) {
        entry.id = data.entryId
        this.status.text('Saved')
      }.bind(this),
      error: function () {
        this.status.text('ERROR')
      }.bind(this),
    })

  }

}

window.EntryFormClass = EntryForm
