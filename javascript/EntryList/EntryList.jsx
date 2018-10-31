'use strict'
import React, {Component} from 'react'
import Waiting from '../AddOn/Waiting'
import EntryRow from './EntryRow'
import PublishOverlay from '../AddOn/PublishOverlay'
import TagOverlay from '../AddOn/TagOverlay'
import ThumbnailOverlay from './ThumbnailOverlay'
import moment from 'moment'
import Navbar from '../AddOn/Navbar'
import SearchBar from '../AddOn/SearchBar'
import SortBy from './SortBy'
import Entry from '../Resource/Entry'
import PropTypes from 'prop-types'
import './style.css'

/* global $ */

export default class EntryList extends Component {
  constructor(props) {
    super(props)
    this.state = {
      listing: [],
      selected: null,
      search: '',
      loading: true,
      currentKey: null,
      sortBy: 'published',
      publishOverlay: false,
      tagOverlay: false,
      thumbnailOverlay: false,
      sortByTagId: 0,
      moreRows: true,
      shareStatus: null,
      tags: [],
      hostId: '0'
    }

    this.offset = 0
    this.delay
    this.lastSearch

    this.publish = this.publish.bind(this)
    this.showMore = this.showMore.bind(this)
    this.saveTags = this.saveTags.bind(this)
    this.showTags = this.showTags.bind(this)
    this.unpublish = this.unpublish.bind(this)
    this.tagChange = this.tagChange.bind(this)
    this.sortByTag = this.sortByTag.bind(this)
    this.changeHost = this.changeHost.bind(this)
    this.shareStory = this.shareStory.bind(this)
    this.updateSort = this.updateSort.bind(this)
    this.updateEntry = this.updateEntry.bind(this)
    this.clearSearch = this.clearSearch.bind(this)
    this.deleteStory = this.deleteStory.bind(this)
    this.updateImage = this.updateImage.bind(this)
    this.currentEntry = this.currentEntry.bind(this)
    this.publishStory = this.publishStory.bind(this)
    this.closeOverlay = this.closeOverlay.bind(this)
    this.searchChange = this.searchChange.bind(this)
    this.thumbnailForm = this.thumbnailForm.bind(this)
    this.newOptionClick = this.newOptionClick.bind(this)
    this.setPublishDate = this.setPublishDate.bind(this)
    this.savePublishDate = this.savePublishDate.bind(this)
    this.showPublishOverlay = this.showPublishOverlay.bind(this)
  }

  currentEntry() {
    if (this.state.currentKey === null) {
      const entry = new Entry
      return entry
    }
    return this.state.listing[this.state.currentKey]
  }

  showMore() {
    this.offset = this.offset + 1
    this.load()
  }

  unpublish() {
    const entry = this.currentEntry()
    entry.published = 0
    this.updateEntry(entry)
    this.savePublish()
  }

  publish() {
    const entry = this.currentEntry()
    entry.published = 1
    this.updateEntry(entry)
    this.savePublish()
  }

  updateImage(image) {
    const entry = this.currentEntry()
    entry.thumbnail = image
    this.updateEntry(entry)
  }

  changeHost(e) {
    this.setState({hostId: e.target.value})
  }

  shareStory() {
    if (this.state.hostId === '0') {
      return
    }
    const entry = this.currentEntry()

    const icon = <span><i className="fas fa-sync fa-spin"></i></span>

    const saving = (
      <div>
        {icon}&nbsp;Sending...
      </div>
    )
    this.setState({shareStatus: saving})
    $.ajax({
      url: `stories/Host/${this.state.hostId}/submit`,
      data: {
        entryId: entry.id,
      },
      dataType: 'json',
      type: 'put',
      success: (data) => {
        if (data.error) {
          const errorMessage = (<div className="alert alert-danger">{data.error}</div>)
          this.setState({shareStatus: errorMessage})
        } else if (data.success) {
          this.setState({shareStatus: <div className="alert alert-success">Request received.</div>})
        } else {
          this.setState({shareStatus: <div className="alert alert-danger">Request failed.</div>})
        }
      },
      error: () => {
        this.setState({shareStatus: <div className="alert alert-danger">Request failed.</div>})
      }
    })
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
    let entry = this.currentEntry()
    const value = e.target.value
    entry.publishDate = moment(value).unix()
    this.updateEntry(entry)
  }

  updateSort(sortBy) {
    this.setState({
      sortBy: sortBy
    }, this.load)
  }

  updateEntry(entry) {
    let listing = this.state.listing
    listing[this.state.currentKey] = entry
    this.setState({listing})
  }

  tagChange(value) {
    const entry = this.currentEntry()
    entry.tags = value
    this.updateEntry(entry)
  }

