'use strict'

/* global $ */

String.prototype.hashCode = function () {
  var hash = 0,
    i,
    chr
  if (this.length === 0) return hash
  for (i = 0; i < this.length; i++) {
    chr = this.charCodeAt(i)
    hash = (hash << 5) - hash + chr
    hash |= 0 // Convert to 32bit integer
  }
  return hash
}

export default class EntryForm {
  constructor(status, entry) {
    this.entry = entry
    window.onunload = function () {
      this.save()
    }.bind(this)
    // status is the jquery node/object for the status text
    this.status = status
    this.allowSave = true
    this.compareHash = null
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
        data: {file: youtubeId + '.jpg'},
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
    const entryHash = entry.content.hashCode()
    if (entryHash === this.compareHash) {
      return
    } else {
      this.compareHash = entryHash
    }
    // Do not try and save content with an base64 encoded image
    if (entry.content.match(/<img src="data:image\//)) {
      return
    }
    if (!this.allowSave) {
      return
    }
    this.allowSave = false
    this.limiter = setTimeout(() => {
      this.status.html(
        '<span><i class="fas fa-cog fa-spin fa-fw"></i>&nbsp;Saving...</span>'
      )
      $.ajax({
        url: './stories/Entry/' + entry.id,
        data: entry,
        dataType: 'json',
        type: 'put',
        success: function () {
          const ts = new Date()
          const timestring = ts.toLocaleTimeString('en-US')
          this.allowSave = true
          clearTimeout(this.limiter)
          setTimeout(
            function () {
              this.status.html(
                `<span class="text-success"><i class="fa fa-check"></i>&nbsp;Saved @ ${timestring}</span>`
              )
            }.bind(this),
            1000
          )
        }.bind(this),
        error: function () {
          this.status.text('ERROR')
        }.bind(this),
      })
    }, 2000)
  }
}

window.EntryFormClass = EntryForm
