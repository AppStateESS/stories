'use strict'
import React from 'react'
import PropTypes from 'prop-types'
import ButtonGroup from '../AddOn/ButtonGroup'
import FeatureDisplay from './FeatureDisplay'
import './style.css'

class FeatureForm extends React.Component {
  constructor(props) {
    super(props)
    this.interval
    this.setColumns = this.setColumns.bind(this)
    this.setTitle = this.setTitle.bind(this)
    this.setFormat = this.setFormat.bind(this)
    this.stopMove = this.stopMove.bind(this)
    this.moveThumb = this.moveThumb.bind(this)
    this.holdThumb = this.holdThumb.bind(this)
    this.applyStory = this.applyStory.bind(this)
    this.saveTitle = this.saveTitle.bind(this)
  }

  setColumns(columns) {
    const feature = this.props.feature
    feature.columns = columns
    this.props.update(feature)
  }

  shiftEntries(feature) {
    let newEntries = feature.entries.map(function (value) {
      if (value.entryId > 0) {
        return value
      }
      feature.entries = newEntries
    }.bind(this))
  }

  saveTitle() {
    this.props.update(this.props.feature)
  }

  setTitle(e) {
    this.props.updateTitle(e.target.value)
  }

  setFormat(format) {
    const feature = this.props.feature
    feature.format = format
    this.props.update(feature)
  }

  stopMove() {
    clearInterval(this.interval)
  }

  holdThumb(key, x, y) {
    this.interval = setInterval(function () {
      this.moveThumb(key, x, y, 5)
    }.bind(this), 100)
  }

  moveThumb(key, x, y) {
    const feature = this.props.feature
    const entry = feature.entries[key]
    let newX = parseInt(entry.x) - x
    let newY = parseInt(entry.y) - y
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

    entry.x = newX
    entry.y = newY
    feature.entries[key] = entry
    this.props.update(feature)
  }

  applyStory(key, entry) {
    const feature = this.props.feature
    feature.entries[key].entryId = entry.value
    this.props.update(feature)
  }

  render() {
    const columnButtons = [
      {
        value: '2',
        label: '2'
      }, {
        value: '3',
        label: '3'
      }, {
        value: '4',
        label: '4'
      },
    ]

    const isActive = (format) => {
      return this.props.feature.format === format
        ? 'btn btn-default active'
        : 'btn btn-default'
    }

    const formatTopBottom = this.props.srcHttp + 'mod/stories/img/top-bottom.png'
    const formatLandscape = this.props.srcHttp + 'mod/stories/img/landscape.png'
    const formatLeftRight = this.props.srcHttp + 'mod/stories/img/left-right.png'

    return (
      <div>
        <div className="settings">
          <div className="mb-1">
            <input
              type="text"
              className="form-control"
              placeholder="Feature title (not required)"
              value={this.props.feature.title}
              onBlur={this.saveTitle}
              onChange={this.setTitle}/>
          </div>
          <div className="mb-1 row">
            <div className="col-sm-6">
              <label className="mr-1">Number of columns</label>
              <ButtonGroup
                buttons={columnButtons}
                handle={this.setColumns}
                match={this.props.feature.columns}/>
            </div>
            <div className="col-sm-6">
              <ul className="format-selection">
                <li>
                  <button
                    className={isActive('landscape')}
                    onClick={this.setFormat.bind(null, 'landscape')}
                    title="Landscape image top"><img src={formatLandscape}/></button>
                </li>
                <li>
                  <button
                    className={isActive('topbottom')}
                    onClick={this.setFormat.bind(null, 'topbottom')}
                    title="Narrow w/ square image top"><img src={formatTopBottom}/></button>
                </li>
                <li>
                  <button
                    className={isActive('leftright')}
                    onClick={this.setFormat.bind(null, 'leftright')}
                    title="Square image to left side"><img src={formatLeftRight}/></button>
                </li>
              </ul>
            </div>
          </div>
        </div>
        <div id="story-feature-list">
          <FeatureDisplay
            {...this.props}
            applyStory={this.applyStory}
            clearStory={this.props.clearStory}
            moveThumb={this.moveThumb}
            thumbnailForm={this.props.thumbnailForm}
            holdThumb={this.holdThumb}
            stopMove={this.stopMove}/>
        </div>
      </div>
    )
  }
}

FeatureForm.propTypes = {
  feature: PropTypes.object,
  updateTitle: PropTypes.func,
  thumbnailForm: PropTypes.func,
  update: PropTypes.func,
  clearStory: PropTypes.func,
  srcHttp: PropTypes.string,
}

FeatureForm.defaultTypes = {}

export default FeatureForm
