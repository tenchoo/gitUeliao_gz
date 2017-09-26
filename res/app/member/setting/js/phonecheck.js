define(function(require, exports, module) {
  var validator = require('modules/form/js/validator.js');
  var dialog = require('modules/dialog/js/dialog.js');
  var $form = $('.phonecheck form');
  var $phone = $('#phone');
  var isChange = !$phone.length; //是否是修改第一步
  $form.on('click', '.send-code', function() {
    validator.sendVerifyCode($(this), {
      field: $phone,
      fieldSuccess: !isChange,
      postData: isChange ? {
        optype: 'changePhone'
      } : {
        phone: $phone.val(),
        optype: 'setPhone'
      },
      form: 'checkForm'
    });
  }).validate({
    rules: {
      'checkForm[phone]': {
        required: true,
        regexp: validator.regexps.mobile
      },
      'checkForm[verifyCode]': {
        required: true,
        regexp: validator.regexps.verifyCode
      }
    },
    messages: {
      'checkForm[phone]': {
        required: '不能为空',
        regexp: '请输入11位手机号码'
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