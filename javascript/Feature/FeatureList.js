'use strict'
import React from 'react'
import PropTypes from 'prop-types'

const FeatureList = (props) => {
  if (props.list === undefined || props.list === null) {
    return <div>No features found. Add a feature row to get started.</div>
  }

  let rows = props.list.map(function (value, key) {
    return (
      <tr key={key}>
        <td><button className="btn btn-primary" onClick={props.loadCurrentFeature.bind(null, key)}>Edit</button></td>
        <td>{value.id}</td>
        <td>{value.active}</td>
        <td>{value.title}</td>
        <td>{value.columns}</td>
      </tr>
    )
  })
  return (
    <div>
      <table className="table table-striped">
        <tbody>
          {rows}
        </tbody>
      </table>
    </div>
  )
}

FeatureList.propTypes = {
  list: PropTypes.array,
  loadCurrentFeature: PropTypes.func,
}

FeatureList.defaultTypes = {}

export default FeatureList
