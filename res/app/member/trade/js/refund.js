define(function(require, exports, module) {
  require('app/member/frame/js/checkedAll.js')();
  var $content = $('.frame-content');
  var input = require('modules/form/js/input.js');
  var dialog = require('modules/dialog/js/dialog.js');
  input.numFloatOnly();

  function upItem($tr, num) {
    var total = num * $tr.find('td[data-unit]').data('unit');
    $tr.find('td[data-price]').data('price', total).text(input.int2Price(total));
    upTotal();
  }

  function upTotal() {
    var total = 0;
    $content.find('tr.checked td[data-price]').each(function() {
      total += $(this).data('price');
    });
    $content.find('tfoot .total').text(input.int2Price(total));
  }

  $content
    .on('change', 'tbody :checkbox', function(e) {
      var $tr = $(this).parents('tr')[this.checked ? 'addClass' : 'removeClass']('checked');
      $tr.find('.num-float-only').prop('disabled', !this.checked);
      upTotal();
    })
    .on('blur', '.num-float-only', function(e) {
      var $t = $(this);
      var $tr = $t.parents('tr');
      var val = this.value;
      var dV = this.defaultValue;
      if (val === '0') {
        this.value = dV;
        $tr.find(':checkbox').prop('checked', false).trigger('change');
        return;
      }
      if (parseFloat(val) > parseFloat(dV)) {
        dialog.tip('退货数量不能大于购买数量', { type: 'error' });
        $t.trigger('focus');
        return;
      }
      upItem($tr, this.value);
    })
    .on('submit', 'form', function(e) {
      e.preventDefault();
      var $t = $(this);
      if ($content.find('tr.checked').length === 0) {
        return dialog.tip('请选择要退货的产品', { type: 'error' });
      }
      if ($.trim($content.find('textarea').val()).length < 6) {
        return dialog.tip('退货理由在6个字以上', { type: 'error' });
      }
      $.post($t.attr('action'), $t.serializeArray(), function(res) {
        if (res.state) {
          location.href = res.data;
          return;
        }
        dialog.tip(res.message || '申请失败，请稍后重试', { type: 'error' });
      }, 'json');
    });

});
