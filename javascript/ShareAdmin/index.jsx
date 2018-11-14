'use strict'
import React from 'react'
import ReactDOM from 'react-dom'
import ShareAdmin from './ShareAdmin.jsx'

/* global siteName, url */
ReactDOM.render(<ShareAdmin siteName={siteName} url={url}/>, document.getElementById('ShareAdmin'))
