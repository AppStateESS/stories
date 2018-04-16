'use strict'
import React from 'react'
import PropTypes from 'prop-types'

const MoveButton = ({dir, holdThumb, stopMove, moveThumb,cX, cY}) => {
  let x
  let y
  let fa
  let disabled = false
  switch (dir) {
    case 'up':
      x = 0
      y = -1
      fa = 'fa fa-arrow-up'
      disabled = cY == 0
      break
    case 'down':
      x = 0
      y = 1
      fa = 'fa fa-arrow-down'
      disabled = cY == 100
      break
    case 'left':
      x = -1
      y = 0
      fa = 'fa fa-arrow-left'
      disabled = cX == 0
      break
    case 'right':
      x = 1
      y = 0
      fa = 'fa fa-arrow-right'
      disabled = cX == 100
      break
  }

  return (
    <button
      disabled={disabled}
      onClick={moveThumb.bind(null, x, y, 2)}
      onMouseDown={holdThumb.bind(null, x, y, 5)}
      onMouseUp={stopMove}
      onMouseLeave={stopMove}
      className="btn btn-sm btn-secondary">
      <i className={fa}></i>
    </button>
  )
}

MoveButton.propTypes = {
  dir: PropTypes.string,
  holdThumb: PropTypes.func,
  stopMove: PropTypes.func,
  moveThumb: PropTypes.func,
  cX: PropTypes.oneOfType([PropTypes.number,PropTypes.string,]),
  cY: PropTypes.oneOfType([PropTypes.number,PropTypes.string,])
}

MoveButton.defaultTypes = {}

export default MoveButton
