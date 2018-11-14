'use strict'
import React from 'react'
import PropTypes from 'prop-types'
import HostRow from './HostRow'

const Hosts = ({listing, deleteHost, setAuthKey}) => {
  if (listing.length === 0) {
    return <p>No guest requests in queue.</p>
  }
  let rows = listing.map((value, key) => {
    return <HostRow
      {...value}
      key={key}
      deleteHost={deleteHost.bind(null, key)}
      setAuthKey={setAuthKey.bind(null, key)}/>
  })
  return (
    <div>
      <table className="table table-striped">
        <tbody>
          <tr>
            <th>Action</th>
            <th>Site</th>
            <th>Url</th>
            <th>Authkey</th>
          </tr>
          {rows}
        </tbody>
      </table>
    </div>
  )
}

Hosts.propTypes = {
  listing: PropTypes.array,
  deleteHost: PropTypes.func,
  setAuthKey: PropTypes.func
}

export default Hosts
