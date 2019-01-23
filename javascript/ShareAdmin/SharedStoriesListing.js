'use strict'
import React from 'react'
import PropTypes from 'prop-types'
import './style.css'

const ShareStoriesListing = ({listing, approve, deny}) => {
  if (!listing || listing.length === 0) {
    return <p>No shared stories.</p>
  }

  let rows = listing.map((value, key) => {
    const denyLink = (
      <a className="d-block badge badge-danger text-white pointer" onClick={deny.bind(null, value.id)}>
        Deny story
      </a>
    )
    let approveListLink
    let approveNoListLink
    if (value.error == undefined) {
      approveListLink = (
        <a
          className="d-block badge badge-success text-light mb-1 pointer"
          onClick={approve.bind(null, value.id, 1)}>
          Add to list
        </a>
      )
      approveNoListLink = (
        <a
          className="d-block badge badge-success text-light mb-1 pointer"
          onClick={approve.bind(null, value.id, 0)}>
          Add, do not list
        </a>
      )
      return (
        <tr key={key}>
          <td className="w-30">
            <ul className="list-unstyled">
              <li>{approveListLink}</li>
              <li>{approveNoListLink}</li>
              <li>{denyLink}</li>
            </ul>
          </td>
          <td>
            <img className="share-thumbnail" src={value.thumbnail}/>
          </td>
          <td>
            <a href={value.siteUrl}>{value.siteName}</a>
          </td>
          <td>
            <a href={value.url}>{value.title}</a>
          </td>
          <td>
            <abbr className="summary" title={value.strippedSummary}>{value.strippedSummary.substr(0, 50)}</abbr>
          </td>
        </tr>
      )
    } else {
      return (
        <tr key={key}>
          <td>{denyLink}</td>
          <td></td>
          <td>
            <a href={value.siteUrl}>{value.siteName}</a>
          </td>
          <td colSpan="2">Failure to communicate with guest site. Suggest denying share.<br/>
            <a href={value.url}>{value.url}</a>
          </td>
        </tr>
      )
    }

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
