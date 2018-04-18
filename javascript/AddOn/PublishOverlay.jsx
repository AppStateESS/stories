import React from 'react'
import PropTypes from 'prop-types'
import Overlay from './Overlay'
import moment from 'moment'

const PublishOverlay = ({
  publishDate,
  isPublished,
  unpublish,
  savePublishDate,
  title,
  publish,
  setPublishDate
}) => {
  let formattedDate = moment().format('YYYY-MM-DDThh:mm')
  if (publishDate.length !== 0) {
    formattedDate = moment.unix(publishDate).format('YYYY-MM-DDTkk:mm')
  }

  const inputHeight = {
    height: '32px'
  }

  let publishButton
  const closeButton = (
    <button className="btn btn-outline-secondary btn-block" onClick={savePublishDate}>Close</button>
  )

  if (isPublished == 0) {
    publishButton = <button className="btn btn-primary btn-block mb-1" onClick={publish}>Publish</button>
  } else {
    publishButton = <button className="btn btn-info btn-block mb-1" onClick={unpublish}>Unpublish</button>
  }

  return (
    <Overlay
      close={savePublishDate}
      width="500px"
      height="300px"
      title={`Publish story: ${title}`}>
      <div className="text-center mb-1">
        Show story after:&nbsp;
        <input
          type="datetime-local"
          style={inputHeight}
          value={formattedDate}
          onChange={setPublishDate}/>
      </div>
      <div className="text-center">
        <div>{publishButton}</div>
        <div>{closeButton}</div>
      </div>
      <div className="text-center mt-1">
        <span className="badge badge-info">Note: stories without content will not be published.</span>
      </div>
      
    </Overlay>
  )
}

PublishOverlay.propTypes = {
  savePublishDate: PropTypes.func,
  title: PropTypes.string,
  isPublished: PropTypes.oneOfType([PropTypes.number,PropTypes.string,]),
  unpublish: PropTypes.func,
  publish: PropTypes.func,
  publishDate: PropTypes.oneOfType([PropTypes.string, PropTypes.number,]),
  setPublishDate: PropTypes.func,
  publishStory: PropTypes.func
}

export default PublishOverlay
