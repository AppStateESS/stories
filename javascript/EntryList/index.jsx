'use strict'
import React from 'react'
import ReactDOM from 'react-dom'
import EntryList from './EntryList.jsx'

/* global segmentSize */
ReactDOM.render(<EntryList segmentSize={segmentSize}/>, document.getElementById('EntryList'))
