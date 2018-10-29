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
      url: './stories/Entry/' + entry.id,
      data: {param: 'imageOrientation', value: orientation},
      dataType: 'json',
      type: 'patch',
    })

    this.setState({orientation})
  }

  render() {
    return (<div>
      <select className="form-control input-sm" value={this.state.orientation} onChange={(e)=>this.update(e)}>
        <option value="0">Centered thumbnail</option>
        <option value="1">Left thumbnail</option>
        <option value="2">Right thumbnail</option>
      </select>
    </div>)
  }
}

ImageOrientation.propTypes = {entry : PropTypes.object,}

ImageOrientation.defaultProps = {}

ReactDOM.render(<ImageOrientation entry={entry}/>, document.getElementById(
  'ImageOrientation'
))
