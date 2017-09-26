define(function(require, exports, module) {
  var validator = require('modules/form/js/validator.js');
  var $form = $('.cart .form-horizontal form');

  $form.validate({
    rules: {
      'order[password]': {
        required: true,
        regexp: validator.regexps.password
      }
    },
    messages: {
      'order[password]': {
        required: '请输入6-16个字符，密码需字母和数字组合',
        regexp: '请输入6-16个字符，密码需字母和数字组合'
      }
    },
    submitHandler: function(form) {
      validator.formAjax($form, 'order');
    }
  });


});