'use strict'
import React, {Component} from 'react'
//import PropTypes from 'prop-types'
import Waiting from '../AddOn/Waiting'
import './style.css'
import EntryRow from './EntryRow'
//import SearchBar from './SearchBar'
import ListControls from './Listcontrols'
import PublishOverlay from './PublishOverlay'
import {VelocityTransitionGroup} from 'velocity-react'
import moment from 'moment'
/* global $ */

export default class EntryList extends Component {
  constructor(props) {
    super(props)
    this.state = {
      listing: [],
      selected: null,
      loading: true,
      currentEntry: null,
      search: '',
      sortBy: 'published',
      publishOverlay: false,
      tags: ''
    }
    this.currentKey = null
    this.publish = this.publish.bind(this)
    this.deleteStory = this.deleteStory.bind(this)
    this.updateSort = this.updateSort.bind(this)
    this.publishStory = this.publishStory.bind(this)
    this.closeOverlay = this.closeOverlay.bind(this)
    this.updateTags = this.updateTags.bind(this)
    this.setPublishDate = this.setPublishDate.bind(this)
  }

  componentDidMount() {
    this.load()
  }

  updateTags(e) {
    this.setState({tags: e.target.value})
  }

  setPublishDate(e) {
    let entry = this.state.currentEntry
    const value = e.target.value
    entry.publishDate = moment(value).unix()
    this.setState({currentEntry: entry})
  }

  updateListing(key, entry) {
    let listing = this.state.listing
    listing[key] = entry
    this.setState({listing: listing})
  }

  updateSort(sortBy) {
    this.setState({
      sortBy: sortBy
    }, this.load)
  }

  load() {
    $.getJSON('./stories/Listing', {
      search: this.state.search,
      sortBy: this.state.sortBy,
    }).done(function (data) {
      if (data.listing == null) {
        this.setState({listing: false, loading: false})
      } else {
        this.setState({listing: data.listing, loading: false,})
      }
    }.bind(this))
  }

  searchChange(e) {
    const search = e.target.value
    this.setState({search: search})
  }

  setCurrentEntry(key) {
    this.setState({currentEntry: this.state.listing[key]})
    this.currentKey = key
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
        this.setState({publishOverlay: false})
        let currentEntry = this.state.currentEntry
        currentEntry.published = 1
        this.updateListing(this.currentKey, currentEntry)
      }.bind(this),
      error: function () {}.bind(this)
    })
  }

  publishStory(key) {
    this.setCurrentEntry(key)
    this.setState({publishOverlay: true})
  }

  closeOverlay() {
    this.setState({publishOverlay: false, currentEntry: null,})
    this.currentKey = null
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

  render() {
    if (this.state.loading) {
      return <Waiting label="stories"/>
    } else if (this.state.listing === false) {
      return <NoEntries/>
    }

    let listing = this.state.listing.map(function (entry, key) {
      return <EntryRow
        deleteStory={this.deleteStory.bind(this, key)}
        entry={entry}
        publishStory={this.publishStory.bind(this, key)}
        key={key}
        publish={this.publish.bind(this, key)}/>
    }.bind(this))

    const fadeIn = {
      animation: "fadeIn"
    }

    const fadeOut = {
      animation: "fadeOut"
    }

    return (
      <div>
        <VelocityTransitionGroup enter={fadeIn} leave={fadeOut}>
          {this.state.publishOverlay
            ? <PublishOverlay
                updateTags={this.updateTags}
                tags={this.state.tags}
                close={this.closeOverlay}
                entry={this.state.currentEntry}
                setPublishDate={this.setPublishDate}
                publishStory={this.publish}/>
            : null}
        </VelocityTransitionGroup>
        <ListControls
          sortBy={this.state.sortBy}
          search={this.state.search}
          handleChange={this.searchChange}
          updateSort={this.updateSort}/>
        <div>{listing}</div>
      </div>
    )
  }
}

const NoEntries = () => {
  return <p>No stories found.
    <a href="./stories/Entry/create">Create your first story!</a>
  </p>
}
