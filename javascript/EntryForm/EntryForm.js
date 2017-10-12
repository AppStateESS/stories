'use strict'

/* global $ */

export default class EntryForm {
  constructor(status, entry) {
    this.entry = entry
    window.onunload = function() {
      this.save()
    }.bind(this)
    // status is the jquery node/object for the status text
    this.status = status
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
        this.status.text('Saved')
      }.bind(this),
      error: function () {
        this.status.text('ERROR')
      }.bind(this),
    })

  }

}

window.EntryFormClass = EntryForm
