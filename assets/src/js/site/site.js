var img = require('./img');

$( document ).ready( function(){

  var toggleNav = function( target ){
    $( target ).toggleClass( target.slice(1) + '--expand' );
  }

  $( '.header-navToggle' ).click( function(){
    toggleNav( '.header-navInner' );
    toggleNav( '.header-navToggleButton' );
    var label = $( '.header-navToggleLabel' );
    label.text( label.text() === 'Menu' ? 'Close' : 'Menu' );
  } );

  $('body').on('click', '.researchMenu-toggle', function(){
    toggleNav( '.researchMenu' );
  } );

  img( '.js-img' );

  $('#author-select').change(function(){
    const value = $(this).val()
    if(!value) return
    window.location.href = value
  })

  $('#option_toggle').click(function() {
    $('.header-navInner-options').toggleClass('open')
  })

} );
