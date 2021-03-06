'use strict'
import React from 'react'
import PropTypes from 'prop-types'
import moment from 'moment'

const CurrentGuestRow = ({id, siteName, url, authkey, email, acceptDate, deny, storyCount}) => {
  const acceptFormatted = moment.unix(acceptDate).format("MMM D YYYY")
  return (<tr>
    <td>
      <button className="btn btn-danger btn-sm mr-1" title="Remove guest and prevent submissions" onClick={deny}>
        <i className="fas fa-trash-alt fa-fw"></i>
      </button>
    </td>
    <td><a href={url}>{siteName}</a>&nbsp;<button type="button" className="authkey btn btn-secondary btn-sm" data-toggle="popover" data-content={authkey}><i className="fas fa-key"></i></button></td>
    <td><a href={`mailto:${email}`}>{email}</a></td>
    <td className="text-center"><a className="badge badge-pill badge-primary" href={`./stories/Share/guestListing/?guestId=${id}`}>{storyCount}</a></td>
    <td>{acceptFormatted}</td>
  </tr>)
}

CurrentGuestRow.propTypes = {
  id: PropTypes.string,
  siteName: PropTypes.string,
  storyCount: PropTypes.string,
  url: PropTypes.string,
  authkey: PropTypes.string,
  email: PropTypes.string,
  acceptDate: PropTypes.string,
  deny: PropTypes.func,
}

export default CurrentGuestRow
