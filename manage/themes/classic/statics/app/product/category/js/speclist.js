define(function(require, exports, module) {
  var getCheckds = require('modules/checkedall/js/checkedall.js')();
  var dialog = window;

  var $content = $('.content-wrap');

  function del(ids, cb) {
    cb = cb || function() {};
    ids = $.isArray(ids) ? ids : [ids];
    if (!confirm('您确定要删除吗？')) return;

    $.post('/category/spec/delvalue', {
      specvalueIds: ids
    }, function(res) {
      if (res.state) {
        cb();
      }
    }, 'json');
  }


  $content.on('click', '.well .del', function(event) {
    event.preventDefault();
    var $checkeds = getCheckds();
    var ids = [];
    var $trs = $checkeds.parents('tr');
    if ($checkeds.length < 1) {
      return dialog.alert('请选择数据后操作');
    }
    $checkeds.each(function() {
      ids.push($(this).val());
    });

    del(ids, function() {
      $trs.fadeOut(function() {
        $trs.remove();
      });
    });
  }).on('click', 'tbody .del', function(event) {
    event.preventDefault();
    var $tr = $(this).parents('tr');
    del($tr.find(':checkbox').val(), function() {
      $tr.fadeOut(function() {
        $tr.remove();
      });
    });
  });
});