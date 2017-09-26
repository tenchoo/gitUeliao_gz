define(function(require, exports, module) {
  var validator = require('modules/form/js/validator.js');
  var $form = $('.paypassword form');
  var $captcha = $form.find('img.refreshcode');
  var captcha = $captcha.attr('src');

  function refreshcode() {
    $captcha.attr('src', captcha + '?t=' + (new Date()).getTime());
  }

  $form
    .on('reset', '.btn', refreshcode)
    .on('click', '.refreshcode', refreshcode)
    .validate({
      rules: {
        'passwordForm[paypassword]': {
          required: true,
          regexp: validator.regexps.password
        },
        'passwordForm[repassword]': {
          required: true,
          equalTo: '[name="passwordForm[paypassword]"]'
        },
        'passwordForm[verifyCode]': {
          required: true,
          regexp: validator.regexps.captcha
        }
      },
      messages: {
        'passwordForm[paypassword]': {
          required: '请输入6-16个字符，密码需字母和数字组合',
          regexp: '请输入6-16个字符，密码需字母和数字组合'
        },
        'passwordForm[repassword]': {
          required: '请再次输入密码',
          equalTo: '两次输入的密码不一致'
        },
        'passwordForm[verifyCode]': {
          required: '请输入验证码',
          regexp: '验证码不正确，请重新输入'
        }
      },
      submitHandler: function(form) {
        validator.formAjax($form, 'passwordForm');
      }
    });

  exports.refresh = function() {
    var $tim = $('#setouttime');
    var time = $tim.text();
    setInterval(function countdowns() {
      time--;
      $tim.text(time);
      if (time === 0) {
        location.href = 'http://member.base.com/membercenter/phonechange';
      }
    }, 1000);
  };
});
