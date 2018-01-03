'use strict'
import React, {Component} from 'react'
import PropTypes from 'prop-types'
import Select from 'react-select'
import 'react-select/dist/react-select.css'

/* global $, entry */

export default class AuthorBar extends Component {
  constructor(props) {
    super(props)
    this.state = {
      authorId: 0,
      authorList: null
    }
    this.load = this.load.bind(this)
    this.changeAuthor = this.changeAuthor.bind(this)

  }

  componentDidMount() {
    this.load()
    this.setState({authorId : entry.authorId})
  }

  changeAuthor(value) {
    this.setState({authorId: value.value})
    $.ajax({
      url: 'stories/Entry/' + entry.id,
      data: {param: 'authorId', value: value.value},
      dataType: 'json',
      type: 'patch',
      success: function(){}.bind(this),
      error: function(){}.bind(this)
    })
  }

  load() {
    $.ajax({
      url: 'stories/Author/select',
      dataType: 'json',
      type: 'get',
      success: function (data) {
        this.setState({authorList: data.listing})
      }.bind(this),
      error: function () {}.bind(this),
    })
  }

  render() {
    return (
      <div>
          <Select
            noResultsText="No authors found"
            clearable={false}
            aria-label="Story author"
            name="author"
            styles={{
              width: '100px'
            }}
            value={this.state.authorId}
            onChange={this.changeAuthor}
            options={this.state.authorList}/>
      </div>
    )
  }
}

AuthorBar.propTypes = {}
