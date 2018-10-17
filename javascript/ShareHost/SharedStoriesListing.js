'use strict'
import React from 'react'
import PropTypes from 'prop-types'

const ShareStoriesListing = ({listing, approve, deny}) => {
  if (!listing || listing.length === 0) {
    return <p>No shared stories.</p>
  }

  const style = {
    height: '60px'
  }
  
  let rows = listing.map((value, key) => {
    return (
      <tr key={key}>
        <td>
          <button className="btn btn-success btn-sm mr-1" onClick={approve.bind(null, value.id)}>
            <i className="fas fa-check fa-fw"></i>
          </button>
          <button className="btn btn-danger btn-sm" onClick={deny.bind(null, value.id)}>
            <i className="fas fa-times fa-fw"></i>
          </button>
        </td>
        <td>
          <img src={value.thumbnail} style={style}/>
        </td>
        <td>
          <a href={value.siteUrl}>{value.siteName}</a>
        </td>
        <td>
          <a href={value.url}>{value.title}</a>
        </td>
        <td>{value.strippedSummary}</td>
      </tr>
    )
  })
  return (
    <div>
      <table className="table table-striped">
        <tbody>
          <tr>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>Site</th>
            <th>Title</th>
            <th>Summary</th>
          </tr>
          {rows}
        </tbody>
      </table>
    </div>
  )
}

ShareStoriesListing.propTypes = {
  listing: PropTypes.array,
  approve: PropTypes.func,
  deny: PropTypes.func
}

export default ShareStoriesListing
