define(function(require, exports, module) {
  var $delconfirm = $('.add-confirm');
  $delconfirm.on('show.bs.modal',function(event){
    var a = $(event.relatedTarget);
    var id = a.data('id');
    var rel = a.data('rel');
    $(this).find('.modal-footer .btn-success').attr({'data-id':id,'data-rel':rel});
  }).on('click','.btn-success',function(){
    var id = $(this).data('id');
    var rel = $(this).data('rel');
    $.get(rel,{
      id:id
    },function(res){
      if(res.state){
        location.href = location.href;
      }
    },'json');
  });
  
  
});