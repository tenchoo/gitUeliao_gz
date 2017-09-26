define(function(require, exports, module) {
  $('body').prepend(['<div style="position:fixed;top:0;right:0;bottom:0;left:0;background:rgba(0,0,0,.5);z-index:99;" class="ajax-loading hide">',
    '<div class="text-center" style="width:20%;min-width:200px;background:#fff;padding:10px 0;margin:25% auto 0">打印推送中',
    '</div></div>'
  ].join(''));
  $('.content-wrap')
    .on('click', '.print', function(e) {
      e.preventDefault();
      var $l = $('.ajax-loading').removeClass('hide');
      $.get($(this).attr('href'), function(res) {
        $l.find('div').text(res.message);
        setTimeout(function() {
          $l.addClass('hide').find('div').text('打印推送中');
        }, 1500);
      }, 'json');
    });
});
