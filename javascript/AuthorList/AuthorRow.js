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
        <i className="fas fa-camera fa-2x text-muted"></i>
      </div>
      <span style={noPic}>No picture</span>
    </div>
  )
  if (pic != null) {
    picture = (<div className="circle-frame pointer"><img src={pic} onClick={props.thumbnail} /></div>)
  }
  return (
    <tr className="align-items-center">
      <td className="align-middle">
        <button className="btn btn-primary btn-sm" onClick={props.update}>
          <i className="fas fa-edit"></i>
        </button>
      </td>
      <td className="align-middle d-flex justify-content-center">{picture}</td>
      <td className="align-middle">{name}</td>
      <td className="align-middle"><a href={`mailto:${email}`}>{email}</a></td>
      <td className="align-middle">{lastLogged}</td>
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
