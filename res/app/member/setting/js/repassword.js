define(function(require, exports, module) {
  var validator = require('modules/form/js/validator.js');
  var $form = $('.repassword form');
  $form.validate({
    rules: {
      'passwordForm[oldpassword]': {
        required: true,
        regexp: validator.regexps.password
      },
      'passwordForm[password]': {
        required: true,
        regexp: validator.regexps.password,
        different: '[name="passwordForm[oldpassword]"]'
      },
      'passwordForm[repassword]': {
        required: true,
        equalTo: '[name="passwordForm[password]"]'
      }
    },
    messages: {
      'passwordForm[oldpassword]': {
        required: '请输入6-16个字符，密码需字母和数字组合',
        regexp: '请输入6-16个字符，密码需字母和数字组合'
      },
      'passwordForm[password]': {
        required: '请输入6-16个字符，密码需字母和数字组合',
        regexp: '请输入6-16个字符，密码需字母和数字组合',
        different: '新密码不能和旧密码相同'
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

  exports.refresh = function(logoutUrl) {
    var $tim = $('#setouttime');
    var time = $tim.text();
    setInterval(function countdowns() {
      time--;
      $tim.text(time);
      if (time === 0) {
        location.href = logoutUrl || '/logout.html?to=login';
      }
    }, 1000);
  };
});