'use strict'
import React from 'react'
import PropTypes from 'prop-types'
import empty from './Empty'

const BigCheckbox = ({handle, checked, label,}) => {

  const handleIt = () => {
    handle(empty(checked))
  }

  const checkBox = (
    <span className={parseInt(checked) == 1
        ? 'd-inline'
        : 'd-none'}>
      <i className="fas fa-2x fa-check-square mr-1 text-success"></i>
    </span>
  )
  const uncheckBox = (
    <span className={parseInt(checked) == 1
        ? 'd-none'
        : 'd-inline'}>
      <i className="far fa-2x fa-square mr-1"></i>
    </span>
  )

  return (
    <div onClick={handleIt} className="pointer d-flex align-items-center h-100">
      {checkBox}{uncheckBox}
      <span
        className={!empty(checked)
          ? 'text-success'
          : 'text-muted'}>{label}</span>
    </div>
  )
}

BigCheckbox.propTypes = {
  label: PropTypes.string,
  checked: PropTypes.oneOfType(
    [PropTypes.bool, PropTypes.string, PropTypes.number,]
  ),
  handle: PropTypes.func.isRequired
}

BigCheckbox.defaultProps = {
  checked: false
}

export default BigCheckbox
