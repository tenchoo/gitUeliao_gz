define(function(require, exports, module) {
  var $menu = $('.frame-help-menu');
  $menu.on('click', 'h3', function() {
    $(this).parent().toggleClass('active');
  });
});