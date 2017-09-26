define(function(require, exports, module) {
  var validator = require('modules/form/js/validator.js');
  var $form = $('.close form');
  $form.validate({
    rules: {
      'shopForm[reason]': {
        required: true
      },
      'shopForm[contactPerson]': {
        required: true,
        maxlength: 25
      },
      'shopForm[contactPhone]': {
        required: true,
        regexp: validator.regexps.mobile
      }
    },
    messages: {
      'shopForm[reason]': {
        required: '不能为空'
      },
      'shopForm[contactPerson]': {
        required: '不能为空',
        maxlength: '不能超过25个字'
      },
      'shopForm[contactPhone]': {
        required: '不能为空',
        regexp: '请输入11位手机号码'
      }
    },
    submitHandler: function(form) {
      validator.formAjax($form, 'shopForm');
    }
  });
});