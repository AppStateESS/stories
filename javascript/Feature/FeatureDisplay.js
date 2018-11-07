'use strict'
import React from 'react'
import PropTypes from 'prop-types'
import DisplayColumn from './DisplayColumn'
import SampleStory from './SampleStory'

const FeatureDisplay = ({
  featureStories,
  publishedTitles,
  srcHttp,
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
  let bsClass = 'col-12'

  switch (featureStories.length) {
    case 0:
      bsClass = 'col-12'
      break

    case 1:
      bsClass = 'col-sm-6'
      break

    case 2:
      bsClass = 'col-sm-4'
      break

    default:
      bsClass = 'col-sm-3'
      break
  }

  let formDisplay = <DisplayColumn
    bsClass={bsClass}
    story={SampleStory(srcHttp)}
    applyStory={applyStory.bind(null, -1)}
    publishedTitles={publishedTitles}
    format={format}/>

  let storyList = featureStories.map((value, key) => {
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
  updated: PropTypes.array,
}

export default FeatureDisplay
