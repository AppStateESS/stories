const SampleStory = {
  id: 0,
  title: 'Example title',
  strippedSummary: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean risus diam, iacu' +
      'lis vel arcu sit amet, molestie faucibus diam.',
  publishDateRelative: 'today',
  thumbnail: 'mod/stories/img/sample.jpg',
}

const SampleEntry = () => {
  return {id: 0, x: 50, y: 50, story: SampleStory}
}

export default SampleEntry
