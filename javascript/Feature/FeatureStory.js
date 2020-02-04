'use strict'
/* global $ */
import React from 'react'
import PropTypes from 'prop-types'
import FeatureDisplay from './FeatureDisplay'
import Message from '../AddOn/Message'
import ThumbnailOverlay from '../AddOn/ThumbnailOverlay'
import StoryList from './StoryList'
import {maxZoom, minZoom} from './config'

import './style.css'

class FeatureStory extends React.Component {
  constructor(props) {
    super(props)

    this.state = {
      message: null,
      moving: false,
      currentStory: null,
      currentStoryKey: null,
      featureStories: [],
      publishedTitles: [],
      loading: true,
      updated: [],
      thumbnailOverlay: false
    }
    this.interval
    this.moveThumb = this.moveThumb.bind(this)
    this.holdThumb = this.holdThumb.bind(this)
    this.applyStory = this.applyStory.bind(this)
    this.clearStory = this.clearStory.bind(this)
    this.stopMove = this.stopMove.bind(this)
    this.savePosition = this.savePosition.bind(this)
    this.setZoom = this.setZoom.bind(this)
    this.closeOverlay = this.closeOverlay.bind(this)
    this.thumbnailForm = this.thumbnailForm.bind(this)
    this.saveThumbnail = this.saveThumbnail.bind(this)
    this.resetThumb = this.resetThumb.bind(this)
  }

  componentDidMount() {
    this.load()
    this.enablePopover()
  }

  enablePopover() {
    $('#feature-note').popover({
      html: true,
      content:
        '<span>To be featured, a story must be ' +
        '<strong>published</strong>, within the feature cutoff ' +
        'time, and have both a title and image</span>',
      trigger: 'hover'
    })
  }

  load() {
    $.ajax({
      url: 'stories/FeatureStory/',
      data: {
        featureId: this.props.feature.id
      },
      dataType: 'json',
      type: 'get',
      success: data => {
        this.setState({
          featureStories: data.featureStories,
          publishedTitles: data.publishedTitles
        })
      },
      error: () => {}
    })
  }

  message() {
    if (this.state.message !== null) {
      const {message} = this.state
      return (
        <Message type={message.type} message={message.text}>
          {message.text}
        </Message>
      )
    }
  }

  closeOverlay() {
    this.setState({
      thumbnailOverlay: false,
      currentStoryKey: null,
      currentStory: null
    })
  }

  thumbnailForm(key) {
    const currentStory = this.state.featureStories[key]
    this.setState({thumbnailOverlay: true, currentStoryKey: key, currentStory})
  }

  stopMove() {
    clearInterval(this.interval)
    this.setState({moving: false})
  }

  resetUpdate(key) {
    let updated = this.state.updated
    const loc = updated.indexOf(key)
    if (loc !== -1) {
      updated.splice(loc, 1)
    }
    this.setState({updated})
  }

  flagUpdate(key) {
    let updated = this.state.updated
    if (updated.indexOf(key) === -1) {
      updated.push(key)
    }
    this.setState({updated})
  }

  resetThumb(key) {
    const story = this.state.featureStories[key]
    story.zoom = '100'
    story.x = '50'
    story.y = '50'
    this.flagUpdate(key)
  }

  moveThumb(key, x, y, inc) {
    const mX = parseInt(x) * parseInt(inc)
    const mY = parseInt(y) * parseInt(inc)
    const story = this.state.featureStories[key]
    let newX = parseInt(story.x) + mX
    let newY = parseInt(story.y) + mY
    if (newX > 100) {
      newX = 100
    } else if (newX < 0) {
      newX = 0
    }

    if (newY > 100) {
      newY = 100
    } else if (newY < 0) {
      newY = 0
    }

    story.x = newX
    story.y = newY
    this.flagUpdate(key)
  }

  setZoom(key, zoom) {
    if (zoom > maxZoom || zoom < minZoom) {
      return
    }
    const story = this.state.featureStories[key]

    story.zoom = zoom
    this.flagUpdate(key)
  }

  savePosition(key) {
    const story = this.state.featureStories[key]
    $.ajax({
      url: `./stories/FeatureStory/${story.id}`,
      data: {
        params: ['x', 'y', 'zoom'],
        x: story.x,
        y: story.y,
        zoom: story.zoom
      },
      dataType: 'json',
      type: 'patch',
      success: function() {
        this.resetUpdate(key)
      }.bind(this),
      error: () => {}
    })
  }

