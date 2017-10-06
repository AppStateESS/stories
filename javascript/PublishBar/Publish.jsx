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
      entryId: props.entryId,
      title: props.title,
      overlay: false,
      published: props.published,
      publishDate: props.publishDate,
      tags: props.tags
    }
    this.allTags = []
    this.publishStory = this.publishStory.bind(this)
    this.setPublishDate = this.setPublishDate.bind(this)
    this.save = this.save.bind(this)
    this.updateTags = this.updateTags.bind(this)
  }

  componentDidMount() {
    this.loadAllTags()
  }

  loadAllTags() {
    $.getJSON('./stories/Tag/').done(function (data) {
      this.setState({allTags: data})
    }.bind(this))
  }

  updateTags(e) {
    const value = e.target.value
    this.setState({tags: value})
  }

  setPublishDate(e) {
    const value = e.target.value
    const publishDate = moment(value).unix()
    this.setState({publishDate: publishDate})
  }

  publishStory() {
    console.log('publish story and save values')
  }

  setOverlay(set) {
    this.setState({'overlay': set})
  }

  save() {
    $.ajax({
      url: './stories/Tag',
      data: {
        entryId: this.state.entryId,
        tags: this.state.tags
      },
      dataType: 'json',
      type: 'post',
      success: function () {}.bind(this),
      error: function () {}.bind(this)
    })
    $.ajax({
      url: `./stories/Entry/${this.state.entryId}`,
      data: {
        param: 'publishDate',
        value: this.state.publishDate,
      },
      dataType: 'json',
      type: 'patch',
      success: function () {}.bind(this),
      error: function () {}.bind(this)
    })
  }

  render() {
    const now = moment.unix()
    const fadeIn = {
      animation: "fadeIn"
    }

    const fadeOut = {
      animation: "fadeOut"
    }

    let publishOverlay
    if (this.state.overlay) {
      publishOverlay = <PublishOverlay
        title={this.state.title}
        save={this.save}
        published={this.state.published}
        publishDate={this.state.publishDate}
        close={this.setOverlay.bind(this, false)}
        setPublishDate={this.setPublishDate}
        updateTags={this.updateTags}
        tags={this.state.tags}
        allTags={this.allTags}
        publishStory={this.publishStory}/>
    }

    let publishLink

    if (this.state.published === '0') {
      publishLink = 'Unpublished'
    } else if (this.state.publishDate < now) {
      publishLink = 'Published'
    } else {
      publishLink = `Publish on ${this.state.publishDate}`
    }

    return (
      <div>
        <VelocityTransitionGroup enter={fadeIn} leave={fadeOut}>
          {publishOverlay}
        </VelocityTransitionGroup>
        <a className="pointer" onClick={this.setOverlay.bind(this, true)}>{publishLink}</a>
      </div>
    )
  }
}

Publish.propTypes = {
  entryId: PropTypes.string,
  publishDate: PropTypes.string,
  title: PropTypes.string,
  published: PropTypes.string,
  tags: PropTypes.string
}
