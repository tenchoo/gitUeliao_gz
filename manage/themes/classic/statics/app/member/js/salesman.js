define(function(require, exports, module) {
  var getCheckds = require('modules/checkedall/js/checkedall.js')();

  var $delconfirm = $('.del-confirm');
  $delconfirm.on('show.bs.modal',function(event){
    var a = $(event.relatedTarget);
    var memberid = a.data('memberid');
    $(this).find('.modal-footer .btn-success').attr('data-memberid',memberid);
  }).on('click','.btn-success',function(){
    var memberid = $(this).data('memberid');
    $.get('/member/salesman/del/',{
      memberId:memberid
    },function(res){
      if(res.state){
        location.href = location.href;
      }
    },'json');
  });
  
  
});