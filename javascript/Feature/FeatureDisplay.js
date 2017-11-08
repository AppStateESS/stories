'use strict'
import React from 'react'
import PropTypes from 'prop-types'
import DisplayColumn from './DisplayColumn'

const FeatureDisplay = (props) => {
  const {
    feature,
    applyStory,
    stories,
    clearStory,
    moveThumb,
    holdThumb,
    stopMove,
  } = props
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
  let previousEmpty = false
  let currentEntry = {}
  for (let i = 0; i < columns; i++) {
    currentEntry = feature.entries[i]
    columnContent.push(
      <DisplayColumn
        key={i}
        bsClass={bsClass}
        previousEmpty={previousEmpty}
        format={feature.format}
        entry={currentEntry}
        stories={stories}
        applyStory={applyStory.bind(null, i)}
        clearStory={clearStory.bind(null, i)}
        stopMove={stopMove}
        thumbnailForm={props.thumbnailForm.bind(null, i)}
        moveThumb={moveThumb.bind(null, i)}
        holdThumb={holdThumb.bind(null, i)}/>
    )
    if (currentEntry.id == 0) {
      previousEmpty = true
    }
  }

  return (<div className="row">
    {columnContent}
  </div>)
}

FeatureDisplay.propTypes = {
  feature: PropTypes.object,
  stories: PropTypes.array,
  applyStory: PropTypes.func,
  clearStory: PropTypes.func,
  holdThumb: PropTypes.func,
  moveThumb: PropTypes.func,
  thumbnailForm: PropTypes.func,
  stopMove: PropTypes.func
}

FeatureDisplay.defaultTypes = {}

export default FeatureDisplay
