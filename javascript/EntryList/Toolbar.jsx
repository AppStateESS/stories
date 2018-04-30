'use strict'
import React from 'react'
import PropTypes from 'prop-types'

const Toolbar = ({entry, publish, deleteStory}) => {
  let buttons

  buttons = <span>
    <a
      href={`stories/Entry/${entry.id}/edit`}
      className="btn btn-sm btn-primary ml-1">
      <i className="fas fa-pencil-alt"></i>&nbsp; Edit
    </a>
    <a href={`stories/Entry/${entry.id}`} className="btn btn-sm btn-outline-dark ml-1">
      <i className="fa fa-eye"></i>&nbsp; View
    </a>
    {entry.published ? 'Published' : 'Publish now'}
    <button className="btn btn-sm btn-danger ml-1" onClick={deleteStory}>
      <i className="far fa-trash-alt"></i>&nbsp;Delete</button>
  </span>
  return (
    <div className="row">
      <div className="col-sm-12">
        {buttons}
      </div>
    </div>
  )
}

Toolbar.propTypes = {
  entry: PropTypes.object,
  publish: PropTypes.func.isRequired,
  deleteStory: PropTypes.func.isRequired,
}

export default Toolbar
