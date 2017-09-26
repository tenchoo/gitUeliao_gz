define(function(require, exports, module) {
  var $uploader;
  
  var $delconfirm = $('.del-confirm');
  $delconfirm.on('show.bs.modal',function(event){
    var a = $(event.relatedTarget);
    var id = a.data('id');
    $(this).find('.modal-footer .btn-success').attr('data-id',id);
  }).on('click','.btn-success',function(){
    var id = $(this).data('id');
    $.get('/member/level/del/',{
      id:id
    },function(res){
      if(res.state){
        location.href = location.href;
      }
    },'json');
  });
  
  require('modules/uploader/js/uploader.js').uploader({
    success: function(file, res) {
      $uploader = $('#rt_' + file.source.ruid).parents('.uploader');
      $uploader.find('button').html('<img src="' + seajs.data.uploaderPath + '/../..' + res.data + '" alt="" width="80" height="80"><span class="bg"></span><span>重新上传</span>');
      $uploader.find('input[name="logo"]').val(res.data);
    },
    formData: {
      'case': 'res'
    }
  });
  
});