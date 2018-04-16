'use strict'

/* global $ */

export default class EntryForm {
  constructor(status, entry) {
    this.entry = entry
    window.onunload = function () {
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

  removeYouTube(element) {
    const youtubeUrl = element.find('iframe').attr('src')
    const youtubeId = youtubeUrl.replace(/.*embed\/(\w+)\?.*/g, '$1')
    if (youtubeId && youtubeId.length > 0) {
      $.ajax({
        url: './stories/EntryPhoto/' + this.entry.id,
        data: {file : youtubeId + '.jpg'},
        dataType: 'json',
        type: 'delete',
        success: function () {
          this.save()
        }.bind(this),
        error: function () {}.bind(this),
      })
    }
  }

  cleanUpEmbed(element) {
    const html = element.html()
    if (html.search('youtube.com') > 0) {
      this.removeYouTube(element)
    }
  }

  save() {
    const entry = this.entry
    // Do not try and save content with an base64 encoded image
    if (entry.content.match(/<img src="data:image\//)) {
      return
    }
    this.status.html('<span><i class="fas fa-cog fa-spin fa-fw"></i>Saving...</span>')
    $.ajax({
      url: './stories/Entry/' + entry.id,
      data: entry,
      dataType: 'json',
      type: 'put',
      success: function () {
        const ts = new Date()
        const timestring = ts.toLocaleTimeString('en-US')
        setTimeout(function(){
          this.status.html(`<span class="text-success"><i class="fa fa-check"></i>Saved @ ${timestring}</span>`)
        }.bind(this), 1000)
      }.bind(this),
      error: function () {
        this.status.text('ERROR')
      }.bind(this)
    })

  }

}

window.EntryFormClass = EntryForm
