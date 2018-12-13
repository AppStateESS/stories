'use strict'
import React, {Component} from 'react'
import PropTypes from 'prop-types'
import BigCheckbox from '@essappstate/canopy-react-bigcheckbox'
import ButtonGroup from '@essappstate/canopy-react-buttongroup'
import Navbar from '../AddOn/Navbar'

/* global $ */

export default class Settings extends Component {
  constructor(props) {
    super(props)
    this.state = {
      advanceListType: 1,
      commentCode: '',
      deleted: 0,
      hideDefault: 0,
      listStories: 0,
      listStoryAmount: 3,
      listStoryFormat: 0,
      purgeVerified: false,
      showComments: 0,
      featureCutOff: '0',
      showAuthor: 0,
      tagList: []
    }
    this.setCommentCode = this.setCommentCode.bind(this)
    this.setShowComments = this.setShowComments.bind(this)
    this.saveCommentCode = this.saveCommentCode.bind(this)
    this.purgeDeleted = this.purgeDeleted.bind(this)
    this.toggleVerified = this.toggleVerified.bind(this)
    this.updateCutOff = this.updateCutOff.bind(this)
    this.deleteTag = this.deleteTag.bind(this)
  }

  componentDidMount() {
    this.setState(this.props.settings)
    this.loadTags()
  }

  deleteTag(tag) {
    const ask = `Deleting this tag will remove ${tag.count} associated stor${tag.count === '1' ? 'y' : 'ies'}.\rAre you sure you want to do this?`
    if (confirm(ask)) {
      $.ajax({
        url: './stories/Tag/' + tag.id,
        dataType: 'json',
        type: 'delete',
        success: () => {
          this.loadTags()
        },
        error: () => {
          alert('There was an error when trying to delete tag ' + tag.id)
        }
      })
    }
  }

  loadTags() {
    $.ajax({
      url: 'stories/Settings/tagList',
      dataType: 'json',
      type: 'get',
      success: (data) => {
        this.setState({tagList: data.tagList})
      },
      error: () => {}
    })
  }

  purgeDeleted() {
    $.ajax({
      url: 'stories/Settings/purge',
      dataType: 'json',
      type: 'post',
      success: function () {
        window.location.reload(true)
      }.bind(this),
      error: function () {}.bind(this)
    })
  }

  saveSetting(param, value) {
    let setting
    if (param === 'listStoryAmount') {
      if (value > 20) {
        value = 20
      } else if (value == 0) {
        value = 2
      }
    }
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

  toggleVerified() {
    this.setState({
      purgeVerified: !this.state.purgeVerified
    })
  }

  updateCutOff(e) {
    this.saveSetting('featureCutOff', e.target.value)
  }

  checkBox(value, match) {
    const plural = value !== '1'
      ? 's'
      : ''
    const checked = match === value
    return (
      <label><input
        type="radio"
        name="featureCutOff"
        checked={checked}
        value={value}
        onChange={this.updateCutOff}/>
        &nbsp;{value}&nbsp; month{plural}</label>
    )
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
      }
    ]

    const formatButton = [
      {
        value: 0,
        label: 'Summary'
      }, {
        value: 1,
        label: 'Full'
      }
    ]

    let purge
    const isAre = this.state.deleted > 1
      ? 'are'
      : 'is'

    const storyCount = `stor${this.state.deleted > 1
      ? 'ies'
      : 'y'}`

    if (this.state.deleted > 0) {
      const lock = (
        <button className="btn btn-outline-dark" onClick={this.toggleVerified}>
          <i
            className={`fas fa-${this.state.purgeVerified
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

    let tags = <p>No tags found.</p>
    if (this.state.tagList.length > 0) {
      const rows = this.state.tagList.map((value, key) => {
        return (
          <tr key={key}>
            <td className="admin">
              <button
                className="btn btn-outline-danger"
                onClick={this.deleteTag.bind(this, value)}>
                <i className="fas fa-trash-alt text-danger"></i>
              </button>
            </td>
            <td className="tag-name">{value.title}</td>
            <td className="tag-count">{value.count}</td>
          </tr>
        )
      })
      tags = <table className="table">
        <thead>
          <tr>
            <th></th>
            <th>Title</th>
            <th>Tagged</th>
          </tr>
        </thead>
        <tbody>{rows}</tbody>
      </table>
    }

    return (
      <div>
        <Navbar header={'Stories settings'}/>
        <h2>Stories Settings</h2>
        <div className="row">
          <div className="col-md-6">
            <div className="settings">
              <h3>Front page</h3>
              <p>Always available on&nbsp;
                <a href="./stories/Listing">Listing page</a>.</p>
              <div className="mb-1">
                <BigCheckbox
                  handle={this.saveSetting.bind(this, 'listStories')}
                  checked={this.state.listStories}
                  label="List stories on front page"/>
              </div>
            </div>
          </div>
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
              <h3>Listing</h3>
              <h4 className="mt-2">Default display type</h4>
              <ButtonGroup
                buttons={formatButton}
                activeColor="success"
                handle={this.saveSetting.bind(this, 'listStoryFormat')}
                match={this.state.listStoryFormat}/>
              <h4 className="mt-3">Stories per page</h4>
              <div className="d-flex align-items-center">
                <ButtonGroup
                  buttons={amountButtons}
                  handle={this.saveSetting.bind(this, 'listStoryAmount')}
                  activeColor="success"
                  match={this.state.listStoryAmount}/>
                <input
                  className="form-control raw-input ml-3"
                  maxsize="2"
                  size="2"
                  name="listStoryAmount"
                  onChange={e => {
                    this.saveSetting('listStoryAmount', e.target.value)
                  }}
                  value={this.state.listStoryAmount}
                  type="text"/>
              </div>
              <p className="text-muted">Story limit: 1 - 20</p>
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
          <div className="col-md-6">
            <div className="settings">
              <h3>View</h3>
              <BigCheckbox
                handle={this.saveSetting.bind(this, 'hideDefault')}
                checked={this.state.hideDefault}
                label="Hide side bar when viewing stories"/>
            </div>
          </div>
          <div className="col-md-6">
            <div className="settings">
              <h3>Feature age cutoff</h3>
              <p>Maximum age a story may be featured.</p>

              <div className="d-flex justify-content-around">
                <ul className="list-unstyled">
                  <li>
                    {this.checkBox('1', this.state.featureCutOff)}
                  </li>
                  <li>
                    {this.checkBox('2', this.state.featureCutOff)}
                  </li>
                  <li>
                    {this.checkBox('3', this.state.featureCutOff)}
                  </li>
                </ul>
                <ul className="list-unstyled">
                  <li>
                    {this.checkBox('4', this.state.featureCutOff)}
                  </li>
                  <li>
                    {this.checkBox('5', this.state.featureCutOff)}
                  </li>
                  <li>
                    {this.checkBox('6', this.state.featureCutOff)}
                  </li>
                </ul>
              </div>
            </div>
          </div>
          <div className="col-md-6">
            <div className="settings">
              <h3>Purge</h3>
              {purge}
            </div>
          </div>
          <div className="col-md-6">
            <div className="settings">
              <h3>Tags</h3>
              <div className="tags">
                {tags}
              </div>
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
