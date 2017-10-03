import React from 'react'
import PropTypes from 'prop-types'
import Overlay from './Overlay'
import moment from 'moment'

const PublishOverlay = ({
  publishDate,
  published,
  save,
  title,
  close,
  publishStory,
  setPublishDate,
  updateTags,
  tags,
}) => {

  let formattedDate = moment().format('YYYY-MM-DDThh:mm')
  if (publishDate.length !== 0) {
    formattedDate = moment.unix(publishDate).format('YYYY-MM-DDTkk:mm')
  }

  const inputHeight = {
    height: '32px'
  }

  const saveClose = () => {
    save()
    close()
  }

  const publishClose = () => {
    publishStory()
    close()
  }

  let publishButton
  const closeButton = (<button className="btn btn-default btn-block" onClick={saveClose}>Close</button>)

  if (published == 0) {
    publishButton = <button className="btn btn-primary btn-block mb-1" onClick={publishClose}>Publish</button>
  }

  return (
    <Overlay
      close={saveClose}
      width="500px"
      height="350px"
      title={`Publish story: ${title}`}>
      <div className="mb-1">
        Before publishing you may choose to add a few tags:
        <textarea
          className="form-control"
          onChange={updateTags}
          value={tags}
          placeholder="Add tags here separated by commas"/>
      </div>
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
    </Overlay>
  )
}

PublishOverlay.propTypes = {
  tags: PropTypes.string,
  save: PropTypes.func,
  published: PropTypes.string,
  publishDate: PropTypes.oneOfType([PropTypes.string, PropTypes.number,]),
  title: PropTypes.string,
  close: PropTypes.func,
  publishStory: PropTypes.func,
  setPublishDate: PropTypes.func,
  updateTags: PropTypes.func
}

export default PublishOverlay
