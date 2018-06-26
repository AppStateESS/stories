'use strict'
import React from 'react'
import ReactDOM from 'react-dom'
import TagBar from './TagBar.jsx'

/* global entry, tags */

ReactDOM.render(<TagBar entryTags={entry.tags} tags={tags} entryId={entry.id} title={entry.title}/>, document.getElementById('TagBar'))
