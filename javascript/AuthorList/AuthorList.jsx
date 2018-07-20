'use strict'
import React, {Component} from 'react'
import AuthorRow from './AuthorRow'
import Waiting from '../AddOn/Waiting'
import Navbar from '../AddOn/Navbar'
import SearchBar from '../AddOn/SearchBar'
import PictureOverlay from './PictureOverlay'
import AuthorOverlay from './AuthorOverlay'
import Author from '../Resource/Author.js'
import './style.css'

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
      currentAuthor: new Author,
      search: '',
      moreRows: true,
      pictureOverlay: false,
      authorOverlay: false,
      unauthored: []
    }

    this.load = this.load.bind(this)
    this.currentAuthor = this.currentAuthor.bind(this)
    this.closeOverlay = this.closeOverlay.bind(this)
    this.update = this.setCurrentAuthor.bind(this)
    this.updateImage = this.updateImage.bind(this)
    this.clearSearch = this.clearSearch.bind(this)
    this.searchChange = this.searchChange.bind(this)
    this.updateAuthor = this.updateAuthor.bind(this)
    this.updateAuthorList = this.updateAuthorList.bind(this)
  }

  authorForm(key) {
    this.setCurrentAuthor(key)
    this.setState({authorOverlay: true})
  }

  componentWillMount() {
    //this.pullUnauthored()
  }

  componentDidMount() {
    this.load()
  }

  clearSearch() {
    this.setState({
      search: ''
    }, this.load)
  }

  closeOverlay() {
    this.resetAuthor()
    this.setState({currentKey: null, pictureOverlay: false, authorOverlay: false,})
  }

  currentAuthor() {
    return this.state.currentAuthor
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
    this.pullUnauthored()
  }

  pullUnauthored() {
    $.getJSON('./stories/Author/unauthored').done(function (data) {
      this.setState({unauthored: data})
    }.bind(this))
  }

  render() {
    const rightSide = [
      <SearchBar
        key="1"
        search={this.state.search}
        clearSearch={this.clearSearch}
        handleChange={this.searchChange}/>,
    ]

    const addAuthor = (
      <a
        href="#"
        key="1"
        className="nav-link"
        onClick={(e) => {
          e.preventDefault()
          this.authorForm(-1)
        }}>
        <span>
          <i className="fas fa-plus"></i>&nbsp;Add author</span>
      </a>
    )

    const leftSide = [addAuthor]

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
          showForm={this.authorForm.bind(this, key)}
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
        <Navbar header={header} rightSide={rightSide} leftSide={leftSide}/>
        <PictureOverlay
          show={this.state.pictureOverlay}
          savePicture={this.savePicture}
          updateAuthorList={this.updateAuthorList}
          updateAuthor={this.updateAuthor}
          updateImage={this.updateImage}
          author={this.currentAuthor()}
          close={this.closeOverlay}/>
        <AuthorOverlay
          show={this.state.authorOverlay}
          updateAuthorList={this.updateAuthorList}
          reload={this.load}
          close={this.closeOverlay}
          unAuthored={this.state.unauthored}
          updateAuthor={this.updateAuthor}
          author={this.currentAuthor()}/>
        <div>{listing}</div>
      </div>
    )
  }

  resetAuthor() {
    this.setState({'currentAuthor': new Author})
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

  showMore() {
    this.offset = this.offset + 1
    this.load()
  }

  setCurrentAuthor(key) {
    if (key === -1) {
      this.resetAuthor()
      return
    }
    const author = Object.assign({}, this.state.listing[key])
    this.setState({currentKey: key, currentAuthor: author,})
  }

  thumbnailForm(key) {
    this.setCurrentAuthor(key)
    this.setState({pictureOverlay: true})
  }

  updateAuthor(author) {
    this.setState({currentAuthor: author})
  }

  updateImage(image) {
    const author = this.currentAuthor()
    author.pic = image
    this.updateAuthor(author)
  }

  updateAuthorList() {
    const listing = this.state.listing
    listing[this.state.currentKey] = this.state.currentAuthor
    this.setState({listing})
  }

}

AuthorList.propTypes = {}
