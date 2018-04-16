'use strict'
import React from 'react'
import PropTypes from 'prop-types'
import Select from 'react-select'
import MoveButton from './MoveButton'
import 'react-select/dist/react-select.min.css'

/* global $ */

class DisplayColumn extends React.Component {
  constructor(props) {
    super(props)
    this.state = {
      showButtons: false,
      dragging: false,
    }
    this.onMouseMove = this.onMouseMove.bind(this)
    this.onMouseDown = this.onMouseDown.bind(this)
    this.onMouseUp = this.onMouseUp.bind(this)
  }

  componentDidUpdate(props, state) {
    if (this.state.dragging && !state.dragging) {
      document.addEventListener('mousemove', this.onMouseMove)
      document.addEventListener('mouseup', this.onMouseUp)
    } else if (!this.state.dragging && state.dragging) {
      document.removeEventListener('mousemove', this.onMouseMove)
      document.removeEventListener('mouseup', this.onMouseUp)
    }
  }

  onMouseDown(e) {
    // only left mouse button
    if (e.button !== 0) {
      return
    }
    this.setState({
      dragging: true,
    })
    e.stopPropagation()
    e.preventDefault()
  }
  onMouseUp(e) {
    this.setState({dragging: false})
    e.stopPropagation()
    e.preventDefault()
  }
  onMouseMove(e) {
    if (!this.state.dragging) {
      return
    }

    this.props.moveThumb(e.movementX, e.movementY)
      
    e.stopPropagation()
    e.preventDefault()
  }

  setShowButtons(value) {
    this.setState({showButtons: value})
  }

  render() {
    const {
      format,
      bsClass,
      entry,
      stories,
      applyStory,
      clearStory,
      previousEmpty,
    } = this.props
    const _class = 'story-feature ' + format
    const position = `${entry.x}% ${entry.y}%`
    const thumbnailStyle = {
      backgroundImage: `url('${entry.story.thumbnail}')`,
      backgroundPosition: position
    }

    let clearButton
    if (entry.entryId > 0) {
      clearButton = <button className="btn btn-primary btn-sm" onClick={clearStory}>Clear</button>
    }

    const selectCss = {
      width: '80%',
      float: 'left',
      marginRight: '10px',
    }

    let storyList = <em>No published stories available</em>
    if (stories !== undefined) {
      let storyOptions = stories.map(function (value) {
        return {value: value.id, label: value.title}
      })
      if (previousEmpty) {
        storyList = null
      } else {
        storyList = (
          <div className="mb-1">
            <div style={selectCss}>
              <Select options={storyOptions} value={0} onChange={applyStory}/>
            </div>{clearButton}
          </div>
        )
      }
    }
    let authorPic
    if (entry.story.authorPic != undefined && entry.story.authorPic.length > 0) {
      authorPic = (
        <div className="circle-frame">
          <img src={entry.story.authorPic}/>
        </div>
      )
    }
    return (
      <div className={bsClass}>
        <div className={_class}>
          <div
            className="story-thumbnail"
            style={thumbnailStyle}
            ref="thumbnail"
            onMouseDown={this.onMouseDown}></div>
          <div className="story-content">
            <div className="story-title">
              <a title="Read this story">
                <h4>{entry.story.title}</h4>
              </a>
            </div>
            <div className="story-summary">{entry.story.strippedSummary}</div>
          </div>
          <div className="publish-info">
            {authorPic}
            <div className="posted">
              <div className="author-name">{entry.story.authorName}</div>
              <div className="publish-date">{entry.story.publishDateRelative}
              </div>
            </div>
          </div>
        </div>
        {storyList}
      </div>
    )
  }
}

DisplayColumn.propTypes = {
  bsClass: PropTypes.string,
  format: PropTypes.string,
  entry: PropTypes.object,
  stories: PropTypes.array,
  applyStory: PropTypes.func,
  clearStory: PropTypes.func,
  moveThumb: PropTypes.func,
  stopMove: PropTypes.func,
  holdThumb: PropTypes.func,
  thumbnailForm: PropTypes.func,
  previousEmpty: PropTypes.bool
}

export default DisplayColumn
