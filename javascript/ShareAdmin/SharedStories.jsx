'use strict'
import React, {Component} from 'react'
import SharedStoriesListing from './SharedStoriesListing'

/* global $ */

export default class SharedStories extends Component {
  constructor(props) {
    super(props)
    this.state = {
      listing: [],
      loading: true
    }
    this.approve = this.approve.bind(this)
    this.deny = this.deny.bind(this)
  }

  componentDidMount() {
    this.load()
  }

  render() {
    if (this.state.loading) {
      return <p>Loading ...</p>
    }

    return (
      <div>
        <SharedStoriesListing
          listing={this.state.listing}
          approve={this.approve}
          deny={this.deny}/>
      </div>
    )
  }
  // --------------------

  load() {
    $.ajax({
      url: './stories/Share/listUnapproved/',
      dataType: 'json',
      type: 'get',
      success: (data) => {
        this.setState({loading: false, listing: data.listing})
      },
      error: () => {}
    })
  }
  
  approve(id, list) {
    $.ajax({
      url: './stories/Share/' + id + '/approve',
      data: {list},
      dataType: 'json',
      type: 'put',
      success: ()=>{
        this.load()
      },
      error: ()=>{}
    })
  }
  
  deny(id) {
    $.ajax({
      url: './stories/Share/' + id + '/deny',
      dataType: 'json',
      type: 'put',
      success: ()=>{
        this.load()
      },
      error: ()=>{}
    })
  }
}
