'use strict'
import React from 'react'
import PropTypes from 'prop-types'
import moment from 'moment'
import Options from './Options.js'

const EntryRow = (props) => {
  const noImage = () => {
    return (
      <div className="no-image">
        <div>
          <i className="fa fa-camera fa-5x"></i><br/>No image</div>
      </div>
    )
  }

  const {entry, deleteStory, publishStory, showTags, sortByTag} = props

  const {
    authorEmail,
    authorName,
    createDate,
    createDateRelative,
    publishDate,
    publishDateRelative,
    published,
    id,
    strippedSummary,
    thumbnail,
    title,
    tags,
    urlTitle
  } = entry

  const publishNowPrompt = () => {
    return (
      <div className="alert alert-warning">Currently unpublished but you may&nbsp;
        <a
          href="#"
          className="alert-link"
          onClick={(e) => {
            e.preventDefault()
            publishStory()
          }}>publish it now.</a>
      </div>
    )
  }

  const mailto = 'mailto:' + authorEmail

  let image = noImage()
  if (thumbnail.length > 0) {
    image = <img className="img-fluid" src={thumbnail}/>
  }

  let publishLabel
  let publishInfo
  if (published == 0) {
    publishInfo = <span className="badge badge-warning">
      <abbr title="Stories without content will remain unpublished">Unpublished</abbr>
    </span>
  } else {
    if (publishDate >= moment().format('X')) {
      publishLabel = <strong>Publish on:</strong>
    } else {
      publishLabel = <strong>Published</strong>
    }
    publishInfo = (
      <div>
        {publishLabel}&nbsp;
        <abbr title={moment.unix(publishDate).format('LLLL')}>{publishDateRelative}</abbr>
      </div>
    )
  }

  let titleLink
  let allowPublish = true
  if (title) {
    titleLink = (
      <a className="entry-title" href={urlTitle}>
        <h3>{title}</h3>
      </a>
    )
  } else {
    allowPublish = false
    titleLink = (<h3>
      <em>Untitled</em>
    </h3>)
  }
  let options
  let rowClass = 'entry-row mb-1 active'
  options = (
    <div className="row mt-2 options">
      <div className="col-sm-4">
        <Options
          entryId={id}
          allowPublish={allowPublish}
          published={published}
          deleteStory={deleteStory}
          publishStory={publishStory}/>
      </div>
      <div className="col-sm-8">
        <TagList tags={tags} showTags={showTags} sortByTag={sortByTag}/>
      </div>
    </div>
  )
  return (
    <div className={rowClass}>
      {
        published === '0'
          ? publishNowPrompt()
          : null
      }
      <div className="row">
        <div className="col-sm-2">
          <div className="entry-image" onClick={props.thumbnailForm}>
            {image}
          </div>
        </div>
        <div className="col-sm-6">
          {titleLink}
          <div className="summary">{strippedSummary}</div>
        </div>
        <div className="col-sm-4 story-data">
          <div>
            <strong>Author:</strong>&nbsp;<a href={mailto}>{authorName}</a>
          </div>
          <div>
            <strong>Created:</strong>&nbsp;<abbr title={createDate}>{createDateRelative}</abbr>
          </div>
          <div>{publishInfo}</div>
        </div>
      </div>
      {options}
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
  thumbnailForm: PropTypes.func,
  setCurrentEntry: PropTypes.func,
  showTags: PropTypes.func
}

export default EntryRow

const TagList = ({tags, showTags, sortByTag}) => {
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
