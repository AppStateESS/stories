'use strict'
import React from 'react'
import PropTypes from 'prop-types'
import GuestRow from './GuestRow'

const GuestRequests = ({listing, acceptRequest, deny}) => {
  if (listing.length === 0) {
    return <p>No guest requests in queue.</p>
  }
  let rows = listing.map((value, key) => {
    return <GuestRow
      {...value}
      key={key}
      accept={acceptRequest.bind(null, key)}
      deny={deny.bind(null, key)}/>
  })
  return (
    <div>
      <table className="table table-striped">
        <tbody>
          <tr>
            <th>Action</th>
            <th>Site</th>
            <th>Contact</th>
            <th>Submitted</th>
          </tr>
          {rows}
        </tbody>
      </table>
    </div>
  )
}

GuestRequests.propTypes = {
  listing: PropTypes.array,
  acceptRequest: PropTypes.func,
  deny: PropTypes.func
}

export default GuestRequests
