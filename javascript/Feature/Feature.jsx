'use strict'
import React, {Component} from 'react'
import PropTypes from 'prop-types'
import FeatureObj from './FeatureObj'
import FeatureList from './FeatureList'
import FeatureForm from './FeatureForm'
import Message from '../AddOn/Message'
import Waiting from '../AddOn/Waiting'
import SampleEntry from './SampleEntry'

/* global $ */

export default class Feature extends Component {
  constructor(props) {
    super(props)
    this.state = {
      message: null,
      currentFeature: null,
      currentKey: null,
      featureList: [],
      loading: true,
      stories: [],
    }
    this.addRow = this.addRow.bind(this)
    this.closeMessage = this.closeMessage.bind(this)
    this.updateFeature = this.updateFeature.bind(this)
    this.updateActive = this.updateActive.bind(this)
    this.loadCurrentFeature = this.loadCurrentFeature.bind(this)
  }

  componentDidMount() {
    this.load()
  }

  load() {
    $.ajax({
      url: './stories/Feature',
      dataType: 'json',
      type: 'get',
      success: function (data) {
        if (data.stories == null) {
          const message = {}
          message.text = <span>No stories available for features.
            <a href="stories/Entry/create">Go write some.</a>
          </span>
          message.type = 'warning'
          this.setState({message: message, loading: false})
        } else {
          this.setState({featureList: data.featureList, stories: data.stories, loading: false})
        }
      }.bind(this),
      error: function () {
        this.setState({
          loading: false,
          message: {
            text: 'Error: Could not pull feature list',
            type: 'danger'
          }
        })
      }.bind(this)
    })
  }

  loadCurrentFeature(key) {
    const feature = this.state.featureList[key]
    if (feature.entries === null || feature.entries === 'null') {
      feature.entries = []
    }
    this.fillEntries(feature)
    if (feature.title === null) {
      feature.title = ''
    }
    this.setState({currentFeature: feature, currentKey: key,})
  }

  fillEntries(feature) {
    for (let i = 0; i < 4; i++) {
      if (feature.entries[i] === undefined) {
        feature.entries[i] = SampleEntry()
      }
    }
  }

  addRow() {
    $.ajax({
      url: './stories/Feature',
      dataType: 'json',
      type: 'post',
      success: function (data) {
        const feature = FeatureObj
        feature.id = data.featureId
        let featureList = this.state.featureList
        if (featureList === null) {
          featureList = []
        }
        featureList.push(feature)
        this.setState({currentFeature: FeatureObj, currentKey: data.featureId, featureList: featureList})
      }.bind(this),
      error: function () {}.bind(this),
    })
  }

  updateFeature(feature) {
    if (feature.id > 0) {
      const {
        title,
        active,
        entries,
        format,
        columns,
        sorting
      } = feature
      let newEntries = entries.map(function (value) {
        return {id: value.id, x: value.x, y: value.y}
      })
      $.ajax({
        url: './stories/Feature/' + feature.id,
        data: {
          title,
          active,
          entries: newEntries,
          format,
          columns,
          sorting
        },
        dataType: 'json',
        type: 'put',
        success: function () {}.bind(this),
        error: function () {}.bind(this),
      })
    }
    this.setState({currentFeature: feature})
  }

  closeMessage() {
    this.setState({message: null})
  }

  updateActive(key, value) {
    const feature = this.state.featureList[key]
    feature.active = value
    this.updateFeature(feature)
  }

  message() {
    if (this.state.message !== null) {
      const {message} = this.state
      return <Message type={message.type} message={message.text}>{message.text}</Message>
    }
  }

  getListing() {
    if (this.state.loading === true) {
      return <Waiting/>
    } else if (this.state.currentKey !== null) {
      return <FeatureForm
        stories={this.state.stories}
        feature={this.state.currentFeature}
        srcHttp={this.props.srcHttp}
        update={this.updateFeature}/>
    } else {
      return <FeatureList
        list={this.state.featureList}
        srcHttp={this.props.srcHttp}
        updateActive={this.updateActive}
        loadCurrentFeature={this.loadCurrentFeature}/>
    }
  }

  render() {
    return (
      <div>
        {this.message()}
        <button className="btn btn-primary mb-1" onClick={this.addRow}>Add feature row</button>
        {this.getListing()}
      </div>
    )
  }
}

Feature.propTypes = {
  srcHttp: PropTypes.string
}
