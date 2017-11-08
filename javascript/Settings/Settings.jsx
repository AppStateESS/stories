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
      listStoryFormat: 0,
      commentCode: '',
      showComments: 0,
    }
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

  render() {
    const amountButtons = [
      {
        value: 2,
        label: 2
      }, {
        value: 4,
        label: 4
      }, {
        value: 6,
        label: 6
      }, {
        value: 8,
        label: 8
      }, {
        value: 10,
        label: 10
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
              handle={this.saveSetting.bind(this, 'listStories')}
              checked={this.state.listStories}
              label="List stories on front page"/>
          </div>
          <div className="indent clearfix">
            <div className="row">
              <div className="row">
                <div className="col-sm-6">
                  <div>Number of stories per page</div>
                  <ButtonGroup
                    buttons={amountButtons}
                    handle={this.saveSetting.bind(this, 'listStoryAmount')}
                    match={this.state.listStoryAmount}/>
                </div>
                <div className="col-sm-6">
                  <div>Story display type</div>
                  <ButtonGroup
                    buttons={formatButton}
                    handle={this.saveSetting.bind(this, 'listStoryFormat')}
                    match={this.state.listStoryFormat}/>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div className="settings">
          <BigCheckbox
            handle={this.saveSetting.bind(this, 'showComments')}
            checked={this.state.showComments}
            label="Show comments on story"/>
          <div className="indent">
            <span>Comment embed code</span>
            <textarea
              className="form-control"
              value={this.state.commentCode}
              onChange={this.setCommentCode}
              placeholder="e.g. Muut, StaticMan, Disqus, Isso"/>
            <button className="btn btn-primary" onClick={this.saveCommentCode}>
              <i className="fa fa-save"></i>&nbsp;Save comment code</button>
          </div>
        </div>
      </div>
    )
  }
}

Settings.propTypes = {
  settings: PropTypes.object
}
