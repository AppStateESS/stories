'use strict'
import React, {Component} from 'react'
import ReactDOM from 'react-dom'

/* global $, entry */

class ListView extends Component {
  constructor(props) {
    super(props)
    this.state = {listView : 0}
    this.update = this.update.bind(this)
  }
  
  componentDidMount(){
    this.setState({listView: entry.listView})
  }
  
  update(e) {
    const listView = e.target.value
    this.setState({listView})
    $.ajax({
      url: './stories/Entry/' + entry.id,
      data: {param: 'listView', value: listView},
      dataType: 'json',
      type: 'patch',
    })
  }

  render() {
    return (<div>
      <select value={this.state.listView} onChange={this.update} className="form-control">
        <option value="0">Show summarized</option>
        <option value="1">Show full content</option>
      </select>
    </div>)
  }
}

ListView.propTypes = {}

ListView.defaultProps = {}

ReactDOM.render(<ListView/>, document.getElementById('ListView'))
