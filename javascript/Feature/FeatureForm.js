'use strict'
import React from 'react'
import PropTypes from 'prop-types'
import ButtonGroup from '../AddOn/ButtonGroup'
import FeatureDisplay from './FeatureDisplay'
import SampleEntry from './SampleEntry'
import './style.css'

/* global $ */

const FeatureForm = (props) => {

  const setColumns = (columns) => {
    const feature = props.feature
    feature.columns = columns
    props.update(feature)
  }

  const setTitle = (e) => {
    const feature = props.feature
    feature.title = e.target.value
    props.update(feature)
  }

  const setFormat = (format) => {
    const feature = props.feature
    feature.format = format
    props.update(feature)
  }

  const applyStory = (key, entry) => {
    const feature = props.feature
    $.ajax({
      url: './stories/Entry/' + entry.value,
      dataType: 'json',
      type: 'get',
      success: function (data) {
        feature.entries[key] = data.entry
        props.update(feature)
      }.bind(this),
      error: function () {}.bind(this),
    })
  }

  const clearStory = (key) => {
    const feature = props.feature
    feature.entries[key] = SampleEntry
    props.update(feature)
  }

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
    return props.feature.format === format
      ? 'btn btn-default active'
      : 'btn btn-default'
  }

  const formatTopBottom = props.srcHttp + 'mod/stories/img/top-bottom.png'
  const formatLandscape = props.srcHttp + 'mod/stories/img/landscape.png'
  const formatLeftRight = props.srcHttp + 'mod/stories/img/left-right.png'

  return (
    <div>
      <div className="settings">
        <div className="mb-1">
          <input
            type="text"
            className="form-control"
            placeholder="Feature title (not required)"
            value={props.feature.title}
            onChange={setTitle}/>
        </div>
        <div className="mb-1 row">
          <div className="col-sm-6">
            <label className="mr-1">Number of columns</label>
            <ButtonGroup
              buttons={columnButtons}
              handle={setColumns}
              match={props.feature.columns}/>
          </div>
          <div className="col-sm-6">
            <ul className="format-selection">
              <li>
                <button
                  className={isActive('landscape')}
                  onClick={setFormat.bind(null, 'landscape')}
                  title="Landscape image top"><img src={formatLandscape}/></button>
              </li>
              <li>
                <button
                  className={isActive('topbottom')}
                  onClick={setFormat.bind(null, 'topbottom')}
                  title="Narrow w/ square image top"><img src={formatTopBottom}/></button>
              </li>
              <li>
                <button
                  className={isActive('leftright')}
                  onClick={setFormat.bind(null, 'leftright')}
                  title="Square image to left side"><img src={formatLeftRight}/></button>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <div id="story-feature-list">
        <FeatureDisplay {...props} applyStory={applyStory} clearStory={clearStory}/>
      </div>
    </div>
  )
}

FeatureForm.propTypes = {
  feature: PropTypes.object,
  update: PropTypes.func,
  srcHttp: PropTypes.string,
}

FeatureForm.defaultTypes = {}

export default FeatureForm
