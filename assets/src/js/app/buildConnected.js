const icon = require( './icon' );
const internalLink = require( './internalLink' );

const buildConnected = (related = []) => {
  return `
    <ul class="relatedItem-wrapper">
      ${(related || []).map((item) => (`
        <li class="relatedItem relatedItem--${item.type === 'post' ? 'blog' : item.type}">
          ${internalLink(item, `${icon((item.type === 'post' ? 'blog' : item.type), 'type')} ${item.name}`)}
        </li>
      `)).join(' ')}
    </ul>
  `
}

module.exports = buildConnected
