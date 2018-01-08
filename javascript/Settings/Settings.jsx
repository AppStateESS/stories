'use strict'
import React, {Component} from 'react'
import PropTypes from 'prop-types'
import BigCheckbox from '../AddOn/BigCheckbox'
import ButtonGroup from '../AddOn/ButtonGroup'
import Navbar from '../AddOn/Navbar'

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
      showAuthor: 0,
      deleted: 0,
      purgeVerified: false
    }
    this.setCommentCode = this.setCommentCode.bind(this)
    this.setShowComments = this.setShowComments.bind(this)
    this.saveCommentCode = this.saveCommentCode.bind(this)
    this.purgeDeleted = this.purgeDeleted.bind(this)
    this.toggleVerified = this.toggleVerified.bind(this)
  }

  componentDidMount() {
    this.setState(this.props.settings)
  }

  toggleVerified() {
    this.setState({
      purgeVerified: !this.state.purgeVerified
    })
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
      value: setting,
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

  purgeDeleted() {
    $.ajax({
      url: 'stories/Settings/purge',
      dataType: 'json',
      type: 'post',
      success: function(){
        window.location.reload(true)
      }.bind(this),
      error: function(){}.bind(this)
    })
  }

  render() {
    const amountButtons = [
      {
        value: 2,
        label: 2,
      }, {
        value: 4,
        label: 4,
      }, {
        value: 6,
        label: 6,
      }, {
        value: 8,
        label: 8,
      }, {
        value: 10,
        label: 10,
      },
    ]

    const formatButton = [
      {
        value: 0,
        label: 'Summary'
      }, {
        value: 1,
        label: 'Full'
      },
    ]

    let purge
    const isAre = this.state.deleted > 1
      ? 'are'
      : 'is'

    const storyCount = `stor${
    this.state.deleted > 1
      ? 'ies'
      : 'y'}`

    if (this.state.deleted > 0) {
      const lock = (
        <button className="btn btn-default" onClick={this.toggleVerified}>
          <i
            className={`fa fa-${this.state.purgeVerified
              ? 'unlock-alt'
              : 'lock'}`}></i>
        </button>
      )
      purge = (
        <div>
          <p>{`There ${isAre} ${this.state.deleted} ${storyCount} waiting to be purged.`}</p>
          <button
            className="btn btn-danger"
            onClick={this.purgeDeleted}
            disabled={!this.state.purgeVerified}>Purge all deleted</button>{lock}
        </div>
      )
    } else {
      purge = <p>No deleted stories require purging.</p>
    }

    return (
      <div>
        <Navbar header={'Stories settings'}/>
        <h2>Stories Settings</h2>
        <div className="row">
          <div className="col-md-6">
            <div className="settings">
              <h3>Front page</h3>
              <div className="mb-1">
                <BigCheckbox
                  handle={this.saveSetting.bind(this, 'listStories')}
                  checked={this.state.listStories}
                  label="List stories on front page"/>
              </div>
              <div className="indent clearfix">
                <div>Stories per page</div>
                <ButtonGroup
                  buttons={amountButtons}
                  handle={this.saveSetting.bind(this, 'listStoryAmount')}
                  match={this.state.listStoryAmount}/>
                <div className="mt-1">Story display type</div>
                <ButtonGroup
                  buttons={formatButton}
                  handle={this.saveSetting.bind(this, 'listStoryFormat')}
                  match={this.state.listStoryFormat}/>
              </div>
            </div>
          </div>
          <div className="col-md-6">
            <div className="settings">
              <h3>Comments</h3>
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
        </div>
        <div className="row">
          <div className="col-md-6">
            <div className="settings">
              <h3>Authors</h3>
              <BigCheckbox
                handle={this.saveSetting.bind(this, 'showAuthor')}
                checked={this.state.showAuthor}
                label="Show author profile on story"/>
            </div>
          </div>
          <div className="col-md-6">
            <div className="settings">
              <h3>Purge</h3>
              {purge}
            </div>
          </div>
        </div>
      </div>
    )
  }
}

Settings.propTypes = {
  settings: PropTypes.object
}
