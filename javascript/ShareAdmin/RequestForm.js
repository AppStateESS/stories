'use strict'
import React, {Component} from 'react'
import PropTypes from 'prop-types'
import InputField from '@essappstate/canopy-react-inputfield'

/* global $ */

export default class RequestForm extends Component {
  constructor(props) {
    super(props)
    this.state = {
      message: null,
      hostName: '',
      hostUrl: '',
      guestRequestUrl: null,
      guestEmail: '',
      errorMessage: null
    }
    this.createHost = this.createHost.bind(this)
  }

  makeRequest() {
    const call = this.isDuplicateSiteCall()
    call.done((data) => {
      if (data.duplicate === true) {
        this.setState(
          {message: 'This host site url is already in use.', hostUrl: '', guestRequestUrl: ''}
        )
      } else if (!data.successfulContact) {
        this.setState(
          {message: 'Could not make contact with host site. Check the url.', hostUrl: '', guestRequestUrl: ''}
        )
      } else {
        if (this.isSameSite()) {
          this.setState(
            {message: 'You are not allowed to host your own site.', hostUrl: '', guestRequestUrl: ''}
          )
        } else {
          const strippedUrl = this.state.hostUrl.replace(/^(https?:)?\/\//, '').replace(
            /\/$/,
            ''
          )
          const guestRequestUrl = `http://${strippedUrl}/stories/Guest/request`
          this.setState({message: null, guestRequestUrl})
        }
      }
    })
  }

  isDuplicateSiteCall() {
    return $.getJSON('./stories/Host/test', {url: this.state.hostUrl})
  }

  updateUrl(e) {
    let hostUrl = e.target.value
    this.setState({hostUrl})
  }

  updateName(e) {
    let hostName = e.target.value
    this.setState({hostName})
  }

  updateEmail(e) {
    const guestEmail = e.target.value
    this.setState({guestEmail})
  }

  isSameSite() {
    const compare1 = this.state.hostUrl.replace(/^(https?:)?\/\/|\/$/g, '')
    const compare2 = this.props.url.replace(/^(https?:)?\/\/|\/$/g, '')
    return compare1 === compare2
  }

  createHost(e) {
    e.preventDefault()
    $.ajax({
      url: 'stories/Host/',
      data: {
        hostUrl: this.state.hostUrl,
        hostName: this.state.hostName
      },
      dataType: 'json',
      type: 'post',
      success: () => {
        $('#guest-request-form').submit()
      },
      error: () => {
        this.setState(
          {guestRequestUrl: '', message: 'An error occurred while saving the host.'}
        )
        e.preventDefault()
      }
    })
  }

  render() {
    let message
    if (this.state.message) {
      message = <div className="alert alert-danger mb-2">{this.state.message}</div>
    }
    if (this.state.guestRequestUrl) {
      return (
        <div>
          {message}
          <p>Clicking the button below will take you off this page and to the host&rsquo;s
            request page.</p>
          <p>If you receive an error, your site address or email were formatted incorrectly.</p>
          <form
            id="guest-request-form"
            method="get"
            onSubmit={this.createHost}
            action={this.state.guestRequestUrl}>
            <input type="hidden" name="siteName" value={this.props.siteName}/>
            <input type="hidden" name="url" value={this.props.url}/>
            <input type="hidden" name="email" value={this.state.guestEmail}/>
            <button type="submit" className="btn btn-primary btn-block">Request story sharing</button>
          </form>
        </div>
      )
    }
    return (
      <div>
        {message}
        <InputField
          name="hostName"
          label="Host name:"
          type="text"
          value={this.state.hostName}
          change={this.updateName.bind(this)}/>
        <InputField
          name="hostUrl"
          label="Host site url:"
          type="text"
          value={this.state.hostUrl}
          change={this.updateUrl.bind(this)}/>
        <InputField
          name="email"
          label="Your email address:"
          type="text"
          value={this.state.guestEmail}
          change={this.updateEmail.bind(this)}/>
        <hr/>
        <button className="btn btn-success" onClick={this.makeRequest.bind(this)}>Send request</button>
      </div>
    )
  }
}

RequestForm.propTypes = {
  siteName: PropTypes.string,
  url: PropTypes.string
}
