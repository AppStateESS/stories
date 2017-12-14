'use strict'
import React from 'react'
import PropTypes from 'prop-types'
import Select from 'react-select'

export default class Tags extends React.Component {
  constructor(props) {
    super(props)
    this.newOptionClick = this.newOptionClick.bind(this)
    this.forceInput = this.forceInput.bind(this)
  }

  newOptionClick(value) {
    this.props.newOptionClick(value)
    this.refs.tagSelect.select.setState({inputValue: ''})
  }

  forceInput(val) {
    const toLower = val.toLowerCase()
    return toLower
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
          onInputChange={this.forceInput}
          ignoreCase={true}
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
