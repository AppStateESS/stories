'use strict'
import React from 'react'
import PropTypes from 'prop-types'
import empty from './Empty'

const BigCheckbox = ({handle, checked, label}) => {

  const handleIt = () => {
    handle(empty(checked))
  }

  const mute = {
    color: '#666'
  }
  const point = {
    cursor: 'pointer',
    display: 'inline-block',
  }
  const labelText = {
    fontSize: '20px',
    display: 'inline-block',
    marginTop: '4px',
  }

  return (
    <div onClick={handleIt} style={point} className="big-checkbox">
      <div className="fa-stack fa-lg pull-left">
        <i className="far fa-square fa-stack-2x" style={mute}></i>
        {empty(checked)
          ? null
          : <i className="fa fa-check text-success fa-stack-2x"></i>}
      </div>&nbsp;
      <div
        style={labelText}
        className={!empty(checked)
        ? 'text-success'
        : 'text-muted'}>{label}</div>
    </div>
  )
}

BigCheckbox.propTypes = {
  label: PropTypes.string,
  checked: PropTypes.oneOfType([PropTypes.bool, PropTypes.string, PropTypes.number,]),
  handle: PropTypes.func.isRequired,
}

BigCheckbox.defaultProps = {
  checked: false
}

export default BigCheckbox
