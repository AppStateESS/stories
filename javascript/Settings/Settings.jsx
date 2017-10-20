'use strict'
import React, {Component} from 'react'
import PropTypes from 'prop-types'
import BigCheckbox from '../AddOn/BigCheckbox'
import ButtonGroup from '../AddOn/ButtonGroup'

/* global $ */

export default class Settings extends Component {
  constructor(props) {
    super(props)
    this.state = {
      listStories: 0,
      listStoryAmount: 3,
      showFeatures: 0,
      featureNumber: 6,
      listStoryFormat: 0,
      commentCode: '',
      showComments: 0,
    }
    this.setListStories = this.setListStories.bind(this)
    this.setListStoryAmount = this.setListStoryAmount.bind(this)
    this.setShowFeatures = this.setShowFeatures.bind(this)
    this.setShowFeatureNumber = this.setShowFeatureNumber.bind(this)
    this.setListStoryFormat = this.setListStoryFormat.bind(this)
    this.setCommentCode = this.setCommentCode.bind(this)
    this.setShowComments = this.setShowComments.bind(this)
    this.saveCommentCode = this.saveCommentCode.bind(this)
  }

  componentDidMount() {
    this.setState(this.props.settings)
  }

  saveSetting(param, value) {
    let setting
    if (typeof(value) === 'boolean') {
      setting = value
        ? 1
        : 0
    } else {
      setting = value

    }
    $.post('./stories/Settings', {
      param: param,
      value: setting
    }, null, 'json').done(function () {
      const stateSetting = {}
      stateSetting[param] = setting
      this.setState(stateSetting)
    }.bind(this))
  }

  saveCommentCode() {
    this.saveSetting('commentCode', this.state.commentCode)
  }

  setCommentCode(e) {
    this.setState({commentCode: e.target.value})
  }

  setShowComments(value) {
    this.saveSetting('showComments', value)
  }

  setListStories(value) {
    this.saveSetting('listStories', value)
  }

  setListStoryAmount(value) {
    this.saveSetting('listStoryAmount', value)
  }

  setShowFeatures(value) {
    this.saveSetting('showFeatures', value)
  }

  setShowFeatureNumber(value) {
    this.saveSetting('featureNumber', value)
    // this.setState({featureNumber: value})
  }

  setListStoryFormat(value) {
    this.saveSetting('listStoryFormat', value)
  }

  render() {
    const amountButtons = [
      {
        value: 3,
        label: 3
      }, {
        value: 6,
        label: 6
      }, {
        value: 9,
        label: 9
      }, {
        value: 12,
        label: 12
      },
    ]

    const formatButton = [
      {
        value: 0,
        label: 'Summary',
      }, {
        value: 1,
        label: 'Full',
      },
    ]

    return (
      <div>
        <h2>Stories Settings</h2>
        <div className="settings">
          <div className="mb-1">
            <BigCheckbox
              handle={this.setListStories}
              checked={this.state.listStories}
              label="List stories on front page"/>
          </div>
          <div className="indent clearfix">
            <div className="pull-left mr-1">
              <ButtonGroup
                buttons={amountButtons}
                handle={this.setListStoryAmount}
                match={this.state.listStoryAmount}/>
            </div>
            <div>Number of stories per page</div>
          </div>
          <div className="indent clearfix mt-1">
            <div className="pull-left mr-1">
              <ButtonGroup
                buttons={formatButton}
                handle={this.setListStoryFormat}
                match={this.state.listStoryFormat}/>
            </div>
            <div>Story display type:</div>
          </div>
        </div>
        <div className="settings">
          <div>
            <BigCheckbox
              handle={this.setShowFeatures}
              checked={this.state.showFeatures}
              label="Show features on front page"/>
          </div>
          <div className="indent">
            <div className="pull-left mr-1">
              <ButtonGroup
                buttons={amountButtons}
                handle={this.setShowFeatureNumber}
                match={this.state.featureNumber}/>
            </div>
            <div>Number of features displayed</div>
          </div>
        </div>
        <div className="settings">
          <BigCheckbox
            handle={this.setShowComments}
            checked={this.state.showComments}
            label="Show comments on story"/>
          <div className="indent">
            <span>Comment embed code</span>
            <textarea
              className="form-control"
              value={this.state.commentCode}
              onChange={this.setCommentCode}
              placeholder="e.g. Muut, StaticMan, Disqus, Isso"/>
              <button className="btn btn-primary" onClick={this.saveCommentCode}><i className="fa fa-save"></i>&nbsp;Save comment code</button>
          </div>
        </div>
      </div>
    )
  }
}

Settings.propTypes = {
  settings: PropTypes.object
}
