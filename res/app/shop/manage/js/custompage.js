define(function(require, exports, module) {
  require('modules/editor/js/editor.js')();
  var validator = require('modules/form/js/validator.js');
  var dialog = require('modules/dialog/js/dialog.js');
  var $form = $('.custompage form');
  var $custompage = $('.custompage');
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
  $custompage.on('click', '.del', function(event) {
    event.preventDefault();
    var $t = $(this);
    dialog.confirm('确定删除？',function(){
      $.get($t.attr('href'),function(res){
        if(res.state){
          $t.parents('tr').remove();
        }else{
          dialog.tip(res.message || '删除失败，请稍后重试！' );
        }
      },'json');
    });
  }).on('change','td :checkbox',function(event){
    var $t = $(this);
    $.post($t.data('url'),{
      'EshopBlock[status]':$t.prop('checked')?1:0
    },function(res){
      if (!res.state) {
        dialog.tip(res.message || '操作失败，请稍后重试！');
        return;
      }
    },'json');
  });
});