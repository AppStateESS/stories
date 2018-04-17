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
      dragging: false
    }
    this.zoomOut = this.zoomOut.bind(this)
    this.zoomIn = this.zoomIn.bind(this)
    this.moveButtons = this.moveButtons.bind(this)
  }

  setShowButtons(value) {
    this.setState({showButtons: value})
  }

  zoomOut() {
    const zoom = parseInt(this.props.entry.zoom) - 5
    this.props.setZoom(zoom)
  }

  zoomIn() {
    const zoom = parseInt(this.props.entry.zoom) + 5
    this.props.setZoom(zoom)
  }

  moveButtons() {
    const {moveThumb, stopMove, holdThumb, entry} = this.props

    if (this.state.showButtons === false || entry.entryId == 0) {
      return null
    }
    const zoom = entry.zoom
    const cX = entry.x
    const cY = entry.y
    return (
      <div className="thumbnail-buttons">
        <button
          className="btn btn-primary btn-sm upload-button"
          onClick={this.props.thumbnailForm}>
          <i className="fa fa-upload"></i>
        </button>
        <div className="move-buttons">
          <table>
            <tbody>
              <tr>
                <td></td>
                <td>
                  <MoveButton dir="up" {...{holdThumb, stopMove, moveThumb,cX, cY}}/>
                </td>
                <td></td>
              </tr>
              <tr>
                <td>
                  <MoveButton dir="left" {...{holdThumb, stopMove, moveThumb,cX, cY}}/>
                </td>
                <td></td>
                <td>
                  <MoveButton dir="right" {...{holdThumb, stopMove, moveThumb,cX, cY}}/>
                </td>
              </tr>
              <tr>
                <td></td>
                <td>
                  <MoveButton dir="down" {...{holdThumb, stopMove, moveThumb,cX, cY}}/>
                </td>
                <td></td>
              </tr>
            </tbody>
          </table>
        </div>
        <div className="zoom-buttons">
          <button disabled={zoom == 50} className="btn btn-sm btn-secondary" onClick={this.zoomOut}>
            <i className="fas fa-search-minus"></i>
          </button>
          <button disabled={zoom == 200} className="btn btn-sm btn-secondary" onClick={this.zoomIn}>
            <i className="fas fa-search-plus"></i>
          </button>
        </div>
      </div>
    )
  }

  render() {
    const {
      format,
      bsClass,
      entry,
      stories,
      applyStory,
      clearStory,
      previousEmpty
    } = this.props
    const _class = 'story-feature ' + format
    const position = `${entry.x}% ${entry.y}%`
    const zoom = entry.zoom
    const thumbnailStyle = {
      backgroundImage: `url('${entry.story.thumbnail}')`,
      backgroundSize: zoom + '%',
      backgroundPosition: position,
    }

    let clearButton
    if (entry.entryId > 0) {
      clearButton = <button className="btn btn-primary btn-sm" onClick={clearStory}>Clear</button>
    }

    const selectCss = {
      width: '80%',
      float: 'left',
      marginRight: '10px'
    }

    let storyList = <em>No published stories available</em>
    if (stories !== undefined) {
      let storyOptions = stories.map(function (value) {
        return {value: value.id, label: value.title,}
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
            onMouseEnter={this.setShowButtons.bind(this, true)}
            onMouseLeave={this.setShowButtons.bind(this, false)}>{this.moveButtons()}</div>
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
  setZoom: PropTypes.func,
  holdThumb: PropTypes.func,
  thumbnailForm: PropTypes.func,
  previousEmpty: PropTypes.bool,
}

export default DisplayColumn
