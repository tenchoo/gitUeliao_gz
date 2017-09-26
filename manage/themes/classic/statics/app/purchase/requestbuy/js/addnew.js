define(function(require, exports, module) {
  var input = require('modules/form/js/input.js');
  input.numFloatOnly();
  var template = require('libs/arttemplate/3.0.0/template.js');
  var $content = $('.content-wrap');
  var $tbody = $content.find('tbody');
  var $supplier = $('input[name="productSearchBox"]');
  var $addBtn = $('#btn-add');
  var cache;
  var $addnewsub =$('.addnewsub');
  input
    .suggestion($supplier, {
      er: function() {
        $addBtn.prop('disabled', true);
      },
      cb: function($li, data) {
        cache = data;
        $addBtn.prop('disabled', false);
      }
    });
  $content
    .on('click', '[data-templateid]', function(event) {
      event.preventDefault();
      if ($tbody.find('tr[data-id="' + cache.id + '"]').length) return;
      if ($supplier.val() === '') return;
      $tbody.append(template('requestbuylist', cache));
    })
    .on('click', '.del', function(event) {
      event.preventDefault();
      $(this).parents('tr:first').remove();
    })
    .on('submit', 'form', function(e) {
      e.preventDefault();
      $addnewsub.prop('disabled', true);
      $addnewsub.html('请稍候..');
      var $t = $(this);
      if ($t.data('submit')) return;
      $t.data('submit', true);
      $.post($t.attr('action'), $t.serializeArray(), function(res) {
        $t.data('submit', false);
        if (res.state) {
          location.href = res.data;
          return;
        }
        alert(res.message || '申请失败，请稍后重试！');
         $addnewsub.prop('disabled', false);
        $addnewsub.html('申请采购');
      }, 'json');
    });


});
