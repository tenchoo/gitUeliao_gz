define(function(require, exports, module) {
  require('libs/my97datepicker/4.8.0/WdatePicker.js');
  var dialog = require('modules/dialog/js/dialog.js');
  var $form = $('.frame-list-search form');
  var $operations = $('.list-body-bd .operations');
  var getCheckeds = require('app/member/frame/js/checkedAll.js')();
  var $uploader;
  require('modules/uploader/js/uploader.js').uploader({
    success: function(file, res) {
      $uploader = $('#rt_' + file.source.ruid).parent('.uploader');
      $.post('/ajax',{
        action: 'order',
        optype: 'upload',
        paymemtId:$uploader.data('paymemtid'),
        voucher:res.data
      },function(r){
        $uploader.after('<span><a href="'+seajs.data.uploaderPath+'/../..'+res.data+'" class="text-link" target="_blank">查看凭证</a></span>');
        $uploader.remove();
      },'json');
    }
  });

  $form.on('click', '#starttime', WdatePicker).on('click', '#endtime', WdatePicker);
  $operations.on('click','.cancel-order',function(){
    var $t = $(this).parents().find('.cancel-order-tip');
    var orderid = $(this).data('orderid');
    dialog.confirm($t.html(),function($dialog){
      $.post('/ajax', {
        action: 'order',
        optype: 'cancleorder',
        orderId: orderid,
        closeReason: $dialog.find('select').val(),
      }, function(res) {
        if (res.state) {
          location.href = location.href;
        } else {
          dialog.tip(res.message || '取消失败，请稍后重试！');
        }
      }, 'json');
    });
  }).on('click','.delete-order',function(){
      var orderid = $(this).parent().data('orderid');
      dialog.confirm('确定删除？',function($dialog){
        $.post('/ajax/order', {
          optype: 'delorder',
          orderId: orderid,
          who: 1
        }, function(res) {
          if (res.state) {
            location.href = location.href;
          } else {
            dialog.tip(res.message || '取消失败，请稍后重试！');
          }
        }, 'json');
      });
  }).on('click','.pack',function(){
    var orderid = $(this).data('orderid');
    dialog.confirm('确定要申请延期？',function($dialog){
      $.post('/ajax', {
        action: 'order',
        optype: 'delaykeep',
        orderId: orderid,
      }, function(res) {
        if (res.state) {
          location.href = location.href;
        } else {
          dialog.tip(res.message || '申请延期失败，请稍后重试！');
        }
      }, 'json');
    });
  });
  
});