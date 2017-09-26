define(function(require, exports, module) {
  $('.content-wrap').on('click', '.f', function(event) {
    event.preventDefault();
    var $t = $(this);
    if (!confirm('确认发货完成？')) return;

    $.get($t.attr('href'), function(res) {
      if (res.state) {
        location.href = location.href;
      } else {
        alert(res.message || '操作失败！');
      }
    }, 'json');
  });

});
