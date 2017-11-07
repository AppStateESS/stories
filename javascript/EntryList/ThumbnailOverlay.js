'use strict'
import React from 'react'
import PropTypes from 'prop-types'
import {VelocityTransitionGroup} from 'velocity-react'
import Overlay from '../AddOn/Overlay'
import Dropzone from 'react-dropzone'
import BigCheckbox from '../AddOn/BigCheckbox'

/* global $ */

class ThumbnailOverlay extends React.Component {
  constructor(props) {
    super(props)
    this.file = null
    this.state = {
      photo: null,
      leadUpdate: false,
      thumbOnly: false,
    }
    this.saveThumbnail = this.saveThumbnail.bind(this)
    this.updateThumb = this.updateThumb.bind(this)
    this.updateImage = this.updateImage.bind(this)
    this.close = this.close.bind(this)
  }

  updateThumb(value) {
    this.setState({thumbOnly: value})
  }

  close() {
    this.setState({photo: null, thumbOnly: false,})
    this.props.close()
  }

  saveThumbnail() {
    if (this.file === null) {
      this.close()
      return
    }

    let formData = new FormData()
    formData.append('image', this.file)
    formData.append('entryId', this.props.entry.id)
    formData.append('thumbOnly', this.state.thumbOnly)
    $.ajax({
      url: './stories/EntryPhoto/update',
      data: formData,
      type: 'post',
      cache: false,
      dataType: 'json',
      processData: false,
      contentType: false,
      success: function (data) {
        const entry = this.props.entry
        entry.thumbnail = data.thumbnail
        this.props.updateEntry(entry)
        this.close()
      }.bind(this),
      error: function () {}.bind(this)
    })
  }

  updateImage(e) {
    const file = e[0]
    this.file = file
    this.props.updateImage(file.preview)
  }

  render() {
    const {entry} = this.props
    let photo = <EmptyPhoto/>
    if (entry && entry.thumbnail != '') {
      photo = <img
        src={this.props.entry.thumbnail}
      style={{maxWidth: '100%', maxHeight: '100%'}}/>
    }

    const closeButton = (
      <button className="btn btn-default btn-block" onClick={this.close}>Close</button>
    )
    const fadeIn = {
      animation: "fadeIn"
    }

    const fadeOut = {
      animation: "fadeOut"
    }

    const disabled = this.file == null
    return (
      <VelocityTransitionGroup enter={fadeIn} leave={fadeOut}>
        {
          this.props.thumbnailOverlay
            ? <Overlay close={this.close} width="500px" height="420px" title="Change image">
                <Dropzone
                  onDrop={this.updateImage}
                  className="dropzone text-center pointer">
                  {photo}
                </Dropzone>
                <div>
                  <BigCheckbox
                    label="Update thumbnail only"
                    checked={this.state.thumbOnly}
                    handle={this.updateThumb}/>
                </div>
                <div>
                  <button
                    className="btn btn-primary btn-block"
                    onClick={this.saveThumbnail}
                    disabled={disabled}>Save</button>
                </div>
                <div>{closeButton}</div>
              </Overlay>
            : null
        }
      </VelocityTransitionGroup>
    )
  }
}

ThumbnailOverlay.propTypes = {
  thumbnailOverlay: PropTypes.bool,
  updateEntry: PropTypes.func,
  updateImage: PropTypes.func,
  entry: PropTypes.object,
  close: PropTypes.func
}

ThumbnailOverlay.defaultTypes = {}

export default ThumbnailOverlay

const EmptyPhoto = () => {
  return (
    <div>
      <i className="fa fa-camera fa-4x"></i><br/>
      <div>Click to browse<br/>- or -<br/>drag image here</div>
    </div>
  )
}
