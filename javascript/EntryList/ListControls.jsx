'use strict'
import React from 'react'
import PropTypes from 'prop-types'

const ListControls = (props) => {
  const {search, handleChange, clearSearch,} = props
  return (
    <div className="mb-1 row">
      <div className="col-sm-3">
        <a className="btn btn-sm btn-success" href="./stories/Entry/create">
          <i className="fa fa-book"></i>&nbsp; Create a new story</a>
      </div>
      <div className="col-sm-3">
        <SortBy {...props}/>
      </div>
      <div className="col-sm-6">
        <div className="input-group">
          <input
            className="form-control"
            value={search}
            type="text"
            placeholder="Search for stories..."
            onChange={handleChange}/>
          <span className="input-group-btn">
            <button className="btn btn-outline-dark" type="button" onClick={clearSearch}>Clear</button>
          </span>
        </div>
      </div>
    </div>
  )
}

ListControls.propTypes = {
  search: PropTypes.string,
  clearSearch: PropTypes.func,
  handleChange: PropTypes.func,
  updateSort: PropTypes.func,
  sortBy: PropTypes.string
}

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
    <div className="dropdown">
      <button
        className="btn btn-outline-dark btn-sm dropdown-toggle"
        type="button"
        id="dropdownMenu1"
        data-toggle="dropdown"
        aria-haspopup="true"
        aria-expanded="true">
        Sort by {sortStr}&nbsp;
        <span className="caret"></span>
      </button>
      <ul className="dropdown-menu sortby" aria-labelledby="dropdownMenu1">
        <li className="pointer" onClick={updateSort.bind(null, 'publishDate')}>Published</li>
        <li className="pointer" onClick={updateSort.bind(null, 'updateDate')}>Updated</li>
        <li className="pointer" onClick={updateSort.bind(null, 'title')}>Title</li>
      </ul>
    </div>
  )
}

SortBy.propTypes = {
  sortBy: PropTypes.string,
  updateSort: PropTypes.func
}

export default ListControls
