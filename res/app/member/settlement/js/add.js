define(function(require, exports, module) {
  var input = require('modules/form/js/input.js');
  var $content = $('.frame-content');
  var dialog = require('modules/dialog/js/dialog.js');

  function updateItem($tr, num) {
    var free = $tr.find(':checkbox').prop('checked');
    var total = free ? 0 : $tr.find('.num-float-only').val() * $tr.find('[data-unit]').data('unit');
    var $price = $tr.find('[data-price]');
    $price.data('price', total).text(input.int2Price(total));
    updateTotal();
  }


  function updateTotal() {
    var freight = input.price2Int($content.find('.price-only').val());
    var total = 0;
    $content.find('td[data-price]').each(function() {
      total += $(this).data('price');
    });

    $content.find('.p-total').text('商品金额 ：' + input.int2Price(total));
    $content.find('.a-total').text('总金额 ：' + input.int2Price(freight + total));
  }

  $content
    .on('click', '.del-product', function(e) {
      e.preventDefault();
      var $tr = $(this).parents('tr');
      dialog.confirm('确定删除？', function() {
        $tr.remove();
        updateTotal();
      });
    })
    .on('blur', '.num-float-only', function() {
      var $tr = $(this).parents('tr');
      var num = parseFloat(this.value, 10);
      var $checkbox = $tr.find(':checkbox');
      var canFree = num > 4;
      if (canFree) {
        $checkbox.prop('checked', false);
      }
      $checkbox.prop('disabled', canFree);
      updateItem($tr);
    })
    .on('change', 'tr :checkbox', function(e) {
      updateItem($(this).parents('tr'));
    })
    .on('change', '.price-only', updateTotal)
    .on('click', '.submit', function(e) {
      var $form = $(this).parents('form');
      $form.find('[name="printpush"]').val(0);
      $form.trigger('submit');
    })
    .on('click', '.submit-print', function(e) {
      var $form = $(this).parents('form');
      $form.find('[name="printpush"]').val(1);
      $form.trigger('submit');
    })
    .on('submit', 'form', function(e) {
      e.preventDefault();
      var $form = $(this);
      $.post($form.attr('action'), $form.serializeArray(), function(res) {
        if (res.state) {
          location.href = res.data;
          return;
        }
        dialog.alert(res.message || '提交失败，请稍后重试', { type: 'error' });
      }, 'json');
    });

  input.numFloatOnly();
  input.priceOnly();

});
