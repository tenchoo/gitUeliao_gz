define(function(require, exports, module) {
  var dialog = require('modules/dialog/js/dialog.js');
  var $content = $('.frame-content');
  $content
    .on('click', '.del', function(event) {
      event.preventDefault();
      var $t = $(this);
      dialog.confirm('确定删除？', function() {
        $.get($t.attr('href'), function(res) {
          if (res.state) {
            location.href = res.data;
            return;
          }
          dialog.alert(res.message || '删除失败，请稍后重试！');
        }, 'json');
      });
    })
    .on('click', '.invalid', function(event) {
      event.preventDefault();
      var $t = $(this);
      dialog.confirm('确定取消？', function() {
        $.get($t.attr('href'), function(res) {
          if (res.state) {
            location.href = res.data;
            return;
          }
          dialog.alert(res.message || '取消失败，请稍后重试！');
        }, 'json');
      });
    });
});
