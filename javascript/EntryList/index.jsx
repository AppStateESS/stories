'use strict'
import React from 'react'
import ReactDOM from 'react-dom'
import EntryList from './EntryList.jsx'

/* global shareList */

ReactDOM.render(<EntryList shareList={shareList}/>, document.getElementById('EntryList'))
