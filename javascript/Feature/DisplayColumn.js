'use strict'
import React from 'react'
import PropTypes from 'prop-types'
import Select from 'react-select'
import 'react-select/dist/react-select.min.css'

const DisplayColumn = ({
  format,
  bsClass,
  entry,
  stories,
  applyStory,
  clearStory,
}) => {
  const _class = 'story-feature ' + format
  const thumbnailStyle = {
    backgroundImage: `url('${entry.thumbnail}')`,
    backgroundPosition: '50% 50%',
  }

  let storyList = <em>No published stories available</em>
  if (stories !== undefined) {
    let storyOptions = stories.map(function (value) {
      return {value: value.id, label: value.title,}
    })
    storyList = (
      <div>
        <Select
          options={storyOptions}
          value={0}
          onChange={applyStory}/>
          <button className="btn btn-primary" onClick={clearStory}>Clear</button>
      </div>
    )
  }

  return (
    <div className={bsClass}>
      <div className={_class}>
        <div className="story-thumbnail" style={thumbnailStyle}></div>
        <div className="story-content">
          <div className="story-title">
            <a title="Link to story">
              <h3>{entry.title}</h3>
            </a>
          </div>
          <div className="story-summary">{entry.strippedSummary}</div>
          <div className="published-date">Published {entry.publishDateRelative}
          </div>
        </div>
      </div>
      {storyList}
    </div>
  )
}

DisplayColumn.propTypes = {
  bsClass: PropTypes.string,
  format: PropTypes.string,
  entry: PropTypes.object,
  stories: PropTypes.array,
  applyStory: PropTypes.func
}

DisplayColumn.defaultTypes = {}

export default DisplayColumn
