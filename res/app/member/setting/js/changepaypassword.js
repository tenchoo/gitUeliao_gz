define(function(require, exports, module) {
  var validator = require('modules/form/js/validator.js');
  var dialog = require('modules/dialog/js/dialog.js');
  var $form = $('.paypassword form');

  $form.on('click', '.send-code', function() {
    validator.sendVerifyCode($(this), {
      postData: {
        optype: 'chagePayPassword'
      },
      form: 'passwordForm'
    });
  }).validate({
    rules: {
      'passwordForm[verifyCode]': {
        required: true,
        regexp: validator.regexps.verifyCode
      }
    },
    messages: {
      'passwordForm[verifyCode]': {
        required: '请输入验证码',
        regexp: '验证码不正确，请重新输入'
      }
    },
    submitHandler: function(form) {
      validator.formAjax($form, 'passwordForm');
    }
  });
});