'use strict'
import React from 'react'
import PropTypes from 'prop-types'
import DisplayColumn from './DisplayColumn'

const FeatureDisplay = ({
  featureStories,
  publishedTitles,
  format,
  moveThumb,
  holdThumb,
  setZoom,
  stopMove,
  savePosition,
  applyStory,
  clearStory,
  updated,
  thumbnailForm
}) => {
  let storyCount = featureStories.length

  let formDisplay
  
  const getBsClass = (storyCount, currentCount) => {
    switch (storyCount) {
      case 1:
        return 'col-12'
      case 2:
        return 'col-sm-6'
      case 3:
        return 'col-sm-4'
      case 4:
      case 8:
        return 'col-md-3 col-sm-6'
      case 5:
        if (currentCount > 3) {
          return 'col-sm-6'
        } else {
          return 'col-sm-4'
        }
      case 6:
        return 'col-sm-4'
      case 7:
        if (currentCount == 4) {
          return 'col-md-3 col-sm-6'
        } else if (currentCount >= 4) {
          return 'col-md-4 col-sm-6'
        } else {
          return 'col-sm-4 col-md-3'
        }
      default:
        return 'col-sm-3'
    }

  }

  let storyList = featureStories.map((value, key) => {
    const bsClass = getBsClass(storyCount, key + 1)
    return (
      <DisplayColumn
        key={key}
        bsClass={bsClass}
        story={value}
        publishedTitles={publishedTitles}
        thumbnailForm={thumbnailForm.bind(null, key)}
        applyStory={applyStory.bind(null, key)}
        clearStory={clearStory.bind(null, key)}
        savePosition={savePosition.bind(null, key)}
        stopMove={stopMove.bind(null, key)}
        moveThumb={moveThumb.bind(null, key)}
        holdThumb={holdThumb.bind(null, key)}
        setZoom={setZoom.bind(null, key)}
        updated={updated.indexOf(key) !== -1}
        format={format}/>
    )
  })

  return (<div className="row">{storyList}{formDisplay}</div>)
}

FeatureDisplay.propTypes = {
  featureStories: PropTypes.array,
  publishedTitles: PropTypes.array,
  srcHttp: PropTypes.string,
  format: PropTypes.string,
  moveThumb: PropTypes.func,
  stopMove: PropTypes.func,
  holdThumb: PropTypes.func,
  applyStory: PropTypes.func,
  clearStory: PropTypes.func,
  setZoom: PropTypes.func,
  savePosition: PropTypes.func,
  thumbnailForm: PropTypes.func,
  updated: PropTypes.array
}

export default FeatureDisplay
