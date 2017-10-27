'use strict'
import React, {Component} from 'react'
import PropTypes from 'prop-types'
import FeatureObj from './FeatureObj'
import FeatureList from './FeatureList'
import FeatureForm from './FeatureForm'
import Message from '../AddOn/Message'
import Waiting from '../AddOn/Waiting'

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
    }
    this.addRow = this.addRow.bind(this)
    this.closeMessage = this.closeMessage.bind(this)
    this.updateFeature = this.updateFeature.bind(this)
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
        const featureList = data
        this.setState({featureList: featureList, loading: false})
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
    if (feature.entries === 'null') {
      feature.entries = []
    }

    if (feature.title === null) {
      feature.title = ''
    }
    this.setState({currentFeature: feature, currentKey: key,})
  }

  addRow() {
    $.ajax({
      url: './stories/Feature',
      dataType: 'json',
      type: 'post',
      success: function (data) {
        const feature = FeatureObj
        feature.id = data.featureId
        /*
        const featureList = this.state.featureList
        featureList.push(feature)
        */
        this.setState({currentFeature: FeatureObj, currentKey: data.featureId,})
      }.bind(this),
      error: function () {}.bind(this),
    })
  }

  updateFeature(feature) {
    this.setState({currentFeature: feature})
  }

  closeMessage() {
    this.setState({message: null})
  }

  message() {
    if (this.state.message !== null) {
      const {message} = this.state
      return <Message type={message.type} message={message.text} onClose={this.closeMessage}>{message.text}</Message>
    }
  }

  getListing() {
    if (this.state.loading === true) {
      return <Waiting/>
    } else if (this.state.currentKey !== null) {
      return <FeatureForm
        feature={this.state.currentFeature}
        srcHttp={this.props.srcHttp}
        update={this.updateFeature}/>
    } else {
      return <FeatureList
        list={this.state.featureList}
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
