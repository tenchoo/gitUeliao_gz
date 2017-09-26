define(function(require, exports, module) {
  require('modules/checkedall/js/checkedall.js')();
  var $uploader;
  require('modules/uploader/js/uploader.js').uploader({
    success: function(file, res) {
      $uploader = $('#rt_' + file.source.ruid).parent('.uploader');
      $.post('/order/default/upload',{
        paymemtId:$uploader.data('paymemtid'),
        voucher:res.data
      },function(r){
        $uploader.after('<span><a href="'+seajs.data.uploaderPath+'/../..'+res.data+'" class="text-primary" target="_blank">查看凭证</a></span>');
        $uploader.remove();
      },'json');
    }
  });
  var $search = $('form');


  var $packconfirm = $('.pack-confirm');
  $packconfirm.on('show.bs.modal',function(event){
    var a = $(event.relatedTarget);
    var orderid = a.data('orderid');
    $(this).find('.modal-footer .btn-success').attr('data-orderid',orderid);
  }).on('click','.btn-success',function(){
    var orderid = $(this).data('orderid');
    $.post('/order/default/noticepicking/',{
      orderId:orderid
    },function(res){
      if(res.state){
        location.href = location.href;
      }
    },'json');
  });
  var $orderconfirm = $('.cancel-order-confirm');
  var $form = $orderconfirm.find('form');
  $orderconfirm.on('show.bs.modal',function(event){
    var a = $(event.relatedTarget);
    var orderid = a.data('orderid');
    $(this).find('[name="orderId"]').val(orderid);
  }).on('click','.btn-success',function(){
    var orderid = $(this).data('orderid');
    $.post($form.attr('action'),$form.serializeArray(),function(res){
      if(res.state){
        location.href = location.href;
      }
    },'json');
  });
  var $deleteconfirm = $('.delete-order-confirm');
  $deleteconfirm.on('show.bs.modal',function(event){
    var a = $(event.relatedTarget);
    var orderid = a.data('orderid');
    $(this).find('.modal-footer .btn-success').attr('data-orderid',orderid);
  }).on('click','.btn-success',function(){
    var orderid = $(this).data('orderid');
    $.post('/order/default/del/',{
      orderId:orderid
    },function(res){
      if(res.state){
        location.href = location.href;
      }
    },'json');
  });
  var $deliveryconfirm = $('.notice-delivery-confirm');
  $deliveryconfirm.on('show.bs.modal',function(event){
    var a = $(event.relatedTarget);
    var packingid = a.data('packingid');
    $(this).find('.modal-footer .btn-success').attr('data-packingid',packingid);
  }).on('click','.btn-success',function(){
    var packingid = $(this).data('packingid');
    $.post('/order/default/noticedelivery/',{
      packingId:packingid
    },function(res){
      if(res.state){
        location.href = location.href;
      }
    },'json');
  });
});