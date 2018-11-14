'use strict'
import React from 'react'
import ReactDOM from 'react-dom'
import GuestListing from './GuestListing.jsx'

/* global guestId */

ReactDOM.render(<GuestListing guestId={guestId}/>, document.getElementById(
  'GuestListing'
))
