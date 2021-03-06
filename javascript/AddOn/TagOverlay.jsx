'use strict'
import React from 'react'
import PropTypes from 'prop-types'
import Overlay from '@essappstate/canopy-react-overlay'
import Tags from './Tags'
import 'react-select/dist/react-select.min.css'

const TagOverlay = ({
  saveTags,
  title,
  tags,
  entryTags,
  tagChange,
  newOptionClick,
  show
}) => {
  const closeButton = (
    <button className="btn btn-success btn-block" onClick={saveTags}>Save</button>
  )
  
  const closeButtonCSS = {
    position : 'absolute',
    width: '100%',
    bottom : '0',
    padding : '5px',
    left: 0
  }
  
  return (
    <Overlay
      close={saveTags}
      show={show}
      width="500px"
      height="350px"
      title={`Tag story: ${title}`}>
      <div className="mb-1">
        <Tags
          tags={tags}
          entryTags={entryTags}
          newOptionClick={newOptionClick}
          tagChange={tagChange}/>
      </div>
      <div style={closeButtonCSS}>
        {closeButton}
      </div>
    </Overlay>
  )
}

TagOverlay.propTypes = {  tags: PropTypes.array,
  saveTags: PropTypes.func,
  title: PropTypes.string,
  tagChange: PropTypes.func,
  entryTags: PropTypes.array,
  show: PropTypes.bool,
  newOptionClick: PropTypes.func,}

TagOverlay.defaultTypes = {}

export default TagOverlay
