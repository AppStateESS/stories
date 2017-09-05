'use strict'
import React, {Component} from 'react'
import empty from './Empty'

/* global $ */

export default class Abstract extends Component {
  constructor(props) {
    super(props)
    this.resourceName = null
    this.setValue = this.setValue.bind(this)
    this.patchValue = this.patchValue.bind(this)
  }

  setValue(varname, value) {
    if (this.resourceName === null) {
      throw 'No resourceName set'
    }
    if (typeof value === 'object' && value.target !== undefined) {
      value = value.target.value
    }
    let currentState = this.state
    let resource = currentState[this.resourceName]
    resource[varname] = value
    this.setState(currentState)
  }

  patchValue(varname, updateOnEmpty = true) {
    if (this.resourceName === null) {
      throw 'No resourceName set'
    }

    if (this.state[this.resourceName].id === undefined) {
      throw('Resource is missing id')
    }
    const resourceId = this.state[this.resourceName].id

    const resourceValue = this.state[this.resourceName][varname]

    if (!updateOnEmpty && empty(resourceValue)) {
      return
    }

    $.ajax({
      url: `./stories/${this.resourceName}/${resourceId}`,
      data: {
        varname: varname,
        value: resourceValue
      },
      dataType: 'json',
      type: 'patch',
      success: function () {}.bind(this),
      error: function () {}.bind(this)
    })
  }

  render() {
    return (
      <div></div>
    )
  }
}

Abstract.propTypes = {}
