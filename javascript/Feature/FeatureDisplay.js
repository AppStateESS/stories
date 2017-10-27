'use strict'
import React from 'react'
import PropTypes from 'prop-types'
import DisplayColumn from './DisplayColumn'

const FeatureDisplay = (props) => {
  const {feature, applyStory, stories, clearStory,} = props
  const columns = parseInt(feature.columns)
  let bsClass

  switch (columns) {
    case 2:
      bsClass = 'col-sm-6'
      break
    case 3:
      bsClass = 'col-sm-4'
      break
    case 4:
      bsClass = 'col-sm-3'
      break
  }

  let columnContent = []
  for (let i = 0; i < columns; i++) {
    columnContent.push(<DisplayColumn
      key={i}
      bsClass={bsClass}
      format={feature.format}
      entry={feature.entries[i]}
      stories={stories}
      applyStory={applyStory.bind(null, i)}
      clearStory={clearStory.bind(null, i)}/>)
  }

  return (
    <div className="row">
      {columnContent}
    </div>
  )
}

FeatureDisplay.propTypes = {
  feature: PropTypes.object,
  stories: PropTypes.array,
  applyStory: PropTypes.func,
  clearStory: PropTypes.func,
}

FeatureDisplay.defaultTypes = {}

export default FeatureDisplay
