'use strict'
import React, {Component} from 'react'
import PropTypes from 'prop-types'

export default class GuestRequests extends Component {
  constructor(props) {
    super(props)
    this.state = {}
  }

  render() {
    return (<div>Guest requests</div>)
  }
}

GuestRequests.propTypes = {
  listing: PropTypes.array,
}

GuestRequests.defaultProps = {}
