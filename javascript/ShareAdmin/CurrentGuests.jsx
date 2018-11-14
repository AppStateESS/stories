'use strict'
import React from 'react'
import PropTypes from 'prop-types'
import CurrentGuestRow from './CurrentGuestRow'

const CurrentGuests = ({listing, denyGuest}) => {
  if (listing.length === 0) {
    return <p>No current guests.</p>
  }
  let rows = listing.map((value, key) => {
    return <CurrentGuestRow {...value} key={key} deny={denyGuest.bind(null, key)}/>
  })
  return (
    <div>
      <table className="table table-striped">
        <tbody>
          <tr>
            <th>Action</th>
            <th>Shares</th>
            <th>Site</th>
            <th>Contact</th>
            <th>Accepted</th>
          </tr>
          {rows}
        </tbody>
      </table>
    </div>
  )
}

CurrentGuests.propTypes = {
  listing: PropTypes.array,
  denyGuest: PropTypes.func,
}

export default CurrentGuests
