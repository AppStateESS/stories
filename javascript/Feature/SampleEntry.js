const SampleStory = {
  entryId: 0,
  title: 'Example title',
  strippedSummary: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean risus diam, iacu' +
      'lis vel arcu sit amet, molestie faucibus diam.',
  publishDateRelative: 'today',
  thumbnail: 'mod/stories/img/sample.jpg',
}

const SampleEntry = (srcHttp) => {
  const copy = Object.assign({}, SampleStory)
  copy.thumbnail = srcHttp + SampleStory.thumbnail
  return {entryId: 0, x: 50, y: 50, zoom: 100, story: copy}
}

export default SampleEntry
