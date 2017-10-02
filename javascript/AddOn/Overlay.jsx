import React, {Component} from 'react'
import PropTypes from 'prop-types'
/* global $ */

export default class Overlay extends Component {
  constructor(props) {
    super(props)
    this.state = {}
    this.lighten = this.lighten.bind(this)
    this.normal = this.normal.bind(this)
    this.unlockBody = this.unlockBody.bind(this)
    this.close = this.close.bind(this)
  }

  componentDidMount() {
    this.lockBody()
  }

  lockBody() {
    $('body').css('overflow', 'hidden')
  }

  unlockBody() {
    $('body').css('overflow', 'inherit')
  }

  normal() {
    this.refs.closebutton.style.color = 'inherit'
  }

  lighten() {
    this.refs.closebutton.style.color = '#479AFF'
  }

  close() {
    this.unlockBody()
    this.props.close()
  }

  render() {
    let width = '80%'
    let height = '80%'
    let overflow = 'visible'

    if (this.props.width !== undefined && this.props.width !== null) {
      width = this.props.width
    }

    if (this.props.height !== undefined && this.props.height !== null) {
      height = this.props.height
    }

    if (this.props.overflow !== undefined && this.props.overflow !== null) {
      overflow = this.props.overflow
    }

    const backing = {
      width: '100%',
      position: 'fixed',
      top: '0px',
      padding: '10%',
      bottom: '0px',
      left: '0px',
      backgroundColor: 'rgba(0,0,0,0.8)',
      zIndex: '300'
    }

    const overlayStyle = {
      width: width,
      height: height,
      position: 'fixed',
      left: '50%',
      border: '1px solid white',
      borderRadius: '6px',
      backgroundColor: 'white',
      zIndex: '310',
      overflow: overflow,
      padding: '10px',
      transform: 'translate(-50%)',
    }

    const headerStyle = {
      marginBottom: '1em',
      height: '45px',
    }

    const titleStyle = {
      padding: '9px',
      fontSize: '18px',
      fontWeight: 'bold',
      overflow: 'hidden'
    }

    const closeButton = {
      padding: '5px',
      float: 'right',
    }

    const childrenStyle = {
      padding: '9px'
    }

    return (
      <div style={backing}>
        <div style={overlayStyle}>
          <div style={headerStyle}>
            <div
              ref="closebutton"
              style={closeButton}
              onMouseEnter={this.lighten}
              onMouseLeave={this.normal}>
              <i className="fa fa-2x fa-times pointer" onClick={this.close}></i>
            </div>
            <div style={titleStyle}>{this.props.title}</div>
          </div>
          <div style={childrenStyle}>
            {this.props.children}
          </div>
        </div>
      </div>
    )
  }
}

Overlay.propTypes = {
  children: PropTypes.oneOfType([PropTypes.string, PropTypes.element, PropTypes.array,]),
  close: PropTypes.func,
  title: PropTypes.string,
  width: PropTypes.string,
  height: PropTypes.string,
  overflow: PropTypes.string,
}
