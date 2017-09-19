'use strict'
import React, {Component} from 'react'
import PropTypes from 'prop-types'

export default class SearchBar extends Component {
  constructor(props) {
    super(props)
    this.state = {
      search: ''
    }
  }

  render() {
    return (
      <div className="entry-search mb-1">
        <input name="entry-search" value={this.state.search} className="form-control" placeholder="Search..."/>
      </div>
    )
  }
}

SearchBar.propTypes = {}
