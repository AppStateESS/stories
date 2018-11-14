'use strict'
import React, {Component} from 'react'
import PropTypes from 'prop-types'
import Overlay from '@essappstate/canopy-react-overlay'
import Dropzone from 'react-dropzone'
import EmptyPhoto from '../AddOn/EmptyPhoto'
import '../AddOn/imageOverlay.css'

/* global $ */

export default class PictureOverlay extends Component {
  constructor(props) {
    super(props)
    this.file = null
    this.state = {
      photo: null
    }
    this.savePicture = this.savePicture.bind(this)
    this.updateImage = this.updateImage.bind(this)
    this.close = this.close.bind(this)
  }

  close() {
    this.setState({photo: null})
    this.props.close()
  }

  updateImage(e) {
    const file = e[0]
    this.file = file
    this.props.updateImage(file.preview)
  }

  savePicture() {
    if (this.file === null) {
      this.close()
      return
    }

    const {author} = this.props

    let formData = new FormData()
    formData.append('image', this.file)
    formData.append('authorId', this.props.author.id)
    $.ajax({
      url: './stories/Author/photo',
      data: formData,
      type: 'post',
      cache: false,
      dataType: 'json',
      processData: false,
      contentType: false,
      success: function (data) {
        author.pic = data.image[0].url
        this.props.updateAuthor(author)
        this.props.updateAuthorList()
        this.close()
      }.bind(this),
      error: function () {}.bind(this),
    })
  }

  render() {
    const {author} = this.props
    let photo = <EmptyPhoto/>
    if (author && author.pic != null) {
      photo = <img
        src={author.pic}
        style={{
          maxWidth: '100%',
          maxHeight: '100%'
        }}/>
    }

    const closeButton = (
      <button className="btn btn-outline-dark btn-block" onClick={this.close}>Close</button>
    )

    const disabled = this.file == null

    return (
      <Overlay
        show={this.props.show}
        close={this.close}
        fade={true}
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
            onClick={this.savePicture}
            disabled={disabled}>Save</button>
        </div>
        <div>{closeButton}</div>
      </Overlay>
    )
  }
}

PictureOverlay.propTypes = {
  author: PropTypes.object,
  close: PropTypes.func,
  show: PropTypes.bool,
  updateAuthor: PropTypes.func,
  updateImage: PropTypes.func,
  updateAuthorList: PropTypes.func,
}

PictureOverlay.defaultTypes = {
  show: false
}
