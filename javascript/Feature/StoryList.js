'use strict'
import React from 'react'
import Select from 'react-select'
import PropTypes from 'prop-types'

const StoryList = ({titles, applyStory}) => {

  const selectCss = {
    width: '100%'
  }
  let storiesNotAvailable = 'd-block'
  let storiesAvailable = 'd-block'

  if (titles.length === 0) {
    storiesAvailable = 'd-none'
  } else {
    storiesNotAvailable = 'd-none'
  }
  let storyOptions = titles.map(function (value) {
    return {value: value.id, label: value.title}
  })
  return (
    <div className="mb-1">
      <div className={storiesNotAvailable}>
        <em>No published stories available</em>
      </div>
      <div className={storiesAvailable}>
        <div className="clearfix">
          <div className="float-left mr-2" style={selectCss}>
            Add feature:
            <Select options={storyOptions} value={0} onChange={applyStory}/>
          </div>
        </div>
      </div>
      <div
        className="badge badge-info pointer"
        id="feature-note"
        data-toggle="popover"
        data-container="body">Story not available?</div>
    </div>
  )
}

StoryList.propTypes = {
  titles: PropTypes.array,
  applyStory: PropTypes.func
}

StoryList.defaultProps = {
  titles: []
}

export default StoryList
