'use strict'
import React from 'react'
import Select from 'react-select'
import PropTypes from 'prop-types'

const StoryList = ({titles, applyStory}) => {

  const selectCss = {
    width: '100%'
  }
  if (titles.length === 0) {
    return <em>No published stories available</em>
  }
  let storyOptions = titles.map(function (value) {
    return {value: value.id, label: value.title}
  })
  // if (previousEmpty) {   return null } else {
  return (
    <div className="mb-1">
      <div className="clearfix">
        <div className="float-left mr-2" style={selectCss}>
          Add feature:&nbsp;<div
            className="badge badge-info pointer"
            id="feature-note"
            data-toggle="popover"
            data-container="body">?</div>
          <Select options={storyOptions} value={0} onChange={applyStory}/>
        </div>
      </div>
    </div>
  )
  // }
}

StoryList.propTypes = {
  titles: PropTypes.array,
  applyStory: PropTypes.func
}

StoryList.defaultProps = {
  titles: []
}

export default StoryList
