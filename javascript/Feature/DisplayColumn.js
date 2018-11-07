'use strict'
import React from 'react'
import PropTypes from 'prop-types'
import Select from 'react-select'
import MoveButton from './MoveButton'
import 'react-select/dist/react-select.min.css'
import {maxZoom, minZoom} from './config'

/* global $ */

class DisplayColumn extends React.Component {
  constructor(props) {
    super(props)
    this.state = {
      showButtons: false,
      dragging: false
    }
    this.moveButtons = this.moveButtons.bind(this)
    this.zoomOut = this.zoomOut.bind(this)
    this.zoomIn = this.zoomIn.bind(this)
  }

  componentDidMount() {
    $('#feature-note').popover({
      html: true,
      content: '<span>To be featured, a story must be <strong>published</strong> with a title ' +
          'and image</span>',
      trigger: 'hover'
    })
  }

  setShowButtons(value) {
    this.setState({showButtons: value})
  }

  zoomOut() {
    const zoom = parseInt(this.props.story.zoom) - 5
    this.props.setZoom(zoom)
  }

  zoomIn() {
    const zoom = parseInt(this.props.story.zoom) + 5
    this.props.setZoom(zoom)
  }

  moveButtons() {
    const {moveThumb, stopMove, holdThumb, resetThumb, story} = this.props

    if (this.state.showButtons === false || story.id == 0) {
      return null
    }
    const zoom = story.zoom
    const cX = story.x
    const cY = story.y
    let uploadButton
    if (story.share === '0') {
      uploadButton = (
        <button
          className="btn btn-primary btn-sm upload-button"
          onClick={this.props.thumbnailForm}>
          <i className="fa fa-upload"></i>
        </button>
      )
    }
    return (
      <div className="thumbnail-buttons">
        {uploadButton}
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
                <td>
                  <button className="btn btn-secondary btn-sm" onClick={resetThumb}>
                    <i className="fas fa-fw fa-redo"></i>
                  </button>
                </td>
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
          <button
            disabled={zoom == minZoom}
            className="btn btn-sm btn-secondary"
            onClick={this.zoomOut}>
            <i className="fas fa-search-minus"></i>
          </button>
          <button
            disabled={zoom == maxZoom}
            className="btn btn-sm btn-secondary"
            onClick={this.zoomIn}>
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
      story,
      publishedTitles,
      applyStory,
      clearStory,
      savePosition,
      previousEmpty,
      updated
    } = this.props
    const _class = 'story-feature ' + format
    const position = `${story.x}% ${story.y}%`
    const zoom = story.zoom
    const thumbnailStyle = {
      backgroundImage: `url('${story.thumbnail}')`,
      backgroundSize: zoom + '%',
      backgroundPosition: position
    }

    let clearButton
    let saveButton
    if (story.id > 0) {
      clearButton = <button
        title="Remove story from feature list"
        className="btn btn-danger btn-sm"
        onClick={clearStory}>
        <i className="fas fa-times"></i>
      </button>

      if (updated) {
        saveButton = (
          <button
            title="Save updates"
            className="btn btn-success btn-sm mr-1"
            onClick={savePosition}>
            <i className="fas fa-save"></i>
          </button>
        )
      }
    }

    const selectCss = {
      width: '80%'
    }

    let storyList = <em>No published stories available</em>
    if (publishedTitles !== undefined) {
      let storyOptions = publishedTitles.map(function (value) {
        return {value: value.id, label: value.title}
      })
      if (previousEmpty) {
        storyList = null
      } else {
        storyList = (
          <div className="mb-1">
            <div className="clearfix">
              <div className="float-left mr-2" style={selectCss}>
                <Select options={storyOptions} value={0} onChange={applyStory}/>
              </div>
              <div
                className="badge badge-info pointer"
                id="feature-note"
                data-toggle="popover"
                data-container="body">?</div>
            </div>
            <div className="mt-2">{saveButton}{clearButton}</div>
          </div>
        )
      }
    }
    let authorPic
    if (story.authorPic != undefined && story.authorPic.length > 0) {
      authorPic = (
        <div className="circle-frame">
          <img src={story.authorPic}/>
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
                <h4>{story.title}</h4>
              </a>
            </div>
            <div className="story-summary">{story.summary}</div>
          </div>
          <div className="publish-info">
            {authorPic}
            <div className="posted">
              <div className="author-name">{story.authorName}</div>
              <div className="publish-date">{story.publishDateRelative}
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
  story: PropTypes.object,
  stories: PropTypes.array,
  applyStory: PropTypes.func,
  clearStory: PropTypes.func,
  moveThumb: PropTypes.func,
  resetThumb: PropTypes.func,
  stopMove: PropTypes.func,
  setZoom: PropTypes.func,
  holdThumb: PropTypes.func,
  thumbnailForm: PropTypes.func,
  updated: PropTypes.bool,
  previousEmpty: PropTypes.bool
}

export default DisplayColumn
