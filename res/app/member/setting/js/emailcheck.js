define(function(require, exports, module) {
  var validator = require('modules/form/js/validator.js');
  var dialog = require('modules/dialog/js/dialog.js');
  var $form = $('.emailcheck form');
  var $email = $('#email');
  var isChange = !$email.length; //是否是修改第一步
  $form.on('click', '.send-code', function() {
    validator.sendVerifyCode($(this), {
      field: $email,
      fieldSuccess: !isChange,
      postData: isChange ? {
        optype: 'changeEmail'
      } : {
        email: $email.val(),
        optype: 'changeEmail2'
      },
      form: 'checkForm'
    });
  }).validate({
    rules: {
      'checkForm[email]': {
        required: true,
        regexp: validator.regexps.email
      },
      'checkForm[verifyCode]': {
        required: true,
        regexp: validator.regexps.verifyCode
      }
    },
    messages: {
      'checkForm[email]': {
        required: '不能为空',
        regexp: '请输入正确的邮箱账号'
      },
      'checkForm[verifyCode]': {
        required: '请输入验证码',
        regexp: '验证码不正确，请重新输入'
      }
    },
    submitHandler: function(form) {
      validator.formAjax($form, 'checkForm', !isChange);
    }
  });
});