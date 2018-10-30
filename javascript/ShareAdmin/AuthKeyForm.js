'use strict'
import React from 'react'
import PropTypes from 'prop-types'
import InputField from '@essappstate/canopy-react-inputfield'

const AuthKeyForm = ({host, update, save}) => {
  if (!host) {
    return <div></div>
  }
  return (<div>
    <InputField value={host.authkey} change={update} placeholder="Paste in authkey received from host."/>
    <button className="btn btn-primary" onClick={save}>Save</button>
  </div>)
}

AuthKeyForm.propTypes = {
  host: PropTypes.object,
  update: PropTypes.func,
  save: PropTypes.func,
}

export default AuthKeyForm
