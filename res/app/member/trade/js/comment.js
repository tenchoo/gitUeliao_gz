define(function(require, exports, module) {

  var dialog = require('modules/dialog/js/dialog.js');
  var $form = $('.comment form');
  
  $form.on('submit',function(e){
    return $form.find('textarea').each(function(){
      var val = $(this).val();
      if ( val === '' ){
        dialog.tip('请填写反馈信息！');
        e.preventDefault()
        return false;
      }
      if ( val.length > 500){
        dialog.tip('反馈信息应为1-500个字符！');
        e.preventDefault()
        return false;
      }
      
    });
    
  });
  
});