  load() {
    let tags = []
    if (this.state.search !== this.lastSearch) {
      this.offset = 0
    }
    this.lastSearch = this.state.search
    const sendData = {
      search: this.state.search,
      sortBy: this.state.sortBy,
      sortByTagId: this.state.sortByTagId
    }
    if (this.offset > 0) {
      sendData.offset = this.offset
    }
    $.getJSON('./stories/Listing/admin', sendData).done(function (data) {
      if (data.tags != null) {
        tags = data.tags
      }
      if (data.listing == null) {
        this.setState({listing: false, loading: false, tags: tags, moreRows: false})
      } else {
        let listing
        if (this.offset > 0) {
          listing = this.state.listing.concat(data.listing)
        } else {
          listing = data.listing
        }
        this.setState(
          {listing: listing, loading: false, tags: tags, moreRows: data.more_rows}
        )
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
    let {tags} = this.state
    const entry = this.currentEntry()
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
        entry.tags.push(newTag)
        this.setState({tags})
        this.updateEntry(entry)
      }.bind(this)
    })
  }

  clearSearch() {
    this.setState({
      search: ''
    }, this.load)
  }

  setCurrentEntry(key) {
    this.setState({currentKey: key})
  }

  savePublish() {
    $.ajax({
      url: `./stories/Entry/${this.currentEntry().id}`,
      data: {
        values: [
          {
            param: 'published',
            value: this.currentEntry().published
          }, {
            param: 'publishDate',
            value: this.currentEntry().publishDate
          }
        ]
      },
      dataType: 'json',
      type: 'patch'
    })
  }

  showPublishOverlay() {
    this.setState({publishOverlay: true})
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
      {publishOverlay: false, tagOverlay: false, thumbnailOverlay: false, currentKey: null, hostId: '0', shareStatus: null}
    )
    this.unlockBody()
  }

  unlockBody() {
    $('body').css('overflow', 'inherit')
  }

  thumbnailForm(key) {
    this.setCurrentEntry(key)
    this.setState({thumbnailOverlay: true})
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
      url: `./stories/Entry/${this.currentEntry().id}`,
      data: {
        param: 'publishDate',
        value: this.currentEntry().publishDate
      },
      dataType: 'json',
      type: 'patch',
      success: function () {
        this.closeOverlay()
      }.bind(this)
    })
  }

  saveTags() {
    $.ajax({
      url: './stories/Tag/attach',
      data: {
        entryId: this.currentEntry().id,
        tags: this.currentEntry().tags
      },
      dataType: 'json',
      type: 'post',
      success: function () {
        this.closeOverlay()
      }.bind(this)
    })
  }

  render() {
    let listing
    if (this.state.loading) {
      return <Waiting label="stories"/>
    } else if (this.state.listing === false || this.state.listing.length == 0) {
      listing = <NoEntries/>
    } else {
      let listResult = this.state.listing.map(function (entry, key) {
        return <EntryRow
          deleteStory={this.deleteStory.bind(this, key)}
          entry={entry}
          sortByTag={this.sortByTag}
          showTags={this.showTags.bind(this, key)}
          setCurrentEntry={this.setCurrentEntry.bind(this, key)}
          publishStory={this.publishStory.bind(this, key)}
          thumbnailForm={this.thumbnailForm.bind(this, key)}
          key={key}
          publish={this.publish.bind(this, key)}/>
      }.bind(this))
      listing = <div>
        <div>{listResult}</div>
      </div>
    }

    const showMore = (
      this.state.moreRows === true
        ? <div className="text-center">
          <button className="btn btn-primary" onClick={this.showMore}>Show more results</button>
        </div>
        : null
    )

    let rightSide = [
      <SortBy updateSort={this.updateSort} sortBy={this.state.sortBy} key="0"/>,
      <SearchBar
        key="1"
        search={this.state.search}
        clearSearch={this.clearSearch}
        handleChange={this.searchChange}/>
    ]

    const header = {
      title: 'Stories list',
      url: './stories/Listing'
    }

    const leftSide = (
      <li className="nav-item">
        <a className="nav-link" href="./stories/Entry/create">
          <i className="fa fa-book"></i>&nbsp;Create a new story</a>
      </li>
    )

    const currentEntry = this.currentEntry()

    return (
      <div className="stories-listing">
        <PublishOverlay
          show={this.state.publishOverlay}
          shareList={this.props.shareList}
          changeHost={this.changeHost}
          shareStory={this.shareStory}
          hostId={this.state.hostId}
          savePublishDate={this.savePublishDate}
          title={currentEntry.title}
          isPublished={currentEntry.published}
          publishDate={currentEntry.publishDate}
          setPublishDate={this.setPublishDate}
          publish={this.publish}
          shareStatus={this.state.shareStatus}
          unpublish={this.unpublish}/>
        <TagOverlay
          show={this.state.tagOverlay}
          tagChange={this.tagChange}
          entryTags={currentEntry.tags}
          tags={this.state.tags}
          saveTags={this.saveTags}
          title={currentEntry.title}
          newOptionClick={this.newOptionClick}/>
        <ThumbnailOverlay
          thumbnailOverlay={this.state.thumbnailOverlay}
          updateEntry={this.updateEntry}
          entry={currentEntry}
          close={this.closeOverlay}/>
        <Navbar leftSide={leftSide} rightSide={rightSide} header={header}/>
        <div>{listing}</div>
        <div>{showMore}</div>
      </div>
    )
  }
}

EntryList.propTypes = {
  shareList: PropTypes.array
}

const NoEntries = () => {
  return (
    <p>No stories found.&nbsp;
      <a href="./stories/Entry/create">Create your first story!</a>
    </p>
  )
}
