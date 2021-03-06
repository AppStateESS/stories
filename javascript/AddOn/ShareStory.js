'use strict'
import React from 'react'
import PropTypes from 'prop-types'

const ShareStory = ({shareList, shareStory, changeHost, hostId}) => {
  if (shareList.length === 0) {
    return null
  }
  let options = shareList.map((value, key) => {
    return <option key={key} value={value.value}>{value.label}</option>
  })
  return (
    <div className="row mb-2">
      <div className="col-8">
        <select className="form-control" onChange={changeHost} value={hostId}>
          <option value="0">&nbsp;</option>
          {options}
        </select>
      </div>
      <div className="col-4">
        <button className="btn btn-primary" onClick={shareStory}>
          <i className="fas fa-share"></i>&nbsp;Share</button>
      </div>
    </div>
  )
}

ShareStory.propTypes = {
  shareList: PropTypes.array,
  changeHost: PropTypes.func,
  shareStory: PropTypes.func,
  hostId : PropTypes.oneOfType([PropTypes.string,PropTypes.number,]),
}

export default ShareStory
