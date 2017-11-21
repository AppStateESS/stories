'use strict'
import React from 'react'
import PropTypes from 'prop-types'
import SortBy from './SortBy'
import './style.css'

const Navbar = (props) => {
  const {search, handleChange, clearSearch} = props

  return (
    <div className="stories-navbar">
      <nav className="navbar navbar-default navbar-fixed-top">
        <div className="container-fluid">
          <div className="navbar-header">
            <button
              type="button"
              className="navbar-toggle collapsed"
              data-toggle="collapse"
              data-target="#stories-navbar-toggle"
              aria-expanded="false">
              <span className="sr-only">Toggle navigation</span>
              <span className="icon-bar"></span>
              <span className="icon-bar"></span>
              <span className="icon-bar"></span>
            </button>
            <a className="navbar-brand" href="./stories/Listing">Stories list</a>
          </div>
          <div className="collapse navbar-collapse" id="stories-navbar-toggle">
            <ul className="nav navbar-nav">
              <li className="dropdown">
                <a
                  className="dropdown-toggle pointer"
                  data-toggle="dropdown"
                  role="button"
                  aria-haspopup="true"
                  aria-expanded="false">Manage&nbsp;
                  <i className="fa fa-chevron-down"></i>
                </a>
                <ul className="dropdown-menu">
                  <li>
                    <a href="./stories/Entry/create">
                      <i className="fa fa-book"></i>&nbsp; Create a new story</a>
                  </li>
                  <li role="separator" className="divider"></li>
                  <li>
                    <a href="./stories/Listing">
                      <i className="fa fa-list"></i>&nbsp;Stories</a>
                  </li>
                  <li>
                    <a href="./stories/Feature">
                      <span className="glyphicon glyphicon-th-large" aria-hidden="true"></span>&nbsp; Features</a>
                  </li>
                  <li>
                    <a href="./stories/Settings">
                      <i className="fa fa-gear"></i>&nbsp; Settings</a>
                  </li>
                  <li role="separator" className="divider"></li>
                  <li>
                    <a href="index.php?module=controlpanel">Control panel</a>
                  </li>
                </ul>
              </li>
            </ul>
            <ul className="nav navbar-nav navbar-right">
              <SortBy {...props}/>
              <li>
                <form className="navbar-form search" role="search" onSubmit={function(e){e.preventDefault()}}>
                  <div className="input-group">
                    <input
                      className="form-control"
                      value={search}
                      type="text"
                      placeholder="Search for stories..."
                      onChange={handleChange}/>
                    <span className="input-group-btn">
                      <button className="btn btn-default" type="button" onClick={clearSearch}>Clear</button>
                    </span>
                  </div>
                </form>
              </li>
            </ul>
          </div>
        </div>
      </nav>
    </div>
  )
}
Navbar.propTypes = {
  entry: PropTypes.object,
  search: PropTypes.string,
  clearSearch: PropTypes.func,
  handleChange: PropTypes.func,
  updateSort: PropTypes.func,
  sortBy: PropTypes.string,
  publishStory: PropTypes.func,
  showPublish: PropTypes.func,
  currentKey: PropTypes.number,
  deleteStory: PropTypes.func,
  showTags: PropTypes.func,
}

Navbar.defaultTypes = {}

export default Navbar
