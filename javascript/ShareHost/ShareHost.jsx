'use strict'
import React, {Component} from 'react'
import CurrentGuests from './CurrentGuests'
import GuestRequests from './GuestRequests'
import Hosts from './Hosts'
import Modal from '@essappstate/canopy-react-modal'
import RequestForm from './RequestForm'
import Navbar from '../AddOn/Navbar'
import AuthKeyForm from './AuthKeyForm'
import PropTypes from 'prop-types'

/* global $ */

export default class ShareHost extends Component {
  constructor(props) {
    super(props)
    this.state = {
      guestRequests: [],
      currentGuests: [],
      hosts: [],
      currentHost: null,
      currentHostKey: -1,
      loading: true
    }
    this.updateAuthKey = this.updateAuthKey.bind(this)
    this.saveAuthKey = this.saveAuthKey.bind(this)
  }

  componentDidMount() {
    this.load()
  }

  render() {
    if (this.state.loading) {
      return <Loading/>
    }

    let hostFormTitle = null
    if (this.state.currentHost) {
      hostFormTitle = `Authkey for: ${this.state.currentHost.siteName}`
    }

    const requestButton = <a
      key="1"
      className="nav-link pointer"
      data-toggle="modal"
      data-target="#reactModal">
      <i className="fas fa-share-alt"></i>&nbsp;Request sharing
    </a>

    const leftside = [requestButton]
    const authKeyForm = (
      <AuthKeyForm
        host={this.state.currentHost}
        update={this.updateAuthKey}
        save={this.saveAuthKey}/>
    )
    const requestForm = <RequestForm {...this.props}/>
    return (
      <div>
        <Navbar header={'Stories Sharing'} leftSide={leftside}/>
        <Modal body={requestForm}/>
        <Modal body={authKeyForm} modalId="authKeyForm" header={hostFormTitle}/>
        <hr/>
        <h3>Hosts</h3>
        <Hosts
          listing={this.state.hosts}
          deleteHost={this.deleteHost.bind(this)}
          setAuthKey={this.setAuthKey.bind(this)}/>
        <hr/>
        <h3>Guest Requests</h3>
        <GuestRequests
          listing={this.state.guestRequests}
          acceptRequest={this.acceptRequest.bind(this)}
          denyRequest={this.denyRequest.bind(this)}/>
        <hr/>
        <h3>Current Guests</h3>
        <CurrentGuests
          listing={this.state.currentGuests}
          denyGuest={this.denyGuest.bind(this)}/>
      </div>
    )
  }

  /*----------------------------*/

  acceptRequest(key) {
    $.ajax({
      url: `./stories/Guest/${this.state.guestRequests[key].id}/accept`,
      dataType: 'json',
      type: 'put',
      success: () => {
        this.load()
      },
      error: () => {}
    })
  }

  deleteHost(key) {
    console.log('delete host at key=', key)
  }

  denyGuest(key) {
    $.ajax({
      url: `./stories/Guest/${this.state.currentGuests[key].id}/denyGuest`,
      dataType: 'json',
      type: 'put',
      success: () => {
        this.load()
      },
      error: () => {}
    })
  }
  denyRequest(key) {
    $.ajax({
      url: `./stories/Guest/${this.state.guestRequests[key].id}/denyGuest`,
      dataType: 'json',
      type: 'put',
      success: () => {
        this.load()
      },
      error: () => {}
    })
  }

  load() {
    $.ajax({
      url: './stories/Host',
      dataType: 'json',
      type: 'get',
      success: (data) => {
        this.setState(
          {loading: false, guestRequests: data.guestRequests, currentGuests: data.currentGuests, hosts: data.hosts}
        )
      },
      error: () => {}
    })
  }

  saveAuthKey() {
    $.ajax({
      url: 'stories/Host/' + this.state.currentHost.id,
      data: {
        authkey: this.state.currentHost.authkey
      },
      dataType: 'json',
      type: 'put',
      success: () => {
        const hosts = this.state.hosts
        hosts[this.state.currentHostKey] = this.state.currentHost
        this.setState({currentHostKey: -1, currentHost: null, hosts})
      },
      error: () => {}
    })
    $('#authKeyForm').modal('hide')
  }

  setAuthKey(key) {
    this.setState(
      {currentHost: Object.assign({}, this.state.hosts[key]), currentHostKey: key}
    )
    $('#authKeyForm').modal('show')
  }

  updateAuthKey(e) {
    const currentHost = this.state.currentHost
    currentHost.authkey = e.target.value
    this.setState({currentHost})
  }

}

ShareHost.propTypes = {
  siteName: PropTypes.string,
  url: PropTypes.string
}

ShareHost.defaultProps = {}

const Loading = () => {
  return (
    <div className="text-center">
      <span className="lead">
        <i className="fas fa-spinner fa-pulse"></i>&nbsp;Loading</span>
    </div>
  )
}
