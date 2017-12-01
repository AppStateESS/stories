'use strict'
import React from 'react'
import PropTypes from 'prop-types'
import './nav.css'

const Navbar = (props) => {
  const {rightSide, leftSide, header} = props

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
            <a className="navbar-brand" href={url}>{title}</a>
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
                      <span className="glyphicon glyphicon-th-large" aria-hidden="true"></span>&nbsp;Features</a>
                  </li>
                  <li>
                    <a href="./stories/Author">
                      <i className="fa fa-user"></i>&nbsp;Authors</a>
                  </li>
                  <li>
                    <a href="./stories/Settings">
                      <i className="fa fa-gear"></i>&nbsp;Settings</a>
                  </li>
                  <li role="separator" className="divider"></li>
                  <li>
                    <a href="index.php?module=controlpanel">Control panel</a>
                  </li>
                </ul>
              </li>
              {leftSide}
            </ul>
            <ul className="nav navbar-nav navbar-right">
              {rightSide}
            </ul>
          </div>
        </div>
      </nav>
    </div>
  )
}
Navbar.propTypes = {
  leftSide: PropTypes.oneOfType([PropTypes.array,PropTypes.object,]),
  rightSide: PropTypes.oneOfType([PropTypes.array,PropTypes.object,]),
  header: PropTypes.oneOfType([PropTypes.object, PropTypes.string,])
}

export default Navbar
