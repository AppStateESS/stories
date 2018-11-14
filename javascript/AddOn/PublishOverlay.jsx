import React from 'react'
import PropTypes from 'prop-types'
import Overlay from '@essappstate/canopy-react-overlay'
import ShareStory from './ShareStory'
import moment from 'moment'

const PublishOverlay = ({
  publishDate,
  isPublished,
  unpublish,
  savePublishDate,
  title,
  publish,
  setPublishDate,
  shareStatus,
  shareList,
  shareStory,
  changeHost,
  hostId,
  show
}) => {
  let formattedDate = moment().format('YYYY-MM-DDThh:mm')
  if (publishDate.length !== 0) {
    formattedDate = moment.unix(publishDate).format('YYYY-MM-DDTkk:mm')
  }
  
  let publishButton
  const closeButton = (
    <button className="btn btn-danger btn-block" onClick={savePublishDate}>Close</button>
  )

  const now = parseInt(moment().format('X'))
  const relative = moment(publishDate * 1000).format('LLL')

  if (isPublished == 0) {
    let publishLabel = 'Publish now!'
    if (publishDate > now) {
      publishLabel = `Publish after ${relative}`
    }
    publishButton = <button className="btn btn-primary btn-block mb-1" onClick={publish}>{publishLabel}</button>
  } else {
    publishButton = <button className="btn btn-warning btn-block mb-1" onClick={unpublish}>Unpublish</button>
  }

  let shareStoryForm
  if (isPublished == 1 && shareList.length > 0) {
    shareStoryForm = (
      <div className="card mb-4 border-primary">
        <div className="card-body">
          <ShareStory shareList={shareList} shareStory={shareStory} changeHost={changeHost} hostId={hostId}/> {shareStatus}
        </div>
      </div>
    )
  }

  return (
    <Overlay
      show={show}
      close={savePublishDate}
      width="500px"
      title={`Publish story: ${title}`}>
      <div className="card mb-4 border-primary">
        <div className="card-body">
          <div className="input-group mb-2">
            <div className="input-group-prepend">
              <span className="input-group-text">Show story after:</span>
            </div>
            <input
              className="form-control"
              type="datetime-local"
              value={formattedDate}
              onChange={setPublishDate}/>
          </div>
          <div>{publishButton}</div>
          <div className="text-center mt-2">
            <span className="badge badge-info">Note: stories without content will not be published.</span>
          </div>
        </div>
      </div>
      {shareStoryForm}
      <div>{closeButton}</div>
    </Overlay>
  )
}
PublishOverlay.propTypes = {
  savePublishDate: PropTypes.func,
  title: PropTypes.string,
  shareList: PropTypes.array,
  shareStatus: PropTypes.element,
  shareStory: PropTypes.func,
  hostId: PropTypes.oneOfType([PropTypes.string,PropTypes.number,]),
  isPublished: PropTypes.oneOfType([PropTypes.number, PropTypes.string]),
  unpublish: PropTypes.func,
  publish: PropTypes.func,
  publishDate: PropTypes.oneOfType([PropTypes.string, PropTypes.number]),
  setPublishDate: PropTypes.func,
  publishStory: PropTypes.func,
  changeHost: PropTypes.func,
  show: PropTypes.bool
}

PublishOverlay.defaultProps = {
  shareList: []
}

export default PublishOverlay
