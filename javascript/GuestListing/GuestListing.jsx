'use strict'
import React, {Component} from 'react'
import PropTypes from 'prop-types'
import Waiting from '../AddOn/Waiting'
import './style.css'

/* global $ */

export default class GuestListing extends Component {
  constructor(props) {
    super(props)
    this.state = {
      listing: [],
      guest: {},
      loading: true
    }
  }

  componentDidMount() {
    this.load()
  }

  load() {
    $.ajax({
      url: './stories/Share/guestListing',
      data: {
        guestId: this.props.guestId
      },
      dataType: 'json',
      type: 'get',
      success: (data) => {
        this.setState({listing: data.listing, loading: false, guest: data.guest})
      },
      error: () => {
      }
    })
  }

  removeShare(key, e) {
    e.preventDefault()
    const share = this.state.listing[key]

    $.ajax({
      url: './stories/Share/' + share.id,
      dataType: 'json',
      type: 'delete',
      success: () => {
        this.load()
      },
      error: () => {
      }
    })
  }

  acceptShare(key, e) {
    e.preventDefault()
    const share = this.state.listing[key]
    $.ajax({
      url: `stories/Share/${share.id}/approve`,
      dataType: 'json',
      type: 'put',
      success: () => {
        this.load()
      },
      error: () => {}
    })
  }

  render() {
    let rows
    if (this.state.loading) {
      rows = <Waiting label="stories"/>
    } else if (!this.state.listing || this.state.listing.length === 0) {
      rows = <p>No stories submitted.</p>
    } else {

      rows = this.state.listing.map((value, key) => {
        let approve
        if (value.approved === false) {
          approve = <a
            className="badge badge-success text-white mr-2"
            onClick={this.acceptShare.bind(this, key)}
            href="./#">Accept share</a>
        }
        return (
          <div className="row" key={key}>
            <div className="col-sm-2">
              <a href={value.url}><img className="img-fluid" src={value.thumbnail}/></a>
            </div>
            <div className="col-sm-10">
              <h4>
                <a href={value.url}>{value.title}</a>
              </h4>
              <div>{value.strippedSummary}</div>
              <div>
                <small>Published {value.publishDateRelative}</small>
              </div>
              <div className="border-top mt-2 pt-2">
                {approve}
                <a
                  className="badge badge-danger text-white"
                  onClick={this.removeShare.bind(this, key)}
                  href="./#">Remove share</a>
              </div>
            </div>
          </div>
        )
      })
    }
    return (
      <div>
        <h3>Stories from&nbsp;
          <a href={this.state.guest.url}>{this.state.guest.siteName}</a>
        </h3>
        <div className="share-rows">
          {rows}
        </div>
      </div>
    )
  }
}

GuestListing.propTypes = {
  guestId: PropTypes.string,
  removeShare: PropTypes.func
}
