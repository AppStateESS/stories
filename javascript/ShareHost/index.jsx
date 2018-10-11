'use strict'
import React from 'react'
import ReactDOM from 'react-dom'
import ShareHost from './ShareHost.jsx'

/* global siteName, url */
ReactDOM.render(<ShareHost siteName={siteName} url={url}/>, document.getElementById('ShareHost'))
