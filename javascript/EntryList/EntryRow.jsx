'use strict'
import React from 'react'
import PropTypes from 'prop-types'

const EntryRow = ({entry, select, selected,}) => {
  const noImage = () => {
    return (
      <div className="no-image">
        <div>
          <i className="fa fa-camera fa-5x"></i><br/>No image</div>
      </div>
    )
  }

  const {
    authorEmail,
    authorName,
    //authorPic,
    createDate,
    expirationDate,
    //id,
    //publishDate,
    //published,
    summary,
    thumbnail,
    title,
  } = entry

  const mailto = 'mailto:' + authorEmail

  let image = noImage
  if (thumbnail.length > 0) {
    image = <img className="img-responsive" src={thumbnail}/>
  }

  let rowClass = 'row entry-row mb-1'
  if (selected) {
    rowClass = rowClass + ' selected'
  }

  let expire = expirationDate

  if (!expirationDate) {
    expire = 'Never'
  }
  return (
    <div className={rowClass} onClick={select}>
      <div className="col-sm-2">
        <div className="entry-image">
          {image}
        </div>
      </div>
      <div className="col-sm-10">
        <h3>{title}
        </h3>
        <div>
          <p>{summary}</p>
        </div>
        <div>
          <strong>Author:</strong> <a href={mailto}>{authorName}</a>
        </div>
        <div><strong>Created:</strong> {createDate}<br/><strong>Expires:</strong> {expire}</div>
      </div>
    </div>
  )
}

EntryRow.propTypes = {
  entry: PropTypes.object.isRequired,
  select: PropTypes.func.isRequired,
  selected: PropTypes.bool
}

export default EntryRow
