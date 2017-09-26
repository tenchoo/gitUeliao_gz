define(function(require, exports, module) {
  require('libs/my97datepicker/4.8.0/WdatePicker.js');
  var dialog = require('modules/dialog/js/dialog.js');
  var $form = $('.frame-list-search form');
  var $operations = $('.list-body-bd .operations');
  var getCheckeds = require('app/member/frame/js/checkedAll.js')();
  var $body = $('body');
  
  function formatNum(val) {
    return parseInt(val.replace(/\D/g, ''), 10) || 1;
  }
	function formatPrice(price) {
    return ((parseInt(price, 10) || 0) / 100).toFixed(2);
  }
  function updateItemPrice($form) {
    var disPrice = 0;
    var freight= $form.find('input[name="orderForm[freight]"]').val();
    var $freight = freight*100;
    var $total = $form.find('.dialog-price').data('total');
    $form.find('input[data-discount]:not([name="orderForm[freight]"])').each(function() {
    	disPrice += $(this).data('discount');
    });
    $nowtotal = $total + $freight + disPrice;
    $form.find('.dialog-discount').text((disPrice < 0 ? '' : '+') +formatPrice(disPrice));
    $form.find('.dialog-freight').text(formatPrice($freight));
    $form.find('.dialog-total-price').text(formatPrice($nowtotal));
  }
  
  $body.on('keyup', '.dialog form .text-box', function() {
    var $t = $(this);
    var val = formatNum($t.val());
    $t.val(val).data('discount',val*100 - parseInt($t.data('amount'), 10));
    updateItemPrice($t.parents('form'));
  });
  
  $form.on('click', '#starttime', WdatePicker).on('click', '#endtime', WdatePicker);
  $operations.on('click','.cancel-order',function(){
    var $t = $(this).parents().find('.cancel-order-tip');
    var orderid = $(this).parent().data('orderid');
    dialog.confirm($t.html(),function($dialog){
      $.post('/ajax/order', {
        optype: 'cancleorder',
        orderId: orderid,
        closeReason: $dialog.find('select').val(),
        who: 2
      }, function(res) {
        if (res.state) {
          location.href = location.href;
        } else {
          dialog.tip(res.message || '取消失败，请稍后重试！');
        }
      }, 'json');
    });
  }).on('click','.change-price',function(){
    var $t = $(this).next('.change-price-tip');
    dialog.confirm($t.html(),{
    	okFun:function($dialog){
    		$dialog.find('form').trigger('submit');
      }
    });
  });
  
});