import React from 'react'
import PropTypes from 'prop-types'

const Message = (props) => {
  let icon = ''
  switch (props.type) {
    case 'danger':
      icon = 'fa fa-exclamation-triangle'
      break

    case 'success':
      icon = 'fa fa-thumbs-o-up'
      break

    case 'info':
      icon = 'fa fa-info-circle'
      break

    case 'warning':
      icon = 'fa fa-hand-paper-o'
      break
  }

  let messageType = 'alert alert-dismissible alert-' + props.type

  let closeButton
  if (props.onClose !== undefined) {
    closeButton = (
      <button
        type="button"
        onClick={props.onClose}
        className="close"
        data-dismiss="alert"
        aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    )
  }

  return (
    <div className={messageType} role="alert">
      {closeButton}
      <i className={icon}></i>&nbsp; {props.children}
    </div>
  )
}

Message.propTypes = {
  type: PropTypes.string,
  children: PropTypes.oneOfType([PropTypes.string, PropTypes.element,]),
  onClose: PropTypes.func,
}

Message.defaultProps = {
  type: 'info'
}

export default Message
