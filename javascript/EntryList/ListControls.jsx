'use strict'
import React from 'react'
import PropTypes from 'prop-types'
import SortBy from './SortBy'

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

export default ListControls
