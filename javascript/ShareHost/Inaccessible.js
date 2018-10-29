'use strict'
import React from 'react'
import PropTypes from 'prop-types'

const Inaccessible = ({listing, deleteInaccessible}) => {
  let rows = listing.map((value, key) => {
    return (
      <tr key={key}>
        <td><button className="btn btn-danger btn-sm" onClick={deleteInaccessible.bind(null, key)}><i className="fas fa-trash-alt"></i></button></td>
        <td>
          <a href={value.url}>{value.url}</a>
        </td>
        <td>{value.inaccessible}</td>
      </tr>
    )
  })
  return (
    <div>
      <p className="small">These are shared stories that no longer appear active. You may wish to remove them. They will be removed automatically at 50 attempts</p>
      <table className="table table-striped">
        <tbody>
          <tr>
            <th></th>
            <th>Link</th>
            <th>Attempts</th>
          </tr>
          {rows}
        </tbody>
      </table>
    </div>
  )
}

Inaccessible.propTypes = {
  listing: PropTypes.array,
  deleteInaccessible: PropTypes.func,
}

export default Inaccessible
