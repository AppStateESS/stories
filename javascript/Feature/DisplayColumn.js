'use strict'
import React from 'react'
import PropTypes from 'prop-types'
import Select from 'react-select'
import MoveButton from './MoveButton'
import 'react-select/dist/react-select.min.css'

class DisplayColumn extends React.Component {
  constructor(props) {
    super(props)
    this.state = {
      showButtons: false
    }
    this.moveButtons = this.moveButtons.bind(this)
  }

  setShowButtons(value) {
    this.setState({showButtons: value})
  }

  moveButtons() {
    const {moveThumb, stopMove, holdThumb, entry,} = this.props
    if (this.state.showButtons === false) {
      return null
    }
    const cX = entry.x
    const cY = entry.y
    return (
      <div className="move-buttons">
        <table>
          <tbody>
            <tr>
              <td></td>
              <td>
                <MoveButton dir="up" {...{holdThumb, stopMove, moveThumb,cX, cY}}/>
              </td>
              <td></td>
            </tr>
            <tr>
              <td>
                <MoveButton dir="left" {...{holdThumb, stopMove, moveThumb,cX, cY}}/>
              </td>
              <td></td>
              <td>
                <MoveButton dir="right" {...{holdThumb, stopMove, moveThumb,cX, cY}}/>
              </td>
            </tr>
            <tr>
              <td></td>
              <td>
                <MoveButton dir="down" {...{holdThumb, stopMove, moveThumb,cX, cY}}/>
              </td>
              <td></td>
            </tr>
          </tbody>
        </table>
      </div>
    )
  }

  render() {
    const {
      format,
      bsClass,
      entry,
      stories,
      applyStory,
      clearStory,
      previousEmpty
    } = this.props
    const _class = 'story-feature ' + format
    const position = `${entry.x}% ${entry.y}%`
    const thumbnailStyle = {
      backgroundImage: `url('${entry.story.thumbnail}')`,
      backgroundPosition: position,
    }

    let storyList = <em>No published stories available</em>
    if (stories !== undefined) {
      let storyOptions = stories.map(function (value) {
        return {value: value.id, label: value.title,}
      })
      if (previousEmpty) {
        storyList = null
      } else {
        storyList = (
          <div>
            <Select options={storyOptions} value={0} onChange={applyStory}/>
            {entry.id > 0 ? <button className="btn btn-primary" onClick={clearStory}>Clear</button> : null}
          </div>
        )
      }
    }

    return (
      <div className={bsClass}>
        <div className={_class}>
          <div
            className="story-thumbnail"
            style={thumbnailStyle}
            onMouseEnter={this.setShowButtons.bind(this, true)}
            onMouseLeave={this.setShowButtons.bind(this, false)}>{this.moveButtons()}</div>
          <div className="story-content">
            <div className="story-title">
              <a title="Link to story">
                <h4>{entry.story.title}</h4>
              </a>
            </div>
            <div className="story-summary">{entry.story.strippedSummary}</div>
            <div className="published-date">Published {entry.story.publishDateRelative}
            </div>
          </div>
        </div>
        {storyList}
      </div>
    )
  }
}

DisplayColumn.propTypes = {
  bsClass: PropTypes.string,
  format: PropTypes.string,
  entry: PropTypes.object,
  stories: PropTypes.array,
  applyStory: PropTypes.func,
  clearStory: PropTypes.func,
  moveThumb: PropTypes.func,
  stopMove: PropTypes.func,
  holdThumb: PropTypes.func,
  previousEmpty: PropTypes.bool,
}

DisplayColumn.defaultTypes = {}

export default DisplayColumn
