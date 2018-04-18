'use strict'
import React, {Component} from 'react'
import moment from 'moment'
import PublishOverlay from '../AddOn/PublishOverlay'
import {VelocityTransitionGroup} from 'velocity-react'
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
    }
    this.publishStory = this.publishStory.bind(this)
    this.setPublishDate = this.setPublishDate.bind(this)
    this.savePublishDate = this.savePublishDate.bind(this)
  }

  setPublishDate(e) {
    const value = e.target.value
    const publishDate = moment(value).unix()
    this.setState({publishDate: publishDate})
  }

  publishStory(value) {
    $.ajax({
      url: `./stories/Entry/${this.state.id}`,
      data: {
        param: 'published',
        value: value,
      },
      dataType: 'json',
      type: 'patch',
      success: function () {
        this.setState({published: value, publishOverlay: false})
      }.bind(this),
      error: function () {}.bind(this)
    })
  }

  setOverlay(set) {
    this.setState({'publishOverlay': set})
  }

  savePublishDate() {
    $.ajax({
      url: `./stories/Entry/${this.state.id}`,
      data: {
        param: 'publishDate',
        value: this.state.publishDate,
      },
      dataType: 'json',
      type: 'patch',
      success: function () {
        this.setOverlay(false)
      }.bind(this),
      error: function () {}.bind(this)
    })
  }

  render() {
    const now = moment().format('X')
    const fadeIn = {
      animation: "fadeIn"
    }

    const fadeOut = {
      animation: "fadeOut"
    }

    let publishOverlay
    if (this.state.publishOverlay) {
      publishOverlay = <PublishOverlay
        title={this.state.title}
        savePublishDate={this.savePublishDate}
        isPublished={this.state.published}
        publishDate={this.state.publishDate}
        setPublishDate={this.setPublishDate}
        publish={this.publishStory.bind(this, 1)}
        unpublish={this.publishStory.bind(this, 0)}/>
    }

    let publishLink
    if (this.state.published === '0') {
      publishLink = 'Unpublished'
    } else if (this.state.publishDate < now) {
      publishLink = 'Published'
    } else {
      const relative = moment(this.state.publishDate*1000).format('LLL')
      publishLink = `Publish after ${relative}`
    }

    return (
      <div>
        <VelocityTransitionGroup enter={fadeIn} leave={fadeOut}>
          {publishOverlay}
        </VelocityTransitionGroup>
        <a className="btn btn-outline-secondary btn-sm" onClick={this.setOverlay.bind(this, true)}>{publishLink}</a>
      </div>
    )
  }
}

Publish.propTypes = {
  id: PropTypes.string,
  publishDate: PropTypes.string,
  title: PropTypes.string,
  published: PropTypes.string,
}
