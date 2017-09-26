define(function(require, exports, module) {
  var $form = $('.frame-list-bd');
  var input = require('modules/form/js/input.js');
  input.plusMinus();

  function updateItemPrice($tr) {
    var price = input.intFormat($tr.data('price'));
    var num = parseFloat($tr.find('.int-only').val(), 10);
    var total = price * num;
    $tr.data('total', total).find('.total').text((total / 100).toFixed(2));
    $.post('/cart/default/qty', {
      num: num,
      cartId: $tr.data('cartid')
    });
    updateTotalPrice($tr.parent('tbody'));
  }

  function updateTotalPrice($tbody) {
    var totalnum = 0;
    var total = 0;
    $tbody.find('tr.list-body-bd').each(function() {
      var $t = $(this);
      total += $t.data('total');
      totalnum += parseFloat($t.find('.int-only').val(), 10) * 10;
    });
    $form.find('.totalnum').text('数量总计：' + totalnum / 10 + '件');
    $form.find('.totalprice').text(input.formatPrice(total));
  }

  $form.on('click', 'td:last-child a', function(event) {
    event.preventDefault();
    var $t = $(this);
    var $tr = $t.parents('tr');
    var $tbody = $tr.parent('tbody');
    $.get($t.attr('href'), function(res) {
      if (res.state) {
        $('#J_cart').trigger('update');
        $tr.fadeOut(function() {
          $tr.remove();
          updateTotalPrice($tbody);
        });
      }
    }, 'jsonp');
  }).on('change', '.int-only', function() {
    updateItemPrice($(this).parents('tr'));
  }).on('mouseenter mouseleave', '.promo-hd', function(event) {
    var $t = $(this);
    var $bd = $t.next();
    var $arr = $t.find('span');
    if (event.type === 'mouseenter') {
      $bd.removeClass('hide');
      $arr.addClass('active');
      return;
    }
    $bd.addClass('hide');
    $arr.removeClass('active');
  });
});