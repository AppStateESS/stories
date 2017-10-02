import React from 'react'
import PropTypes from 'prop-types'
import Overlay from '../AddOn/Overlay'
import moment from 'moment'

const PublishOverlay = ({
  entry,
  close,
  publishStory,
  setPublishDate,
  updateTags,
  tags,
}) => {
  let publishDate = moment().format('YYYY-MM-DDThh:mm')
  const rightNow = publishDate
  if (entry.publishDate.length !== 0) {
    publishDate = moment.unix(entry.publishDate).format('YYYY-MM-DDTkk:mm')
  }

  let publishAfter = "Publish now"
  if (publishDate > rightNow) {
    publishAfter = "Publish after"
  }

  const inputHeight = {
    height: '32px'
  }

  return (
    <Overlay close={close} width="500px" height="350px" title={`Publish story: ${entry.title}`}>
      <div className="mb-1">
        Before publishing you may choose to add a few tags:
        <textarea
          className="form-control"
          onChange={updateTags}
          value={tags}
          placeholder="Add tags here separated by commas"/>
      </div>
      <div className="text-center mb-1">
        Publish after:&nbsp;
        <input
          type="datetime-local"
          style={inputHeight}
          value={publishDate}
          onChange={setPublishDate}/>
      </div>
      <div className="text-center">
        <button className="btn btn-primary" onClick={publishStory}>{publishAfter}</button>
      </div>
    </Overlay>
  )
}

PublishOverlay.propTypes = {
  tags: PropTypes.string,
  close: PropTypes.func,
  publishStory: PropTypes.func,
  setPublishDate: PropTypes.func,
  entry: PropTypes.object,
  updateTags: PropTypes.func
}

export default PublishOverlay
