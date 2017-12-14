require("expose-loader?$!jquery")
import 'tooltipster/dist/css/tooltipster.bundle.min.css'
import 'tooltipster/dist/css/plugins/tooltipster/sideTip/themes/tooltipster-sideTip-light.min.css'
require ('tooltipster/dist/js/tooltipster.bundle')($)

/* global $ */
$('.tagged').tooltipster({
  theme: ['tooltipster-light'],
  contentAsHTML: true,
  interactive: true,
  side: 'bottom',
  trigger: 'hover',
  animation: 'grow',
})

const getTags = (id) => {
  const idName = '#entry-' + id
  return $(idName).html()
}

$('.tagged').hover(function(){
  const entryId = $(this).data('entry-id')
  $(this).tooltipster('content', getTags(entryId))
  $(this).tooltipster('open')
})
