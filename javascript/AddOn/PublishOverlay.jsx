import React from 'react'
import PropTypes from 'prop-types'
import Overlay from '@essappstate/canopy-react-overlay'
import ShareStory from './ShareStory'
import moment from 'moment'
import DatePicker from 'react-datepicker'
import "react-datepicker/dist/react-datepicker.css"

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
  const publishDateObj = new Date(publishDate * 1000)

  let publishButton
  const closeButton = (
    <button className="btn btn-danger btn-block" onClick={savePublishDate}>Close</button>
  )

  const now = parseInt(moment().format('X'))
  const relative = moment(publishDate * 1000).format('MMM DD, YYYY hh:mm a')

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
          <ShareStory
            shareList={shareList}
            shareStory={shareStory}
            changeHost={changeHost}
            hostId={hostId}/> {shareStatus}
        </div>
      </div>
    )
  }

  return (
    <Overlay
      show={show}
      close={savePublishDate}
      width="550px"
      height="400px"
      title={`Publish story: ${title}`}>
      <div className="card mb-4 border-primary">
        <div className="card-body">
          <div className="input-group mb-2">
            <div className="input-group-prepend">
              <span className="input-group-text">Show story after:</span>
            </div>
            <DatePicker
              selected={publishDateObj}
              onChange={setPublishDate}
              className="form-control"
              showTimeSelect={true}
              timeFormat="HH:mm"
              timeIntervals={15}
              dateFormat="MMM d, yyyy h:mm aa"
              timeCaption="time"/>
          </div>
          <div>{publishButton}</div>
          <div className="text-center mt-2">
            <span className="badge badge-info">Note: stories without title or content will not be published.</span>
          </div>
        </div>
      </div>
      {shareStoryForm}
      <div style={{position: 'absolute', bottom: 5, right: 5}}>{closeButton}</div>
    </Overlay>
  )
}
PublishOverlay.propTypes = {
  savePublishDate: PropTypes.func,
  title: PropTypes.string,
  shareList: PropTypes.array,
  shareStatus: PropTypes.element,
  shareStory: PropTypes.func,
  hostId: PropTypes.oneOfType([PropTypes.string, PropTypes.number]),
  isPublished: PropTypes.oneOfType([PropTypes.number, PropTypes.string]),
  unpublish: PropTypes.func,
  publish: PropTypes.func,
  publishDate: PropTypes.oneOfType([PropTypes.string, PropTypes.number]),
  publishDateObj: PropTypes.object,
  setPublishDate: PropTypes.func,
  publishStory: PropTypes.func,
  changeHost: PropTypes.func,
  show: PropTypes.bool,
  close: PropTypes.func,
}

PublishOverlay.defaultProps = {
  shareList: []
}

export default PublishOverlay
