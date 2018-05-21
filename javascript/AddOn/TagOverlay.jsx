'use strict'
import React from 'react'
import PropTypes from 'prop-types'
import Overlay from 'canopy-react-overlay'
import Tags from './Tags'
import 'react-select/dist/react-select.min.css'

const TagOverlay = ({
  saveTags,
  title,
  tags,
  entryTags,
  tagChange,
  newOptionClick,
}) => {
  const closeButton = (
    <button className="btn btn-outline-dark btn-block" onClick={saveTags}>Close</button>
  )
  return (
    <Overlay
      close={saveTags}
      width="500px"
      height="250px"
      title={`Tag story: ${title}`}>
      <div className="mb-1">
        <Tags
          tags={tags}
          entryTags={entryTags}
          newOptionClick={newOptionClick}
          tagChange={tagChange}/>
      </div>
      <div className="text-center">
        <div>{closeButton}</div>
      </div>
    </Overlay>
  )
}

TagOverlay.propTypes = {  tags: PropTypes.array,
  saveTags: PropTypes.func,
  title: PropTypes.string,
  tagChange: PropTypes.func,
  entryTags: PropTypes.array,
  newOptionClick: PropTypes.func,}

TagOverlay.defaultTypes = {}

export default TagOverlay
