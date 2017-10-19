'use strict'
import React from 'react'
import PropTypes from 'prop-types'
import moment from 'moment'

const EntryRow = (props) => {
  const noImage = () => {
    return (
      <div className="no-image">
        <div>
          <i className="fa fa-camera fa-5x"></i><br/>No image</div>
      </div>
    )
  }

  const {
    entry,
    deleteStory,
    publishStory,
    showTags,
    sortByTag,
  } = props

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
    strippedSummary,
    thumbnail,
    title,
    tags
  } = entry

  const mailto = 'mailto:' + authorEmail

  let image = noImage()
  if (thumbnail.length > 0) {
    image = <img className="img-responsive" src={thumbnail}/>
  }

  let expire = expirationDate

  if (!expirationDate) {
    expire = 'Never'
  }

  let publishLabel
  let publishInfo
  if (published == 0) {
    publishInfo = 'Unpublished'
  } else {
    if (publishDate >= moment().format('X')) {
      publishLabel = 'Publish on'
    } else {
      publishLabel = 'Published on'
    }
    publishInfo = (
      <div>
        {publishLabel}&nbsp;
        <abbr title={moment.unix(publishDate).format('LLLL')}>{publishDateRelative}</abbr>
      </div>
    )
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
          <div className="summary">{strippedSummary}</div>
        </div>
        <div className="col-sm-3">
          <strong>Author:</strong>&nbsp;
          <a href={mailto}>{authorName}</a><br/>
          <strong>Created:</strong>&nbsp;
          <abbr title={createDate}>{createDateRelative}</abbr><br/> {publishInfo}
          <br/>
          <strong>Expires:</strong>&nbsp; {expire}
        </div>
      </div>
      <hr/>
      <div className="row mt-1">
        <div className="col-sm-4">
          <Options
            entryId={id}
            deleteStory={deleteStory}
            isPublished={published}
            publishStory={publishStory}/>
        </div>
        <div className="col-sm-8">
          <TagList tags={tags} showTags={showTags} sortByTag={sortByTag}/>
        </div>
      </div>
    </div>
  )
}

EntryRow.propTypes = {
  entry: PropTypes.object,
  select: PropTypes.func,
  unselect: PropTypes.func,
  selected: PropTypes.bool,
  publishStory: PropTypes.func,
  deleteStory: PropTypes.func,
  sortByTag: PropTypes.func,
  showTags: PropTypes.func
}

export default EntryRow

const Options = ({entryId, deleteStory, isPublished, publishStory,}) => {
  return (
    <div>
      <a
        className="btn btn-sm btn-default mr-1"
        href={`./stories/Entry/${entryId}/edit`}>Edit</a>
      {isPublished === '0'
        ? <a className="btn btn-sm btn-default mr-1" onClick={publishStory}>Publish</a>
        : null}
      <a className="btn btn-sm btn-default mr-1" onClick={deleteStory}>
        Delete</a>
    </div>
  )
}

Options.propTypes = {
  entryId: PropTypes.string,
  deleteStory: PropTypes.func,
  isPublished: PropTypes.oneOfType([PropTypes.string, PropTypes.number,]),
  publishStory: PropTypes.func
}

const TagList = ({tags, showTags, sortByTag,}) => {
  let tagList
  const tagButton = <button className="btn btn-primary mr-1 btn-sm" onClick={showTags}>
    <i className="fa fa-tags"></i>&nbsp;Tags</button>
  if (tags[0] !== undefined) {
    tagList = tags.map(function (value, key) {
      return <button
        className="btn btn-sm mr-1"
        key={key}
        onClick={sortByTag.bind(null, value.value)}>{value.label}</button>
    })
  }
  return <div>
    <strong>{tagButton}
    </strong>
    {tagList}</div>
}

TagList.propTypes = {
  tags: PropTypes.array,
  showTags: PropTypes.func,
  sortByTag: PropTypes.func
}
