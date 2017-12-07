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

  let tempStoryList = []
  for (let x = 0; x < stories.length; x++) {
    tempStoryList[x] = stories[x]
  }

  for (let i = 0; i < columns; i++) {
    currentEntry = feature.entries[i]
    for (let j = 0; j < tempStoryList.length; j++) {
      if(tempStoryList[j].id == currentEntry.entryId) {
        tempStoryList.splice(j,1)
      }
    }
    columnContent.push(
      <DisplayColumn
        key={i}
        bsClass={bsClass}
        previousEmpty={previousEmpty}
        format={feature.format}
        entry={currentEntry}
        stories={tempStoryList}
        applyStory={applyStory.bind(null, i)}
        clearStory={clearStory.bind(null, i)}
        stopMove={stopMove}
        thumbnailForm={props.thumbnailForm.bind(null, i)}
        moveThumb={moveThumb.bind(null, i)}
        holdThumb={holdThumb.bind(null, i)}/>
    )
    if (currentEntry.entryId == 0) {
      previousEmpty = true
    }
  }
  return (<div className="feature-row row">
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
