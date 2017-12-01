'use strict'
import React, {Component} from 'react'
import AuthorRow from './AuthorRow'
import Waiting from '../AddOn/Waiting'
import Navbar from '../AddOn/Navbar'
import SearchBar from '../AddOn/SearchBar'
import PictureOverlay from './PictureOverlay'

/* global $ */

export default class AuthorList extends Component {
  constructor(props) {
    super(props)
    this.delay
    this.offset = 0
    this.state = {
      listing: null,
      loading: true,
      currentKey: null,
      search: '',
      moreRows: true,
      pictureOverlay: false
    }

    this.load = this.load.bind(this)
    this.currentAuthor = this.currentAuthor.bind(this)
    this.closeOverlay = this.closeOverlay.bind(this)
    this.update = this.update.bind(this)
    this.updateImage = this.updateImage.bind(this)
    this.clearSearch = this.clearSearch.bind(this)
    this.searchChange = this.searchChange.bind(this)
  }

  componentDidMount() {
    this.load()
  }

  load() {
    const sendData = {
      search: this.state.search
    }
    $.getJSON('./stories/Author/list', sendData).done(function (data) {
      if (data.listing == null) {
        this.setState({listing: false, loading: false, moreRows: false})
      } else {
        let listing
        if (this.offset > 0) {
          listing = this.state.listing.concat(data.listing)
        } else {
          listing = data.listing
        }
        this.setState({listing: listing, loading: false, moreRows: data.moreRows})
      }
    }.bind(this))
  }

  showMore() {
    this.offset = this.offset + 1
    this.load()
  }

  update(key) {
    this.setState({currentKey: key})
  }

  clearSearch() {
    this.setState({
      search: ''
    }, this.load)
  }

  currentAuthor() {
    if (this.state.currentKey === null) {
      return null
    }
    return this.state.listing[this.state.currentKey]
  }

  updateAuthor(author) {
    let listing = this.state.listing
    listing[this.state.currentKey] = author
    this.setState({listing})
  }

  thumbnailForm(key) {
    this.setState({currentKey: key, pictureOverlay: true,})
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

  updateImage(image) {
    const author = this.currentAuthor()
    author.pic = image
    this.updateAuthor(author)
  }

  closeOverlay() {
    this.setState({currentKey: null, pictureOverlay: false,})
  }

  render() {
    const rightSide = [
      <SearchBar
        key="1"
        search={this.state.search}
        clearSearch={this.clearSearch}
        handleChange={this.searchChange}/>,
    ]

    const header = {
      title: 'Author list',
      url: 'stories/Author',
    }
    let listing
    if (this.state.loading) {
      listing = <Waiting/>
    } else if (this.state.listing == null || this.state.listing[0] == null) {
      listing = <div>No authors found.</div>
    } else {
      let rows = this.state.listing.map(function (value, key) {
        return <AuthorRow
          key={key}
          author={value}
          update={this.update.bind(this, key)}
          thumbnail={this.thumbnailForm.bind(this, key)}/>
      }.bind(this))
      listing = (
        <table className="table table-striped">
          <tbody>
            <tr>
              <th></th>
              <th></th>
              <th>Name</th>
              <th>Contact email</th>
              <th>Last logged</th>
            </tr>
            {rows}
          </tbody>
        </table>
      )
    }
    return (
      <div>
        <Navbar header={header} rightSide={rightSide}/>
        <PictureOverlay
          show={this.state.pictureOverlay}
          savePicture={this.savePicture}
          updateAuthor={this.updateAuthor}
          updateImage={this.updateImage}
          author={this.currentAuthor()}
          close={this.closeOverlay}/>{listing}
      </div>
    )
  }
}

AuthorList.propTypes = {}
