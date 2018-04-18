'use strict'
import React, {Component} from 'react'
import PropTypes from 'prop-types'
import TagOverlay from '../AddOn/TagOverlay'

/* global $ */

export default class TagBar extends Component {
  constructor(props) {
    super(props)
    this.state = {
      title: '',
      entryId: 0,
      tagOverlay: false,
      entryTags: [],
      tags: [],
    }
    this.tagChange = this.tagChange.bind(this)
    this.saveTags = this.saveTags.bind(this)
    this.newOptionClick = this.newOptionClick.bind(this)
    this.forceToLower = this.forceToLower.bind(this)
  }

  componentDidMount() {
    this.setState(this.props)
  }

  setOverlay(val) {
    this.setState({tagOverlay: val})
  }

  unlockBody() {
    $('body').css('overflow', 'inherit')
  }

  saveTags() {
    $.ajax({
      url: './stories/Tag/attach',
      data: {
        entryId: this.state.entryId,
        tags: this.state.entryTags,
      },
      dataType: 'json',
      type: 'post',
      success: function () {
        this.setOverlay(false)
        this.unlockBody()
      }.bind(this),
      error: function () {}.bind(this)
    })
  }

  forceToLower(tag) {
    tag.value = tag.value.toLowerCase()
    tag.label = tag.label.toLowerCase()
  }

  newOptionClick(newTag) {
    delete newTag.className
    let {tags, entryTags,} = this.state
    this.forceToLower(newTag)
    // prevent repeats
    for (var key in tags) {
      const tagVal = tags[key].label
      if (tagVal == newTag.value || tagVal == newTag.label) {
        return
      }
    }
    $.ajax({
      url: './stories/Tag',
      data: {
        title: newTag.label
      },
      dataType: 'json',
      type: 'post',
      success: function (data) {
        newTag.value = data
        if (tags == null) {
          tags = []
        }
        tags.push(newTag)
        entryTags.push(newTag)
        this.setState({tags, entryTags,})
      }.bind(this),
      error: function () {}.bind(this),
    })
  }

  tagChange(value) {
    this.setState({entryTags: value})
  }

  render() {
    let tagListing = <span>No tags</span>
    if (this.state.entryTags[0] != undefined) {
      tagListing = this.state.entryTags.map(function (value, key) {
        return (
          <span className="btn btn-outline-secondary btn-sm mr-1" disabled key={key}>{value.label}</span>
        )
      }.bind(this))
    }
    return (
      <div>
        {this.state.tagOverlay
          ? <TagOverlay
              saveTags={this.saveTags}
              title={this.state.title}
              tagChange={this.tagChange}
              entryTags={this.state.entryTags}
              tags={this.state.tags}
              newOptionClick={this.newOptionClick}/>
          : null}
        <button
          className="btn btn-primary btn-sm mr-1"
          onClick={this.setOverlay.bind(this, true)}>
          <i className="fa fa-tags"></i>&nbsp;Tags</button>
        {tagListing}
      </div>
    )
  }
}

TagBar.propTypes = {
  entryTags: PropTypes.array,
  tags: PropTypes.array,
  title: PropTypes.string,
  entryId: PropTypes.string
}
