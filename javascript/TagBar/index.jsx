'use strict'
import React from 'react'
import ReactDOM from 'react-dom'
import TagBar from './TagBar.jsx'

/* global entryTags, tags, entryId, title */

ReactDOM.render(<TagBar entryTags={entryTags} tags={tags} entryId={entryId} title={title}/>, document.getElementById('TagBar'))
