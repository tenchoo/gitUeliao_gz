define(function(require, exports, module) {
  var template = require('libs/arttemplate/3.0.0/template.js');
  var input = require('modules/form/js/input.js');
  var $content = $('.content-wrap');
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
    var pTotal = 0;
    $content.find('td[data-price]').each(function() {
      pTotal += $(this).data('price');
    });

    $content.find('.p-total').text('商品金额 ：' + input.int2Price(pTotal));
    $content.find('.a-total').text('总金额 ：' + input.int2Price(freight + pTotal));
  }

  $content.on('click', '.del-product', function(e) {
    e.preventDefault();
    var $tr = $(this).parents('tr');
    dialog.confirm('确定删除？', function() {
      $tr.remove();
    });
  }).on('click', '.del-batch', function(e) {

    e.preventDefault();
    var $li = $(this).parent();
    dialog.confirm('确定删除？', function() {
      $li.remove();
    });

  }).on('click', '.add-batch', function(e) {
    e.preventDefault();
    $(this).parent().before(template('batch'));
  }).on('blur', '.num-float-only', function() {
    var $tr = $(this).parents('tr');
    var num = input.intFormat(this.value);
    var $checkbox = $tr.find(':checkbox');
    var free = num > 4;
    $checkbox.prop('disabled', free);
    if (free) {
      $checkbox.prop('checked', false);
    }
    updateItem($tr);
  }).on('change', 'tr :checkbox', function(e) {
    updateItem($(this).parents('tr'));
  }).on('change', '.price-only', updateTotal);

  input.intOnly();
  input.priceOnly();
  input.numFloatOnly();

});