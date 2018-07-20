'use strict'
import React, {Component} from 'react'
import PropTypes from 'prop-types'
import Overlay from 'canopy-react-overlay'
import AuthorForm from './AuthorForm'

/* global $ */

export default class AuthorOverlay extends Component {
  constructor(props) {
    super(props)
    this.updateName = this.updateName.bind(this)
    this.updateEmail = this.updateEmail.bind(this)
    this.saveAuthor = this.saveAuthor.bind(this)
    this.createAuthor = this.createAuthor.bind(this)
  }
  
  createAuthor(userId) {
    if (parseInt(userId) === -1) {
      return
    }
    $.ajax({
      url: 'stories/Author/create',
      data: {userId},
      dataType: 'json',
      type: 'post',
      success: ()=>{
        this.props.reload()
        this.props.close()
      },
      error: ()=>{}
    })
  }

  saveAuthor() {
    const {author} = this.props
    $.ajax({
      url: './stories/Author/' + author.id,
      data: {
        name: author.name,
        email: author.email,
      },
      dataType: 'json',
      type: 'put',
      success: function () {
        this.props.updateAuthorList()
        this.props.close()
      }.bind(this),
      error: function () {}.bind(this),
    })
  }

  updateName(e) {
    const {author} = this.props
    author.name = e.target.value
    this.props.updateAuthor(author)
  }

  updateEmail(e) {
    const {author} = this.props
    author.email = e.target.value
    this.props.updateAuthor(author)
  }

  render() {
    let title = 'Update author'
    if (parseInt(this.props.author.id) === 0) {
      title = 'Create author'
    }
    return (
      <Overlay
        show={this.props.show}
        fade={true}
        close={this.props.close}
        width="400px"
        height="350px"
        title={title}>
        <AuthorForm {...this.props} updateName={this.updateName} updateEmail={this.updateEmail} update={this.saveAuthor} create={this.createAuthor}/>
        <button className="btn btn-outline-dark btn-block" onClick={this.props.close}>Close</button>
      </Overlay>
    )
  }
}

AuthorOverlay.propTypes = {
  show: PropTypes.bool,
  close: PropTypes.func,
  updateAuthor: PropTypes.func,
  author: PropTypes.object,
  updateAuthorList: PropTypes.func,
  reload: PropTypes.func,
  unAuthored: PropTypes.array,
}
