'use strict'
import React from 'react'
import PropTypes from 'prop-types'
import DisplayColumn from './DisplayColumn'

const FeatureDisplay = (props) => {
  const {feature} = props
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
    //columnContent.push(<div className={bsClass} key={i}>columns!</div>)
    let entry = {
      id: 0,
      title: 'Example title',
      strippedSummary: 'Example summary',
      publishDateRelative: 'today',
      thumbnail: 'mod/stories/img/sample.jpg',
    }
    if (feature.entries[i] !== undefined) {
      entry = feature.entries[i]
    }
    columnContent.push(<DisplayColumn key={i} bsClass={bsClass} format={feature.format} entry={entry}/>)
  }

  return (
    <div className="row">
      {columnContent}
    </div>
  )
}

FeatureDisplay.propTypes = {
  feature: PropTypes.object
}

FeatureDisplay.defaultTypes = {}

export default FeatureDisplay
