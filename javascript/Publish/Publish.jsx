'use strict'
import React, {Component} from 'react'
import moment from 'moment'
import PublishOverlay from '../AddOn/PublishOverlay'
import PropTypes from 'prop-types'

/* global $ */

export default class Publish extends Component {
  constructor(props) {
    super(props)
    this.state = {
      id: props.id,
      title: props.title,
      publishOverlay: false,
      published: props.published,
      publishDate: props.publishDate,
      shareStatus: null,
      hostId: '0'
    }
    this.publishStory = this.publishStory.bind(this)
    this.setPublishDate = this.setPublishDate.bind(this)
    this.savePublishDate = this.savePublishDate.bind(this)
    this.shareStory = this.shareStory.bind(this)
    this.changeHost = this.changeHost.bind(this)
    this.closeOverlay = this.closeOverlay.bind(this)
  }

  setPublishDate(e) {
    const value = e.target.value
    const publishDate = moment(value).unix()
    this.setState({publishDate: publishDate})
  }

  shareStory() {
    if (this.state.hostId === '0') {
      return
    }

    const icon = <span>
      <i className="fas fa-sync fa-spin"></i>
    </span>

    const saving = (<div>
      {icon}&nbsp;Sending...
    </div>)
    this.setState({shareStatus: saving})
    $.ajax({
      url: `stories/Host/${this.state.hostId}/share`,
      data: {
        entryId: this.state.id
      },
      dataType: 'json',
      type: 'put',
      success: (data) => {
        if (data.error) {
          const errorMessage = (<div className="alert alert-danger">{data.error}</div>)
          this.setState({shareStatus: errorMessage})
        } else if (data.success) {
          this.setState({
            shareStatus: <div className="alert alert-success">Request received.</div>
          })
        } else {
          this.setState({
            shareStatus: <div className="alert alert-danger">Request failed.</div>
          })
        }
      },
      error: () => {}
    })
  }

  publishStory(value) {
    $.ajax({
      url: `./stories/Entry/${this.state.id}`,
      data: {
        param: 'published',
        value: value
      },
      dataType: 'json',
      type: 'patch',
      success: function () {
        this.setState({published: value.toString(), publishOverlay: false})
      }.bind(this),
      error: function () {}.bind(this)
    })
  }
  
  closeOverlay() {
    this.setState(
      {publishOverlay: false, hostId: '0', shareStatus: null}
    )
  }

  setOverlay(set) {
    this.setState({'publishOverlay': set})
  }

  savePublishDate() {
    $.ajax({
      url: `./stories/Entry/${this.state.id}`,
      data: {
        param: 'publishDate',
        value: this.state.publishDate
      },
      dataType: 'json',
      type: 'patch',
      success: function () {
        this.closeOverlay()
      }.bind(this),
      error: function () {}.bind(this)
    })
  }

  changeHost(e) {
    this.setState({hostId: e.target.value})
  }

  render() {
    const now = moment().format('X')

    const publishOverlay = (
      <PublishOverlay
        show={this.state.publishOverlay}
        title={this.state.title}
        savePublishDate={this.savePublishDate}
        isPublished={this.state.published}
        publishDate={this.state.publishDate}
        setPublishDate={this.setPublishDate}
        changeHost={this.changeHost}
        hostId={this.state.hostId}
        shareList={this.props.shareList}
        shareStory={this.shareStory}
        shareStatus={this.state.shareStatus}
        publish={this.publishStory.bind(this, 1)}
        unpublish={this.publishStory.bind(this, 0)}/>
    )

    let publishLink
    if (this.state.published === '0') {
      publishLink = 'Unpublished'
    } else if (this.state.publishDate < now) {
      publishLink = 'Published'
    } else {
      const relative = moment(this.state.publishDate * 1000).format('LLL')
      publishLink = `Publish after ${relative}`
    }

    return (
      <div>
        {publishOverlay}
        <button
          role="button"
          className={`btn ${ (this.state.published) == '1'
            ? 'btn-success'
            : 'btn-outline-dark'} btn-sm`}
          onClick={this.setOverlay.bind(this, true)}>{publishLink}</button>
      </div>
    )
  }
}

Publish.propTypes = {
  id: PropTypes.string,
  publishDate: PropTypes.string,
  title: PropTypes.string,
  published: PropTypes.string,
  shareList: PropTypes.array
}
