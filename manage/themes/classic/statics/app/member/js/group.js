define(function(require, exports, module) {

  var $delconfirm = $('.del-confirm');
  $delconfirm.on('show.bs.modal',function(event){
    var a = $(event.relatedTarget);
    var groupid = a.data('groupid');
    $(this).find('.modal-footer .btn-success').attr('data-groupid',groupid);
  }).on('click','.btn-success',function(){
    var groupid = $(this).data('groupid');
    $.get('/member/group/del/',{
      groupId:groupid
    },function(res){
      if(res.state){
        location.href = location.href;
      }
    },'json');
  });
  
  
});