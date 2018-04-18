'use strict'
import React from 'react'
import PropTypes from 'prop-types'

const Options = ({entryId, deleteStory, publishStory, published, allowPublish}) => {
  let publishLabel = 'Publish'
  if (published == 1) {
    publishLabel = 'Publish'
  }
  return (
    <div>
      <a
        className="btn btn-sm btn-outline-secondary mr-1"
        href={`./stories/Entry/${entryId}/edit`}>Edit</a>
      {
        allowPublish
          ? <a href="./stories/Listing/admin#" className="btn btn-sm btn-outline-secondary mr-1" onClick={publishStory}>{publishLabel}</a>
          : null
      }
      <a href="./stories/Listing/admin#" className="btn btn-sm btn-danger mr-1" onClick={deleteStory}>
        Delete</a>
    </div>
  )
}

Options.propTypes = {
  entryId: PropTypes.string,
  deleteStory: PropTypes.func,
  isPublished: PropTypes.oneOfType([PropTypes.string, PropTypes.number,]),
  publishStory: PropTypes.func,
  allowPublish: PropTypes.bool,
  published: PropTypes.oneOfType(
    [PropTypes.bool, PropTypes.string, PropTypes.number,]
  )
}

export default Options
