'use strict'
import React, {Component} from 'react'
import PropTypes from 'prop-types'
import Waiting from '../AddOn/Waiting'
import './style.css'
import EntryRow from './EntryRow'
//import SearchBar from './SearchBar'
import ListControls from './ListControls'
import PublishOverlay from '../AddOn/PublishOverlay'
import TagOverlay from '../AddOn/TagOverlay'
import ThumbnailOverlay from './ThumbnailOverlay'
import {VelocityTransitionGroup} from 'velocity-react'
import moment from 'moment'
/* global $ */

export default class EntryList extends Component {
  constructor(props) {
    super(props)
    this.state = {
      listing: [],
      selected: null,
      search: '',
      loading: true,
      currentEntry: null,
      sortBy: 'published',
      publishOverlay: false,
      tagOverlay: false,
      thumbnailOverlay: false,
      sortByTagId: 0,
      tags: [],
    }

    this.offset = 0
    this.delay
    this.currentKey = null
    this.publish = this.publish.bind(this)
    this.saveTags = this.saveTags.bind(this)
    this.showTags = this.showTags.bind(this)
    this.showMore = this.showMore.bind(this)
    this.tagChange = this.tagChange.bind(this)
    this.sortByTag = this.sortByTag.bind(this)
    this.updateSort = this.updateSort.bind(this)
    this.clearSearch = this.clearSearch.bind(this)
    this.deleteStory = this.deleteStory.bind(this)
    this.publishStory = this.publishStory.bind(this)
    this.closeOverlay = this.closeOverlay.bind(this)
    this.searchChange = this.searchChange.bind(this)
    this.setPublishDate = this.setPublishDate.bind(this)
    this.savePublishDate = this.savePublishDate.bind(this)
    this.newOptionClick = this.newOptionClick.bind(this)
    this.thumbnailForm = this.thumbnailForm.bind(this)
    this.updateEntry = this.updateEntry.bind(this)
    this.updateImage = this.updateImage.bind(this)
  }

  updateImage(image) {
    const entry = this.state.currentEntry
    entry.thumbnail = image
    this.updateEntry(entry)
  }

  componentDidMount() {
    this.load()
  }

  sortByTag(sortByTagId) {
    this.setState({
      sortByTagId: sortByTagId
    }, this.load)
  }

  setPublishDate(e) {
    let entry = this.state.currentEntry
    const value = e.target.value
    entry.publishDate = moment(value).unix()
    this.setState({currentEntry: entry})
  }

  updateSort(sortBy) {
    this.setState({
      sortBy: sortBy
    }, this.load)
  }

  updateEntry(entry) {
    let currentEntry = this.state.currentEntry
    currentEntry = entry

    let listing = this.state.listing
    listing[this.currentKey] = currentEntry
    this.setState({currentEntry, listing})
  }

  tagChange(value) {
    const entry = this.state.currentEntry
    entry.tags = value
    this.setState({currentEntry: entry})
  }

  showMore() {}

  load() {
    $.getJSON('./stories/Listing', {
      search: this.state.search,
      sortBy: this.state.sortBy,
      sortByTagId: this.state.sortByTagId,
    }).done(function (data) {
      if (data.listing == null) {
        this.setState({listing: false, loading: false, tags: data.tags,})
      } else {
        this.setState({listing: data.listing, loading: false, tags: data.tags,})
      }
    }.bind(this))
  }

  searchChange(e) {
    clearTimeout(this.delay)
    const search = e.target.value
    this.setState({search: search})
    if (search.length < 3 && search.length > 0) {
      return
    }
    this.delay = setTimeout(function () {
      this.load()
    }.bind(this, search), 500)
  }

  newOptionClick(newTag) {
    delete newTag.className
    let {tags, currentEntry} = this.state
    $.ajax({
      url: './stories/Tag',
      data: {
        title: newTag.label
      },
      dataType: 'json',
      type: 'post',
      success: function (data) {
        newTag.value = data
        tags.push(newTag)
        currentEntry.tags.push(newTag)
        this.setState({tags, currentEntry})
      }.bind(this),
      error: function () {}.bind(this)
    })
  }

  clearSearch() {
    this.setState({
      search: ''
    }, this.load)
  }

  setCurrentEntry(key) {
    const currentEntry = this.state.listing[key]
    this.currentKey = key
    this.setState({currentEntry: currentEntry})
  }

  publish() {
    $.ajax({
      url: `./stories/Entry/${this.state.currentEntry.id}`,
      data: {
        values: [
          {
            param: 'published',
            value: 1
          }, {
            param: 'publishDate',
            value: this.state.currentEntry.publishDate
          },
        ]
      },
      dataType: 'json',
      type: 'patch',
      success: function () {
        let currentEntry = this.state.currentEntry
        currentEntry.published = 1
        this.updateEntry(currentEntry)
        this.closeOverlay()
      }.bind(this),
      error: function () {}.bind(this)
    })
  }

