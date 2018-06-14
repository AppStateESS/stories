/* global $ */
const resizeCaption = () => {
  const containerWidth = $('.container').width()
  $('figure').each(function (i, obj) {
    const image = $(obj).find('img')
    const caption = $(obj).find('figcaption')
    const imageWidth = image.width()
    const leftMargin = (containerWidth - imageWidth) / 2
    caption.css('margin-left', leftMargin)
  })

}
resizeCaption()
$(window).resize(resizeCaption)
