'use strict'
import React from 'react'
import moment from 'moment'
import PropTypes from 'prop-types'

const GuestRow = ({
  accept,
  deny,
  email,
  siteName,
  submitDate,
  url
}) => {
  const submitFormatted = moment.unix(submitDate).format("MMM D YYYY, h:mm a")
  return (
    <tr>
      <td>
        <button className="btn btn-outline-success btn-sm mr-1" title="Accept submissions from this site" onClick={accept}>
          <i className="fas fa-check fa-fw"></i>
        </button>
        <button className="btn btn-outline-danger btn-sm" title="Deny submissions from this site" onClick={deny}>
          <i className="fas fa-times fa-fw"></i>
        </button>
      </td>
      <td >{siteName}<br /><pre>{url}</pre></td>
      <td>{email}</td>
      <td>{submitFormatted}</td>
    </tr>
  )
}

GuestRow.propTypes = {
  email: PropTypes.string,
  siteName: PropTypes.string,
  submitDate: PropTypes.string,
  url: PropTypes.string,
  accept: PropTypes.func,
  deny: PropTypes.func,
}

GuestRow.defaultProps = {}

export default GuestRow
