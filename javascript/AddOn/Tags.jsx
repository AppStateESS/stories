'use strict'
import React from 'react'
import PropTypes from 'prop-types'
import Select from 'react-select'

export default class Tags extends React.Component {
  constructor(props) {
    super(props)
    this.newOptionClick = this.newOptionClick.bind(this)
  }

  newOptionClick(value) {
    this.props.newOptionClick(value)
    this.refs.tagSelect.select.setState({inputValue: ''})
  }

  render() {
    return (
      <div>
        <Select.Creatable
          ref="tagSelect"
          multi={true}
          options={this.props.tags}
          value={this.props.entryTags}
          onNewOptionClick={this.newOptionClick}
          onChange={this.props.tagChange}/>
      </div>
    )
  }
}

Tags.propTypes = {
  tags: PropTypes.array,
  entryTags: PropTypes.array,
  tagChange: PropTypes.func,
  newOptionClick: PropTypes.func
}
