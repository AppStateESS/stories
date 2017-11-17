'use strict'
import React from 'react'
import PropTypes from 'prop-types'

const SortBy = ({sortBy, updateSort,}) => {
  let sortStr
  switch (sortBy) {

    case 'updateDate':
      sortStr = 'last updated'
      break

    case 'title':
      sortStr = 'title'
      break

    default:
      sortStr = 'date published'
      break
  }
  return (
    <li className="dropdown">
      <a
        className="dropdown-toggle pointer"
        data-toggle="dropdown"
        aria-haspopup="true"
        aria-expanded="true">
        Sort by {sortStr}&nbsp;
        <i className="fa fa-chevron-down"></i>
      </a>
      <ul className="dropdown-menu sortby" aria-labelledby="dropdownMenu1">
        <li className="pointer" onClick={updateSort.bind(null, 'publishDate')}>Published</li>
        <li className="pointer" onClick={updateSort.bind(null, 'updateDate')}>Updated</li>
        <li className="pointer" onClick={updateSort.bind(null, 'title')}>Title</li>
      </ul>
    </li>
  )
}

SortBy.propTypes = {
  sortBy: PropTypes.string,
  updateSort: PropTypes.func
}

export default SortBy
