'use strict'
import React from 'react'
import ReactDOM from 'react-dom'
import Publish from './Publish.jsx'
/* global entryId, published, publishDate, title, tags */
const props = {entryId, published, publishDate, title}
ReactDOM.render(<Publish {...props}/>, document.getElementById('PublishBar'))