  addStory(publishId) {
    $.ajax({
      url: 'stories/FeatureStory',
      data: {
        featureId: this.props.feature.id,
        publishId: publishId
      },
      dataType: 'json',
      type: 'post',
      success: () => {
        this.load()
      },
      error: () => {}
    })
  }

  applyStory(published) {
    this.addStory(published.value)
  }

  clearStory(key) {
    const story = this.state.featureStories[key]
    $.ajax({
      url: 'stories/FeatureStory/' + story.id,
      dataType: 'json',
      type: 'delete',
      success: () => {
        this.load()
      },
      error: () => {}
    })
  }

  holdThumb(key, x, y) {
    this.interval = setInterval(
      function() {
        this.setState({moving: true})
        this.moveThumb(key, x, y, 5, false)
      }.bind(this),
      100
    )
  }

  updateFormat(format) {
    this.props.update('format', format)
    this.props.save()
  }

  saveThumbnail(file, story) {
    let formData = new FormData()
    formData.append('image', file)
    formData.append('storyId', story.id)
    $.ajax({
      url: './stories/FeatureStory/updateThumbnail',
      data: formData,
      type: 'post',
      cache: false,
      dataType: 'json',
      processData: false,
      contentType: false,
      success: () => {
        this.closeOverlay()
        this.load()
      }
    })
  }

  render() {
    const formatTopBottom =
      this.props.srcHttp + 'mod/stories/img/top-bottom.png'
    const formatLandscape = this.props.srcHttp + 'mod/stories/img/landscape.png'
    const formatLeftRight =
      this.props.srcHttp + 'mod/stories/img/left-right.png'

    const isActive = format => {
      return this.props.feature.format === format
        ? 'btn btn-outline-dark active'
        : 'btn btn-outline-dark'
    }
    const {update, save} = this.props
    let thumbnailOverlay
    if (this.state.currentStoryKey !== null) {
      thumbnailOverlay = (
        <ThumbnailOverlay
          thumbnailOverlay={this.state.thumbnailOverlay}
          updateEntry={this.updateStory}
          entry={this.state.currentStory}
          close={this.closeOverlay}
          saveThumbnail={this.saveThumbnail}
        />
      )
    }

    return (
      <div>
        {thumbnailOverlay}
        {this.message()}
        <div className="settings">
          <div className="mb-1">
            <input
              type="text"
              className="form-control"
              placeholder="Feature title (not required)"
              value={this.props.feature.title}
              onBlur={save}
              onChange={update.bind(null, 'title')}
            />
          </div>
          <div className="mb-1 row">
            <div className="col-sm-6">
              <ul className="format-selection">
                <li>
                  <button
                    className={isActive('landscape')}
                    onClick={this.updateFormat.bind(this, 'landscape')}
                    title="Landscape image top">
                    <img src={formatLandscape} />
                  </button>
                </li>
                <li>
                  <button
                    className={isActive('topbottom')}
                    onClick={this.updateFormat.bind(this, 'topbottom')}
                    title="Narrow w/ square image top">
                    <img src={formatTopBottom} />
                  </button>
                </li>
                <li>
                  <button
                    className={isActive('leftright')}
                    onClick={this.updateFormat.bind(this, 'leftright')}
                    title="Square image to left side">
                    <img src={formatLeftRight} />
                  </button>
                </li>
              </ul>
            </div>
          </div>
        </div>
        <div id="story-feature-list">
          <FeatureDisplay
            updated={this.state.updated}
            format={this.props.feature.format}
            moveThumb={this.moveThumb}
            holdThumb={this.holdThumb}
            resetThumb={this.resetThumb}
            thumbnailForm={this.thumbnailForm}
            setZoom={this.setZoom}
            stopMove={this.stopMove}
            srcHttp={this.props.srcHttp}
            applyStory={this.applyStory}
            clearStory={this.clearStory}
            savePosition={this.savePosition}
            featureStories={this.state.featureStories}
            publishedTitles={this.state.publishedTitles}
          />
        </div>
        <hr />
        <div className="row justify-content-sm-center">
          <div className="col-sm-8 col-md-6">
            <StoryList
              titles={this.state.publishedTitles}
              applyStory={this.applyStory}
            />
          </div>
        </div>
      </div>
    )
  }
}
FeatureStory.propTypes = {
  feature: PropTypes.object,
  update: PropTypes.func,
  save: PropTypes.func,
  srcHttp: PropTypes.string
}

FeatureStory.defaultTypes = {}

export default FeatureStory
