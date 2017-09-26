define(function(require, exports, module) {
  require('modules/editor/js/editor.js')();
  var validator = require('modules/form/js/validator.js');
  var dialog = require('modules/dialog/js/dialog.js');
  var $form = $('.recommend form');
  var $recommend = $('.recommend');
  $form.validate({
    rules: {
      'EshopBlock[title]': {
        required: true,
        maxEnLength: 12
      }
    },
    messages: {
      'EshopBlock[title]': {
        required: '不能为空',
        maxEnLength: '不得超过6个汉字或者12个英文字母'
      }
    },
    submitHandler: function(form) {
      validator.formAjax($form, 'EshopBlock', true);
    }
  });
  $recommend.on('click','.add',function(event){
    var $t = $(this);
    $.post('/ajax/shoprecommend',{
      optype:'add',
      recommendId:$t.data('recommendid'),
      productId:$t.data('productid')
    },function(res){
      if (!res.state) {
        dialog.tip(res.message || '操作失败，请稍后重试！');
        return;
      }else{
        location.href = res.data;
      }
    },'json');
  }).on('click','.del',function(event){
    var $t = $(this);
    $.post('/ajax/shoprecommend',{
      optype:'del',
      recommendId:$t.data('recommendid'),
      productId:$t.data('productid')
    },function(res){
      if (!res.state) {
        dialog.tip(res.message || '操作失败，请稍后重试！');
        return;
      }else{
        location.href = res.data;
      }
    },'json');
  });
});