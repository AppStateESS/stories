'use strict'
/* global $ */
$('.medium-insert-images-grid figure img').click(function () {
  if (this.naturalWidth > this.clientWidth) {
    $('#image-zoom img').attr('src', this.src)
    $('#image-zoom').modal('show')
  }
})
