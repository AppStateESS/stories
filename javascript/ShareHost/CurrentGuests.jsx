'use strict'
import React, {Component} from 'react'
import PropTypes from 'prop-types'

export default class CurrentGuests extends Component {
  constructor(props) {
    super(props)
    this.state = {}
  }

  render() {
    if (this.props.listing.length === 0) {
      return <p>No guests found.</p>
    }
    return (<div>current guests</div>)
  }
}

CurrentGuests.propTypes = {
  listing: PropTypes.array,
}

CurrentGuests.defaultProps = {}
