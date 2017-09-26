define(function(require, exports, module) {
  var $btn = $('.send-code');
  var url = $btn.attr('href');
  var sending;
  var left = 60;
  var timer;
  var timeLeft = function() {
    timer = setInterval(function() {
      if (left === 1) {
        $btn.removeClass('disabled').text('重新发送');
        clearInterval(timer);
        left = 60;
        return;
      }
      left--;
      $btn.text(left + ' 秒后重新发送');
    }, 1000);
  };


  $btn.on('click', function(e) {
    e.preventDefault();
    if (sending) return;
    sending = true;
    $.get(url, function(res) {
      sending = false;
      if (res.state) {
        $btn.addClass('disabled').text('60 秒后重新发送');
        timeLeft();
        return;
      }
      alert(res.message || '发送失败，请稍后重试');
    }, 'json');
  });





});