  publishStory(key) {
    this.setCurrentEntry(key)
    this.setState({publishOverlay: true})
  }

  showTags(key) {
    this.setState({
      tagOverlay: true
    }, this.setCurrentEntry(key))
  }

  closeOverlay() {
    this.setState(
      {publishOverlay: false, tagOverlay: false, thumbnailOverlay: false, currentEntry: null}
    )
    this.unlockBody()
    this.currentKey = null
  }

  unlockBody() {
    $('body').css('overflow', 'inherit')
  }

  thumbnailForm(key) {
    this.setCurrentEntry(key)
    this.setState({
      thumbnailOverlay: true
    })
  }

  deleteStory(key) {
    if (confirm('Are you sure you want to delete this story?')) {
      let listing = this.state.listing
      const entry = listing[key]
      $.ajax({
        url: './stories/Entry/' + entry.id,
        dataType: 'json',
        type: 'delete',
        success: function () {
          listing.splice(key, 1)
          this.setState({listing: listing})
        }.bind(this),
        error: function () {}.bind(this)
      })
    }
  }

  savePublishDate() {
    $.ajax({
      url: `./stories/Entry/${this.state.currentEntry.id}`,
      data: {
        param: 'publishDate',
        value: this.state.currentEntry.publishDate
      },
      dataType: 'json',
      type: 'patch',
      success: function () {
        this.closeOverlay()
      }.bind(this),
      error: function () {}.bind(this),
    })
  }

  saveTags() {
    $.ajax({
      url: './stories/Tag/attach',
      data: {
        entryId: this.state.currentEntry.id,
        tags: this.state.currentEntry.tags
      },
      dataType: 'json',
      type: 'post',
      success: function () {
        this.closeOverlay()
      }.bind(this),
      error: function () {}.bind(this),
    })

  }

  render() {
    let listing

    if (this.state.loading) {
      return <Waiting label="stories"/>
    } else if (this.state.listing === false) {
      listing = <NoEntries/>
    } else {
      listing = this.state.listing.map(function (entry, key) {
        return <EntryRow
          deleteStory={this.deleteStory.bind(this, key)}
          entry={entry}
          sortByTag={this.sortByTag}
          showTags={this.showTags.bind(this, key)}
          publishStory={this.publishStory.bind(this, key)}
          thumbnailForm={this.thumbnailForm.bind(this, key)}
          key={key}
          publish={this.publish.bind(this, key)}/>
      }.bind(this))
    }

    const fadeIn = {
      animation: "fadeIn"
    }

    const fadeOut = {
      animation: "fadeOut"
    }

    let showMore
    if (this.state.listing.length > this.props.segmentSize) {
      showMore = (
        <button className="btn btn-primary" onClick={this.showMore}>Show more rows</button>
      )
    }

    return (
      <div>
        <VelocityTransitionGroup enter={fadeIn} leave={fadeOut}>
          {
            this.state.publishOverlay
              ? <PublishOverlay
                  savePublishDate={this.savePublishDate}
                  title={this.state.currentEntry.title}
                  isPublished={this.state.currentEntry.published}
                  publishDate={this.state.currentEntry.publishDate}
                  setPublishDate={this.setPublishDate}
                  publishStory={this.publish}/>
              : null
          }
        </VelocityTransitionGroup>
        <VelocityTransitionGroup enter={fadeIn} leave={fadeOut}>
          {
            this.state.tagOverlay
              ? <TagOverlay
                  tagChange={this.tagChange}
                  entryTags={this.state.currentEntry.tags}
                  tags={this.state.tags}
                  saveTags={this.saveTags}
                  title={this.state.currentEntry.title}
                  newOptionClick={this.newOptionClick}/>
              : null
          }
        </VelocityTransitionGroup>
        <ThumbnailOverlay
          thumbnailOverlay={this.state.thumbnailOverlay}
          updateEntry={this.updateEntry}
          updateImage={this.updateImage}
          entry={this.state.currentEntry}
          close={this.closeOverlay}/>
        <ListControls
          sortBy={this.state.sortBy}
          search={this.state.search}
          clearSearch={this.clearSearch}
          handleChange={this.searchChange}
          updateSort={this.updateSort}/>
        <div>{listing}</div>
        <div>{showMore}</div>
      </div>
    )
  }
}

EntryList.propTypes = {
  segmentSize: PropTypes.number
}

const NoEntries = () => {
  return <p>No stories found.&nbsp;
    <a href="./stories/Entry/create">Create your first story!</a>
  </p>
}
