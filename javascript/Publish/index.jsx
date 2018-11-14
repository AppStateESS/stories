'use strict'
import React from 'react'
import ReactDOM from 'react-dom'
import Publish from './Publish.jsx'
/* global entry, shareList */
ReactDOM.render(
  <Publish
    shareList={shareList}
    id={entry.id}
    publishDate={entry.publishDate}
    title={entry.title}
    published={entry.published}/>,
  document.getElementById(
    'Publish'
  )
)
