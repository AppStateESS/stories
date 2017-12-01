'use strict'
import React from 'react'
import PropTypes from 'prop-types'

const SearchBar = ({search, handleChange, clearSearch}) => {
  return (
    <li>
      <form
        className="navbar-form search"
        role="search"
        onSubmit={function (e) {
          e.preventDefault()
        }}>
        <div className="input-group">
          <input
            className="form-control"
            value={search}
            type="text"
            placeholder="Search..."
            onChange={handleChange}/>
          <span className="input-group-btn">
            <button className="btn btn-default" type="button" onClick={clearSearch}>Clear</button>
          </span>
        </div>
      </form>
    </li>
  )
}

SearchBar.propTypes = {
  search: PropTypes.string,
  clearSearch: PropTypes.func,
  handleChange: PropTypes.func,
}

export default SearchBar
