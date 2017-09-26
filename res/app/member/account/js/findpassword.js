define(function(require, exports, module) {
  var validator = require('modules/form/js/validator.js');
  var $form = $('form');
  var $account = $('#account');
  $form.on('click', '.send-code', function() {
    validator.sendVerifyCode($(this), {
      field: $account,
      postData: {
        account: $account.val(),
        optype: 'forget'
      },
      form: 'passwordForm'
    });
  }).validate({
    rules: {
      'passwordForm[account]': {
        required: true,
        regexp: validator.regexps.mobile
      },
      'passwordForm[verifyCode]': {
        required: true,
        regexp: validator.regexps.verifyCode
      },
      'passwordForm[password]': {
        required: true,
        regexp: validator.regexps.password
      },
      'passwordForm[repassword]': {
        required: true,
        equalTo: '[name="passwordForm[password]"]'
      }
    },
    messages: {
      'passwordForm[account]': {
        required: '不能为空',
        regexp: '请输入11位手机号码'
      },
      'passwordForm[verifyCode]': {
        required: '请输入验证码',
        regexp: '验证码不正确，请重新输入'
      },
      'passwordForm[password]': {
        required: '请输入6-16个字符，密码需字母和数字组合',
        regexp: '请输入6-16个字符，密码需字母和数字组合'
      },
      'passwordForm[repassword]': {
        required: '请再次输入密码',
        equalTo: '两次输入的密码不一致'
      }
    },
    submitHandler: function(form) {
      validator.formAjax($form, 'passwordForm');
    }
  });
  validator.showErrors($form);
});