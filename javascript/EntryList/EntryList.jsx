'use strict'
import React, {Component} from 'react'
import Waiting from '../AddOn/Waiting'
import './style.css'
import EntryRow from './EntryRow'
//import SearchBar from './SearchBar'
import Toolbar from './Toolbar'
import Panel from './Panel'

/* global $ */

export default class EntryList extends Component {
  constructor(props) {
    super(props)
    this.state = {
      listing: [],
      selected: null,
      loading: true,
      currentEntry: null
    }
    this.publish = this.publish.bind(this)
    this.deleteStory = this.deleteStory.bind(this)
  }

  componentDidMount() {
    this.load()
  }

  updateListing(key, entry) {
    let listing = this.state.listing
    listing[key] = entry
    this.setState({listing: listing})
  }

  load() {
    $.getJSON('./stories/Listing').done(function (data) {
      this.setState({listing: data.listing, loading: false})
    }.bind(this))
  }

  publish(key) {
    let entry = this.state.listing[key].id
    $.ajax({
      url: `./stories/Entry/${entry}`,
      data: {
        param: 'published',
        value: 1
      },
      dataType: 'json',
      type: 'patch',
      success: function () {
        entry.published = 1
        this.updateListing(key, entry)
      }.bind(this),
      error: function () {}.bind(this),
    })
  }

  select(key) {
    this.setState({selected: key, currentEntry: this.state.listing[key]})
  }

  deleteStory() {
    console.log(this.state.currentEntry.title);
  }

  render() {
    if (this.state.loading) {
      return <Waiting label="stories"/>
    }

    let listing = this.state.listing.map(function (entry, key) {
      return <EntryRow
        entry={entry}
        key={key}
        select={this.select.bind(this, key)}
        selected={this.state.selected == key}/>
    }.bind(this))
    return (
      <div>
        <Toolbar entry={this.state.currentEntry} publish={this.publish} deleteStory={this.deleteStory}/>
        <div className="toolbar-buffer"></div>
        {listing}
      </div>
    )
  }
}
