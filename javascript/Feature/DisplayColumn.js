'use strict'
import React from 'react'
import PropTypes from 'prop-types'

const DisplayColumn = ({format, bsClass, entry,}) => {
  const _class = 'story-feature ' + format
  const thumbnailStyle = {
    backgroundImage: `url('${entry.thumbnail}')`,
    backgroundPosition: '50% 50%'
  }
  return (
    <div className={bsClass}>
      <div className={_class}>
        <div className="story-thumbnail" style={thumbnailStyle}></div>
        <div className="story-content">
          <div className="story-title">
            <a>
              <h3>{entry.title}</h3>
            </a>
          </div>
          <div className="story-summary">{entry.strippedSummary}</div>
          <div className="published-date">Published {entry.publishDateRelative}
          </div>
        </div>
      </div>
    </div>
  )
}

DisplayColumn.propTypes = {
  bsClass: PropTypes.string,
  format: PropTypes.string,
  entry: PropTypes.object
}

DisplayColumn.defaultTypes = {}

export default DisplayColumn
