'use strict'
import React, {Component} from 'react'
import PropTypes from 'prop-types'

export default class AuthorForm extends Component {
  constructor(props) {
    super(props)
    this.state = {
      authorPick: -1
    }
  }

  handleAuthorPick(e) {
    this.setState({authorPick: e.target.value})
  }
  
  createAuthor() {
    console.log('createAuthor')
    this.props.create(this.state.authorPick)
    this.setState({authorPick: -1})
  }

  render() {
    const {
      author,
      unAuthored,
      updateName,
      updateEmail,
      update,
    } = this.props
    let unauthored
    let name
    let email
    let button

    if (parseInt(author.id) === 0) {
      let options = unAuthored.map((value, key) => {
        return (
          <option key={key} value={value.id}>{value.display_name}&nbsp;({value.username})</option>
        )
      })
      
      unauthored = (
        <div>
          <label>User</label>
          <select
            className="form-control"
            onChange={(e)=>this.handleAuthorPick(e)} value={this.state.authorPick}>
            <option value="-1">Pick user below</option>
            {options}
          </select>
          <span className="small text-muted"><em>Authors must have Stories permissions.</em></span>
        </div>
      )

      button = (
        <button className="btn btn-primary btn-block mt-2 mb-1" onClick={() => {this.createAuthor()}}>Add new author</button>
      )
    } else {
      name = (
        <div className="form-group">
          <label>Name:</label>
          <input
            type="text"
            name="name"
            value={author.name}
            onChange={updateName}
            className="form-control"/>
        </div>
      )

      email = (
        <div className="form-group">
          <label>Email:</label>
          <input
            type="text"
            name="email"
            value={author.email}
            onChange={updateEmail}
            className="form-control"/>
        </div>
      )

      button = <button className="btn btn-primary btn-block mt-2 mb-1" onClick={update}>Save</button>
    }

    return (
      <div>
        {unauthored}
        {name}
        {email}
        {button}
      </div>
    )
  }
}

AuthorForm.propTypes = {
  author: PropTypes.object,
  unAuthored: PropTypes.array,
  updateName: PropTypes.func,
  updateEmail: PropTypes.func,
  update: PropTypes.func,
  create: PropTypes.func
}

AuthorForm.defaultProps = {}
