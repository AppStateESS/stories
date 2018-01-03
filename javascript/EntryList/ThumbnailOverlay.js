'use strict'
import React from 'react'
import PropTypes from 'prop-types'
import {VelocityTransitionGroup} from 'velocity-react'
import Overlay from '../AddOn/Overlay'
import Dropzone from 'react-dropzone'
import EmptyPhoto from '../AddOn/EmptyPhoto'
import '../AddOn/imageOverlay.css'

/* global $ */

class ThumbnailOverlay extends React.Component {
  constructor(props) {
    super(props)
    this.file = null
    this.state = {
      leadUpdate: false,
      preview: null,
    }
    this.saveThumbnail = this.saveThumbnail.bind(this)
    this.updateImage = this.updateImage.bind(this)
    this.close = this.close.bind(this)
  }

  close() {
    this.setState({photo: null, preview: null,})
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
    this.setState({preview: file.preview})
  }

  render() {
    let photo = <EmptyPhoto/>
    let src

    if (this.state.preview && this.state.preview != '') {
      src = this.state.preview
    } else if (this.props.entry && this.props.entry.thumbnail) {
      src = this.props.entry.thumbnail
    }

    if (src) {
      photo = <img
        src={src}
        style={{
          maxWidth: '100%',
          maxHeight: '100%'
        }}/>
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
            ? <Overlay
                close={this.close}
                width="500px"
                height="420px"
                title="Change thumbnail">
                <Dropzone onDrop={this.updateImage} className="dropzone text-center pointer">
                  {photo}
                </Dropzone>
                <div className="text-muted mb-1">
                  <small>
                    <strong>Note:</strong>&nbsp; changing images in story may change thumbnail.</small>
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
  entry: PropTypes.object,
  close: PropTypes.func,
  saveThumbnail: PropTypes.func,
}

ThumbnailOverlay.defaultTypes = {}

export default ThumbnailOverlay
