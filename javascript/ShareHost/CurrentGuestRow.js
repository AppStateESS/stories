'use strict'
import React from 'react'
import PropTypes from 'prop-types'
import moment from 'moment'

const CurrentGuestRow = ({siteName, url, authkey, email, acceptDate, deny}) => {
  const acceptFormatted = moment.unix(acceptDate).format("MMM D YYYY, h:mm a")
  return (<tr>
    <td>
      <button className="btn btn-outline-danger btn-sm mr-1" title="Deny submissions" onClick={deny}>
        <i className="fas fa-times fa-fw"></i>
      </button>
    </td>
    <td><a href={url}>{siteName}</a></td>
    <td><a href={`mailto:${email}`}>{email}</a></td>
    <td>{acceptFormatted}</td>
  </tr>)
}

CurrentGuestRow.propTypes = {
  siteName: PropTypes.string,
  url: PropTypes.string,
  authkey: PropTypes.string,
  email: PropTypes.string,
  acceptDate: PropTypes.string,
  deny: PropTypes.func,
}

export default CurrentGuestRow
