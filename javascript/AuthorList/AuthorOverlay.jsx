'use strict'
import React, {Component} from 'react'
import PropTypes from 'prop-types'
import {VelocityTransitionGroup} from 'velocity-react'
import Overlay from '../AddOn/Overlay'

/* global $ */

export default class AuthorOverlay extends Component {
  constructor(props) {
    super(props)
    this.state = {author : this.props.author}
    this.updateName = this.updateName.bind(this)
    this.updateEmail = this.updateEmail.bind(this)
    this.saveAuthor = this.saveAuthor.bind(this)
  }

  saveAuthor() {
    const {author} = this.props
    $.ajax({
      url: './stories/Author/' + author.id,
      data: {name: author.name, email: author.email},
      dataType: 'json',
      type: 'put',
      success: function(){
        this.props.close()
      }.bind(this),
      error: function(){}.bind(this)
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
    const fadeIn = {
      animation: "fadeIn"
    }
    const fadeOut = {
      animation: "fadeOut"
    }

    const {author} = this.props
    return (
      <VelocityTransitionGroup enter={fadeIn} leave={fadeOut}>
        {
          this.props.show
            ? <Overlay
                close={this.props.close}
                width="400px"
                height="340px"
                title="Update author">
                <label>Name:</label>
                <input type="text" name="name" value={author.name}
                  onChange={this.updateName} className="form-control"/>
                <label>Email:</label>
                <input type="text" name="email" value={author.email}
                  onChange={this.updateEmail} className="form-control"/>
                <button className="btn btn-primary btn-block mt-1" onClick={this.saveAuthor}>Save</button>
                <button className="btn btn-default btn-block" onClick={this.props.close}>Close</button>
              </Overlay>
            : null
        }</VelocityTransitionGroup>
    )
  }
}

AuthorOverlay.propTypes = {
  show: PropTypes.bool,
  close: PropTypes.func,
  updateAuthor: PropTypes.func,
  author: PropTypes.object
}
