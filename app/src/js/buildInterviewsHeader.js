var buildCollectionsList = require('./buildCollectionsList')
var buildConnected = require('./buildConnected')
var icon = require('./icon')
var scrollIndicator = require('./scrollIndicator')
var socialLinks = require('./socialLinks')

var buildInterviewsHeader = function(wrapper, data){

  var outer = $('<div class="contentHeaderOuter" />')
  var header = $('<header class="contentHeader contentHeader--interview"/>')
  var inner = $('<div class="contentHeader-inner" />')
  var imgWrapper = $('<div class="contentHeader-imgWrapper" />')

  header.append( '<span class="contentHeader-type">'+icon('interview', 'type')+'Interview</span>')

  inner.append('<h2 class="contentHeader-head">'+data.name+'</h2>')

  if(data.collections){
    var collections = buildCollectionsList(data.collections)
    inner.append(collections)
  }

  if(data.introduction){
    inner.append('<div class="contentHeader-introduction">'+data.introduction+'</div>')
  }

  if(data.related){
    var related = buildConnected(data.related)
    inner.append('<h3 class="contentHeader-relatedHead">Related to</h3>')
    inner.append(related)
  }

  inner.append('<span class="contentHeader-selectWrapper" id="selectwrap-'+data.id+'" style="display:none"><select class="contentHeader-select" id="select-'+data.id+'"><option value="null">Contents</option></select></span>')

  var video = $('<video data-able-player data-youtube-id="'+data.video_id+'" data-youtube-playsinline>')

  if(data.transcript_url){
    video.attr('data-transcript-src', 'transcript-'+data.id)
    video.append('<track kind="captions" src="'+data.transcript_url+'"/>')
  }

  if(data.description_url){
    video.append('<track kind="descriptions" src="'+data.description_url+'"/>')
  }

  header.append(inner)

  if(data.video_id){
    imgWrapper.append('<span class="contentHeader-toggleVid" data-action="toggle" data-target=".contentHeader-imgWrapper"><label for="toggleVid">Video Display:</label><select id="toggleVid"><option>Small</option><option>Medium</option><option>Large</option><option>Hidden</option></select></span >')
    imgWrapper.append(video)
    imgWrapper.append('<a class="able-fake-pause"></a><div class="contentHeader-searchwrap"><input class="contentHeader-search" id="video-search" placeholder="Search transcript, annotations & descriptions..."></div>')
  }

  imgWrapper.append('<div class="shareLinks">Share this interview'+socialLinks(data.link, data.title, data.introduction.replace(/(<([^>]+)>)/ig,""), data.introduction.replace(/(<([^>]+)>)/ig,""))+'</div>')

  header.append(imgWrapper)
  header.append(scrollIndicator.add('.transcript'))
  outer.append(header)
  wrapper.append(outer)
}

module.exports = buildInterviewsHeader
