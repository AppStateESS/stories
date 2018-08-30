'use strict'
import React, {Component} from 'react'
import ReactDOM from 'react-dom'
import PropTypes from 'prop-types'

/* global $, entry */

export default class ImageOrientation extends Component {
  constructor(props) {
    super(props)
    this.state = {orientation : 0}
  }
  
  componentDidMount(){
    this.setState({orientation : this.props.entry.imageOrientation})
  }
  
  update(e) {
    const orientation = e.target.value
    $.ajax({
      url: `stories/Entry/${this.props.entry.id}/orientation`,
      data: {orientation: orientation},
      dataType: 'json',
      type: 'put',
      success: ()=>{
      },
      error: ()=>{}
    })
    this.setState({orientation})
  }

  render() {
    return (<div>
      <select className="form-control" value={this.state.orientation} onChange={(e)=>this.update(e)}>
        <option value="-1" disabled={true}>Summary image orientation</option>
        <option value="0">Centered</option>
        <option value="1">Left </option>
        <option value="2">Right</option>
      </select>
    </div>)
  }
}

ImageOrientation.propTypes = {entry : PropTypes.object,}

ImageOrientation.defaultProps = {}

ReactDOM.render(<ImageOrientation entry={entry}/>, document.getElementById(
  'ImageOrientation'
))
