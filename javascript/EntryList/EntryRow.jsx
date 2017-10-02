'use strict'
import React from 'react'
import PropTypes from 'prop-types'
import moment from  'moment'

const EntryRow = (props) => {
  const noImage = () => {
    return (
      <div className="no-image">
        <div>
          <i className="fa fa-camera fa-5x"></i><br/>No image</div>
      </div>
    )
  }

  const {entry, deleteStory, publishStory,} = props

  const {
    authorEmail, authorName,
    //authorPic,
    createDate,
    createDateRelative,
    expirationDate,
    publishDate,
    publishDateRelative,
    published,
    id,
    summary,
    thumbnail,
    title
  } = entry

  const mailto = 'mailto:' + authorEmail

  let image = noImage
  if (thumbnail.length > 0) {
    image = <img className="img-responsive" src={thumbnail}/>
  }

  let expire = expirationDate

  if (!expirationDate) {
    expire = 'Never'
  }

  let publish = publishDate
  let publishLabel = 'Published on'

  if (!published) {
    publish = 'Unpublished'
  } else {
    if (publishDate >= moment().format('X')) {
      publishLabel = 'Publish on'
    }
    publish = <abbr title={moment.unix(publishDate).format('LLLL')}>{publishDateRelative}</abbr>
  }

  return (
    <div className="entry-row mb-1">
      <div className="row">
        <div className="col-sm-3">
          <div className="entry-image">
            {image}
          </div>
        </div>
        <div className="col-sm-6">
          <h3>{title}
          </h3>
          <p>{summary}</p>
        </div>
        <div className="col-sm-3">
          <strong>Author:</strong>&nbsp;
          <a href={mailto}>{authorName}</a><br/>
          <strong>Created:</strong>&nbsp;
          <abbr title={createDate}>{createDateRelative}</abbr><br/>
          <strong>{publishLabel}</strong>&nbsp;{publish}
          <br/>
          <strong>Expires:</strong>&nbsp; {expire}
        </div>
      </div>
      <Options
        entryId={id}
        deleteStory={deleteStory}
        published={published}
        publishStory={publishStory}/>
    </div>
  )
}

EntryRow.propTypes = {
  entry: PropTypes.object,
  select: PropTypes.func,
  unselect: PropTypes.func,
  selected: PropTypes.bool,
  publishStory: PropTypes.func,
  deleteStory: PropTypes.func
}

export default EntryRow

const Options = ({entryId, deleteStory, published, publishStory,}) => {
  return (
    <div className="mt-1">
      <a className="admin edit mr-1" href={`./stories/Entry/${entryId}/edit`}>Edit</a>
      {published === '0'
        ? <a className="admin edit mr-1 pointer" onClick={publishStory}>Publish</a>
        : null}
      <a className="admin delete mr-1 pointer" onClick={deleteStory}>
        Delete</a>
    </div>
  )
}

Options.propTypes = {
  entryId: PropTypes.string,
  deleteStory: PropTypes.func,
  published: PropTypes.oneOfType([PropTypes.string, PropTypes.number,]),
  publishStory: PropTypes.func
}
