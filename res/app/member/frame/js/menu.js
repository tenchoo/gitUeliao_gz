define(function(require, exports, module) {
  var $menu = $('.frame-menu');
  $menu.on('click', 'h3', function() {
    var $h3 = $(this);
    var $ul = $h3.next();
    if ($h3.is('.active')) {
      $h3.removeClass('active');
      $ul.addClass('hide');
    } else {
      $ul.removeClass('hide');
      $h3.addClass('active');
    }
  }).on('click', 'li:not(.active) a', function() {
    $menu.find('li').removeClass('active');
    $(this).parent().addClass('active');
  });
});