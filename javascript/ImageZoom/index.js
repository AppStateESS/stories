'use strict'
/* global $ */
$('.medium-insert-images-grid figure img').click(function () {
  $('#image-zoom img').attr('src', this.src)
  $('#image-zoom').modal('show')
})
