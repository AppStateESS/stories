require("expose-loader?$!jquery")
/* global $ */
$.noConflict()
let currentDom = ''
$(function () {
  $(".tagged").popover({
    trigger: "manual",
    html: true,
    animation: false,
    placement: 'bottom',
    content: function () {
      return currentDom
    },
  }).on("mouseenter", function () {
    var _this = this
    const entryDom = '#entry-' + $(this).data('entryId')
    currentDom = $(entryDom).html()
    $(this).popover("show")
    $(".popover").on("mouseleave", function () {
      $(_this).popover('hide')
    })
  }).on("mouseleave", function () {
    var _this = this
    setTimeout(function () {
      if (!$(".popover:hover").length) {
        $(_this).popover("hide")
      }
    }, 300)
  })
})
