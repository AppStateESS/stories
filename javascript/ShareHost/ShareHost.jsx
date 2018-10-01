'use strict'
import React, {Component} from 'react'
import CurrentGuests from './CurrentGuests'
import GuestRequests from './GuestRequests'
//import PropTypes from 'prop-types'

/* global $ */

export default class ShareHost extends Component {
  constructor(props) {
    super(props)
    this.state = {
      guestRequests: [],
      currentGuests: [],
      loading: true
    }
    this.showRequestForm = this.showRequestForm.bind(this)
  }

  componentDidMount() {
    this.load()
  }

  load() {
    $.ajax({
      url: './stories/Host',
      dataType: 'json',
      type: 'get',
      success: (data) => {
        this.setState(
          {loading: false, guestRequests: data.guestRequests, currentGuests: data.currentGuests}
        )
      },
      error: () => {},
    })
  }

  showRequestForm() {}

  render() {
    if (this.state.loading) {
      return <Loading/>
    }
    return (
      <div>
        <button className="btn btn-outline-dark" onClick={this.showRequestForm}>
          <i className="fas fa-share-alt"></i>&nbsp;Request sharing</button>
        <hr/>
        <h3>Guest Requests</h3>
        <GuestRequests listing={this.state.guestRequests}/>
        <hr/>
        <h3>Current Guests</h3>
        <CurrentGuests listing={this.state.currentGuests}/>
      </div>
    )
  }
}

ShareHost.propTypes = {}

ShareHost.defaultProps = {}

const Loading = () => {
  return (
    <div className="text-center">
      <span className="lead">
        <i className="fas fa-spinner fa-pulse"></i>&nbsp;Loading</span>
    </div>
  )
}
