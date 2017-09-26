define(function(require, exports, module) {
  $('table.table').on('click', '.add-comment', function(event) {
    event.preventDefault();
    var $tr = $(this).parents('tr');
    $tr.find('form').removeClass('hide');
  }).on('click', '.edit-comment', function(event) {
    event.preventDefault();
    var $tr = $(this).parents('tr');
    $tr.find('form').removeClass('hide').prev().addClass('hide');
  }).on('click', '.save-comment', function(event) {
    event.preventDefault();
    var $tr = $(this).parents('tr');
    $tr.find('form').trigger('submit');
  }).on('click', '.cancel-comment', function(event) {
    event.preventDefault();
    var $tr = $(this).parents('tr');
    $tr.find('form').addClass('hide').prev().removeClass('hide');
  }).on('submit', 'form', function(event) {
    event.preventDefault();
    var $t = $(this);
    $.post($t.attr('action'), $t.serializeArray(), function(res) {
      if (res.state) {
        $t.addClass('hide').prev().removeClass('hide').html('<span class="text-success">解释：</span>' + $t.find('textarea').val());
      }

    }, 'json');
  });
  var getCheckds = require('modules/checkedall/js/checkedall.js')();
  $('.btn-export').on('click', function() {
    if (getCheckds().length) {
      console.log('导出操作');
      return;
    }
    alert('请选择数据后操作！');
  });
});