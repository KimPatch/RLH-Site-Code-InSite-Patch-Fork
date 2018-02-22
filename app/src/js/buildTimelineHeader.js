var buildCollectionsList = require('./buildCollectionsList');
var buildConnected = require('./buildConnected');
var icon = require('./icon');
var respImg = require('./respImg');
var socialLinks = require('./socialLinks');

var buildTimelineHeader = function (wrapper, data, type = null){
  var header = $('<header class="contentHeader contentHeader--timeline"/>');
  var inner = $('<div class="contentHeader-inner" />');
  var imgWrapper = $('<div class="contentHeader-imgWrapper" />');
  if(type !== false){
    header.append(`
      <span class="contentHeader-type">
        ${icon(data.type, 'type')}
        ${type || 'Timeline'}
      </span>
    `);
  }
  inner.append('<h2 class="contentHeader-head">'+data.name+'</h2>');

  if(data.collections){
    var collections = buildCollectionsList(data.collections);
    inner.append(collections);
  }

  if(data.introduction){
    inner.append('<div class="contentHeader-introduction">'+data.introduction+'</div>');
  }

  if(data.related){
    var related = buildConnected(data.related);
    inner.append('<h3 class="contentHeader-relatedHead">Related to</h3>');
    inner.append(related);
  }

  header.append(inner);

  if(data.image){
    imgWrapper.append(respImg.markup(data.image, 'feat_lg', 'respImg contentHeader-img', null, true));
  }

  var intro = data.intro || data.introduction || ''
  imgWrapper.append('<div class="shareLinks">Share this collection'+socialLinks(
    data.link,
    data.title,
    intro.replace(/(<([^>]+)>)/ig,""),
    intro.replace(/(<([^>]+)>)/ig,"")
 )+'</div>')
  header.append(imgWrapper);
  wrapper.append(header);
}

module.exports = buildTimelineHeader;
