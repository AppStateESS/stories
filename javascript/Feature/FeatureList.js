'use strict'
import React from 'react'
import PropTypes from 'prop-types'
import ButtonGroup from '@essappstate/canopy-react-buttongroup'

const FeatureList = (props) => {
  if (props.list === undefined || props.list === null) {
    return (
      <div>No features found.
        <button className="btn btn-link" onClick={props.add}>Add a feature set to get started.</button>
      </div>
    )
  }

  const formatTopBottom = props.srcHttp + 'mod/stories/img/top-bottom.png'
  const formatLandscape = props.srcHttp + 'mod/stories/img/landscape.png'
  const formatLeftRight = props.srcHttp + 'mod/stories/img/left-right.png'

  const iconStyle = {
    height: '30px'
  }

  const buttons = [
    {
      value: '1',
      label: 'Yes',
      color: 'success'
    }, {
      value: '0',
      label: 'No',
      color: 'danger'
    }
  ]

  let rows = props.list.map(function (value, key) {
    let formatIcon
    switch (value.format) {
      case 'landscape':
        formatIcon = formatLandscape
        break
      case 'topbottom':
        formatIcon = formatTopBottom
        break
      case 'leftright':
        formatIcon = formatLeftRight
        break
    }
    return (
      <tr key={key}>
        <td>
          <button
            className="btn btn-primary btn-sm"
            onClick={props.loadCurrentFeature.bind(null, key)}>
            <i className="fa fa-edit"></i>
          </button>
          <button
            className="btn btn-danger btn-sm"
            onClick={props.deleteFeature.bind(null, key)}>
            <i className="far fa-trash-alt"></i>
          </button>
        </td>
        <td>{
            value.title
              ? value.title
              : <em className="text-muted">Untitled</em>
          }</td>
        <td>{value.storyCount}</td>
        <td><img style={iconStyle} src={formatIcon}/></td>
        <td><ButtonGroup
          buttons={buttons}
          handle={props.updateActive.bind(null, key)}
          match={value.active}/></td>
      </tr>
    )
  })
  return (
    <div>
      <table className="table table-striped features">
        <tbody>
          <tr>
            <th>&nbsp;</th>
            <th>Title</th>
            <th>Stories</th>
            <th>Format</th>
            <th>Active</th>
          </tr>
          {rows}
        </tbody>
      </table>
    </div>
  )
}

FeatureList.propTypes = {
  list: PropTypes.array,
  loadCurrentFeature: PropTypes.func,
  updateActive: PropTypes.func,
  deleteFeature: PropTypes.func,
  srcHttp: PropTypes.string,
  add: PropTypes.func
}

FeatureList.defaultTypes = {}

export default FeatureList
