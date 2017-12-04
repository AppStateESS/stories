'use strict'
import React from 'react'
import PropTypes from 'prop-types'

const AuthorRow = (props) => {
  const {name, email, last_logged, pic,} = props.author
  const date = new Date(last_logged * 1000)
  const months = [
    'Jan',
    'Feb',
    'Mar',
    'Apr',
    'May',
    'Jun',
    'Jul',
    'Aug',
    'Sep',
    'Oct',
    'Nov',
    'Dec',
  ]
  const lastLogged = `${months[date.getMonth()]}. ${date.getDate()}, ${date.getFullYear()}`
  const noPic = {
    fontSize: '12px',
    lineHeight: '0px',
    fontFamily: 'sans',
    position: 'relative',
    top: '-10px'
  }
  let picture = (
    <div className="text-center" onClick={props.thumbnail}>
      <div>
        <i className="fa fa-camera fa-2x text-muted"></i>
      </div>
      <span style={noPic}>No picture</span>
    </div>
  )
  if (pic != null) {
    picture = (<div className="circle-frame"><img src={pic} onClick={props.thumbnail} /></div>)
  }
  return (
    <tr>
      <td>
        <button className="btn btn-primary btn-sm" onClick={props.update}>
          <i className="fa fa-edit"></i>
        </button>
      </td>
      <td>{picture}</td>
      <td>{name}</td>
      <td>{email}</td>
      <td>{lastLogged}</td>
    </tr>
  )
}

AuthorRow.propTypes = {
  author: PropTypes.object,
  update: PropTypes.func,
  thumbnail: PropTypes.func,
}

AuthorRow.defaultTypes = {}

export default AuthorRow
