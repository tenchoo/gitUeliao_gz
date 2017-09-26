define(function(require, exports, module) {
  var input = require('modules/form/js/input.js');
  input.intOnly();
  input.numFloatOnly();
  var template = require('libs/arttemplate/3.0.0/template.js');
  var $content = $('.content-wrap');
  var $tbody = $content.find('tbody');
  var $supplier = $('input[name="singleNumber"]');
  var $addBtn = $('#btn-add');
  var $form = $('.alloction');
  var cache;
  var $callbacksub=$('.callbacksub');
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
  $content.on('click', '[data-templateid]', function(event) {
    event.preventDefault();
    var serial = $supplier.val();
    if (serial === '') return;
    var t = new Date().getTime();
    $.get('/api/contain_product_area', {
      serial: serial,
      house: $supplier.data('house')
    }, function(res) {
      $tbody.append(template('alloctionlist', $.extend(cache, {
        t: t,
        area: res.data
      })));
    }, 'json');
  }).on('click', '.del', function(event) {
    event.preventDefault();
    $(this).parents('tr:first').remove();
  }).on('change', '.parea', function(event) {
    var $t = $(this);
    var $tr = $t.parents('tr');
    $.get('/api/contain_product_position', {
      serial: $t.data('serial'),
      area: $t.val()
    }, function(res) {
      $t.next().html('<option value="">请选择仓位</option>' +
        (function() {
          var html = '';
          for (var i in res.data) {
            html += '<option value="' + res.data[i].positionId + '">' + res.data[i].positionTitle + '</option>';
          }
          return html;
        }())
      );
    }, 'json');
    $t.next().html('<option value="">请选择仓位</option>');
    $tr.find('.pbat').html('<option value="">产品批次</option>');
  }).on('change', '.ppo', function(event) {
    var $t = $(this);
    var $tr = $t.parents('tr');
    $.get('/api/product_at_batch2', {
      serial: $t.data('serial'),
      position: $t.val()
    }, function(res) {
      $tr.find('.pbat').html('<option value="">产品批次</option>' +
        (function() {
          var html = '';
          for (var i in res.data) {
            html += '<option data-max="' + res.data[i].total + '" value="' + res.data[i].productBatch + '">' + res.data[i].productBatch + '</option>';
          }
          return html;
        }())
      );
    }, 'json');
    $tr.find('.pbat').html('<option value="">产品批次</option>');
  }).on('change', '.pbat', function(event) {
    var $t = $(this);
    var $tr = $t.parents('tr');
    $tr.find('.num-float-only').data('max', $t.find('option:selected').data('max'));
  });

  $form.on('submit', function(e) {
    e.preventDefault();
    $callbacksub.prop('disabled',true);
    $callbacksub.html('请稍候..');
    var hasError;
    $form.find('tr .num-float-only').each(function() {
      var $t = $(this);
      var val = parseFloat(this.value || 0);
      var max = parseFloat($t.data('max'));
      if (val > max) {
        hasError = true;
        alert('调拨数量不能大于库存数量');
        $t.trigger('focus');
		$callbacksub.prop('disabled',false);
		$callbacksub.html('立即调拨');
        return false;
      }
    });
    if (hasError) return;
    $.post(location.href, $form.serializeArray(), function(res) {
      if (res.state) {
        location.href = res.data;
        return;
      }
      alert(res.message || '保存失败，请稍后重试！');
      $callbacksub.prop('disabled',false);
      $callbacksub.html('立即调拨');
    }, 'json');
  });
});
