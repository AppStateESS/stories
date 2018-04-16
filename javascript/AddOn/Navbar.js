'use strict'
import React from 'react'
import PropTypes from 'prop-types'
import './nav.css'

const Navbar = (props) => {
  const {rightSide, leftSide, header,} = props

  let url = './'
  let title
  if (header != null) {
    if (header.title != undefined) {
      if (header.url != null) {
        url = header.url
      }
      title = header.title
    } else {
      title = header
    }
  }

  return (
    <div className="stories-navbar">
      <nav className="navbar navbar-expand-lg fixed-top navbar-light">
        <a className="navbar-brand" href={url}>{title}</a>
        <button
          type="button"
          className="navbar-toggler"
          data-toggle="collapse"
          data-target="#stories-navbar-toggle"
          aria-expanded="false"
          aria-label="Toggle navigation">
          <span className="navbar-toggler-icon"></span>
        </button>
        <div className="collapse navbar-collapse" id="stories-navbar-toggle">
          <ul className="navbar-nav mr-auto">
            <li className="nav-item dropdown">
              <a
                className="nav-link dropdown-toggle pointer"
                data-toggle="dropdown"
                role="button"
                aria-haspopup="true"
                aria-expanded="false">Manage
              </a>
              <div className="dropdown-menu">
                <a className="dropdown-item" href="./stories/Entry/create">
                  <i className="fas fa-book"></i>&nbsp; Create a new story</a>
                <hr/>
                <a className="dropdown-item" href="./stories/Listing/admin">
                  <i className="fas fa-list"></i>&nbsp;Stories</a>
                <a className="dropdown-item" href="./stories/Feature">
                  <span className="fas fa-th-large"></span>&nbsp;Features</a>
                <a className="dropdown-item" href="./stories/Author">
                  <i className="fas fa-user"></i>&nbsp;Authors</a>
                <a className="dropdown-item" href="./stories/Settings">
                  <i className="fas fa-cog"></i>&nbsp;Settings</a>
                <hr/>
                <a className="dropdown-item" href="index.php?module=controlpanel">Control panel</a>
              </div>
            </li>
            {leftSide}
          </ul>
          <ul className="nav navbar-nav navbar-right">
            {rightSide}
          </ul>
        </div>
      </nav>
    </div>
  )
}
Navbar.propTypes = {
  leftSide: PropTypes.oneOfType([PropTypes.array, PropTypes.object,]),
  rightSide: PropTypes.oneOfType([PropTypes.array, PropTypes.object,]),
  header: PropTypes.oneOfType([PropTypes.object, PropTypes.string,]),
}

export default Navbar
