'use strict'
import React from 'react'
import PropTypes from 'prop-types'

const Toolbar = ({entry, publish, deleteStory,}) => {
  let buttons
  let title = <em>Select a story to edit</em>
  if (entry !== null) {
    title = <strong>{entry.title}</strong>

    buttons = <span>
      <a
        href={`stories/Entry/${entry.id}/edit`}
        className="btn btn-sm btn-primary ml-1">
        <i className="fa fa-pencil"></i>&nbsp; Edit
      </a>
      <a href={`stories/Entry/${entry.id}`} className="btn btn-sm btn-default ml-1">
        <i className="fa fa-eye"></i>&nbsp; View
      </a>
      <button className="btn btn-sm btn-danger ml-1" onClick={deleteStory}>
        <i className="fa fa-trash-o"></i>&nbsp;Delete</button>
    </span>
  }
  return (
    <div className="row entry-toolbar">
      <div className="col-sm-2">
        <a className="btn btn-sm btn-success" href="./stories/Entry/create">
          <i className="fa fa-book"></i>&nbsp;
          Create</a>
      </div>
      <div className="col-sm-5">
        {title}
      </div>
      <div className="col-sm-5">
        {buttons}
      </div>
    </div>
  )
}

Toolbar.propTypes = {
  entry: PropTypes.object,
  publish: PropTypes.func.isRequired,
  deleteStory: PropTypes.func.isRequired
}

export default Toolbar
