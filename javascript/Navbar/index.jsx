'use strict'
import React from 'react'
import ReactDOM from 'react-dom'
import Navbar from '../AddOn/Navbar'

ReactDOM.render(<Navbar header="Edit story" rightSide={<li><a><em>Story status:</em> <span id="story-status">{status}</span></a></li>}/>, document.getElementById(
  'Navbar'
))
