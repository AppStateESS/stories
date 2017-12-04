'use strict'
import React from 'react'
import ReactDOM from 'react-dom'
import Navbar from '../AddOn/Navbar'

/* global entry */

const rightSide = (
  <li>
    <a>
      <em>
        Story status:
      </em>&nbsp;
      <span id="story-status">
        {status}
      </span>
    </a>
  </li>
)

const leftSide = (
  <li>
    <a href={`./stories/${entry.urlTitle}`}>View story</a>
  </li>
)


ReactDOM.render(
  <Navbar header="Edit story" rightSide={rightSide} leftSide={leftSide}/>,
  document.getElementById(
    'Navbar'
  )
)
