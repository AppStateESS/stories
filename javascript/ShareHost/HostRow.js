'use strict'
import React from 'react'
import PropTypes from 'prop-types'

const HostRow = ({siteName, url, authkey, deleteHost, setAuthKey}) => {
  const authkeyStr = authkey.length == 0
    ? <em>Not set</em>
    : authkey
  return (
    <tr>
      <td>
        <button className="btn btn-danger btn-sm mr-1" onClick={deleteHost} title="Delete host">
          <i className="fas fa-trash-alt"></i>
        </button>
        <button className="btn btn-success btn-sm" onClick={setAuthKey} title="Set authkey">
          <i className="fas fa-key"></i>
        </button>
      </td>
      <td>{siteName}</td>
      <td>{url}</td>
      <td>{authkeyStr}</td>
    </tr>
  )
}

HostRow.propTypes = {
  siteName: PropTypes.string,
  url: PropTypes.string,
  authkey: PropTypes.string,
  deleteHost: PropTypes.func,
  setAuthKey: PropTypes.func,
}

export default HostRow
