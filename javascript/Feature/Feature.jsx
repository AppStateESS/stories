'use strict'
import React, {Component} from 'react'
import PropTypes from 'prop-types'
import FeatureObj from './FeatureObj'
import FeatureList from './FeatureList'
import FeatureStory from './FeatureStory'
import Message from '../AddOn/Message'
import Waiting from '../AddOn/Waiting'
import Navbar from '../AddOn/Navbar'
import './style.css'

/* global $ */

export default class Feature extends Component {
  constructor(props) {
    super(props)
    this.state = {
      message: null,
      currentFeature: Object.assign({}, FeatureObj),
      currentFeatureKey: null,
      featureList: [],
      loading: true,
      thumbnailOverlay: false
    }
    this.save = this.save.bind(this)
    this.addRow = this.addRow.bind(this)
    this.clearFeature = this.clearFeature.bind(this)
    this.closeMessage = this.closeMessage.bind(this)
    this.deleteFeature = this.deleteFeature.bind(this)
    this.loadCurrentFeature = this.loadCurrentFeature.bind(this)
    this.updateActive = this.updateActive.bind(this)
    this.updateFeatureValue = this.updateFeatureValue.bind(this)
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
        if (data.featureList.length === 0) {
          data.featureList = null
        }
        this.setState(
          {featureList: data.featureList, loading: false, currentFeature: null, currentFeatureKey: null}
        )
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

  closeMessage() {
    this.setState({message: null})
  }

  deleteFeature(key) {
    const feature = this.state.featureList[key]
    $.ajax({
      url: 'stories/Feature/' + feature.id,
      dataType: 'json',
      type: 'delete',
      success: function () {
        this.load()
      }.bind(this),
      error: function () {}.bind(this)
    })
  }

  loadCurrentFeature(key) {
    const feature = this.state.featureList[key]
    if (feature.title === null) {
      feature.title = ''
    }
    this.setState({currentFeature: feature, currentFeatureKey: key})
  }

  clearFeature() {
    this.setState({currentFeature: null, currentFeatureKey: null})
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
        this.setState(
          {currentFeature: FeatureObj, currentFeatureKey: data.featureId, featureList: featureList}
        )
      }.bind(this),
      error: function () {}.bind(this)
    })
  }

  updateFeatureValue(param, value) {
    if (typeof value === 'object') {
      value = value.target.value
    }
    const {currentFeature} = this.state
    currentFeature[param] = value
    this.setState({currentFeature})
  }

  save() {
    const {
      title,
      active,
      format,
      columns,
      sorting,
      id
    } = this.state.currentFeature
    $.ajax({
      url: './stories/Feature/' + id,
      data: {
        title,
        active,
        format,
        columns,
        sorting
      },
      dataType: 'json',
      type: 'put',
      success: function () {},
      error: function () {}
    })
  }

  updateCurrentFeature(feature) {
    const {featureList} = this.state
    featureList[this.state.currentFeatureKey] = feature
    this.setState({currentFeature: feature, featureList: featureList})
  }

  updateActive(key, value) {
    const currentFeature = this.state.featureList[key]
    currentFeature.active = value
    this.setState({
      currentFeature
    }, this.save)
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
    } else if (this.state.currentFeatureKey !== null) {
      return <FeatureStory
        feature={this.state.currentFeature}
        update={this.updateFeatureValue}
        save={this.save}
        srcHttp={this.props.srcHttp}/>
    } else {
      return <FeatureList
        add={this.addRow}
        list={this.state.featureList}
        srcHttp={this.props.srcHttp}
        updateActive={this.updateActive}
        deleteFeature={this.deleteFeature}
        loadCurrentFeature={this.loadCurrentFeature}/>
    }
  }

  render() {
    let leftSide = []

    if (this.state.currentFeatureKey !== null) {
      leftSide = (
        <li key="1">
          <span onClick={this.clearFeature} className="navbar-text pointer">
            <i className="fas fa-list"></i>&nbsp;Back to feature list</span>
        </li>
      )
    } else {
      leftSide = (
        <li key="2">
          <span onClick={this.addRow} className="navbar-text pointer">
            <i className="fas fa-plus"></i>&nbsp;Add feature set</span>
        </li>
      )
    }

    return (
      <div className="feature-admin">
        <Navbar header="Features" leftSide={leftSide}/> {this.getListing()}
      </div>
    )
  }
}

Feature.propTypes = {
  srcHttp: PropTypes.string
}
