const SampleStoryObj = {
  id: 0,
  title: 'Example title',
  summary: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean risus diam, ia' +
      ' culis vel arcu sit amet, molestie faucibus diam.',
  publishId: 0,
  url: 'stories/',
  publishDateRelative: 'today',
  thumbnail: 'mod/stories/img/sample.jpg',
  authorPic: 'mod/stories/img/profile.png',
  authorName: 'Joan Doe',
  featureId: 0,
  x: 50,
  y: 50,
  zoom: 100
}
const SampleStory = (srcHttp) => {
  const copy = Object.assign({}, SampleStoryObj)
  copy.thumbnail = srcHttp + SampleStoryObj.thumbnail
  return copy
}

export default SampleStory
