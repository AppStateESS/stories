'use strict'
/* global $ */
$('.medium-insert-images* figure img').click(function () {
  $('#image-zoom img').attr('src', this.src)
  $('#image-zoom').show()
})

$('#close-zoom').click(() => {
  $('#image-zoom img').attr('src', '')
  $('#image-zoom').hide()
})
