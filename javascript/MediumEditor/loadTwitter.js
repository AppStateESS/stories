/* global $, twttr */
/* Special thanks to Romain Mougel via github.com/orthes/medium-editor-insert-plugin/issues/229 */
window.onload = function () {
  let k = 0
  let tweet = document.getElementById('twitter-widget-' + k)
  let tweetParent
  let tweetID
  while (tweet) {
    tweetParent = tweet.parentNode
    tweetID = tweet.dataset.tweetId
    $(tweet).remove()
    twttr.widgets.createTweet(tweetID, tweetParent)
    k++
    tweet = document.getElementById('twitter-widget-' + k)
  }
}
