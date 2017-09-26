define(function(require, exports, module) {
  var validator = require('modules/form/js/validator.js');
  var $form = $('form');
  var $messageBox = $form.find('.message-box');
  var $captcha = $form.find('img.refresh');
  var captcha = $captcha.attr('src');


  function showError(message, id) {
    $messageBox.show().html('<span id="LoginForm[' + id + ']-error" class="has-error">' + message + '</span>');
  }

  function refresh(event) {
    $captcha.attr('src', captcha + '?t=' + (new Date()).getTime());
  }

  $form.on('click', '.refresh', refresh).validate({
    rules: {
      'LoginForm[username]': {
        required: true
      },
      'LoginForm[password]': {
        required: true,
        regexp: validator.regexps.password
      },
      'LoginForm[verifyCode]': {
        required: true,
        regexp: validator.regexps.captcha
      }
    },
    messages: {
      'LoginForm[username]': {
        required: '账号不能为空'
      },
      'LoginForm[password]': {
        required: '密码不能为空',
        regexp: '用户名或密码不正确'
      },
      'LoginForm[verifyCode]': {
        required: '验证码不能为空',
        regexp: '验证码不正确，请重新输入'
      }
    },
    onfocusin: false,
    onfocusout: false,
    errorLabelContainer: ".message-box",
    showErrors: function(errorMap, errorList) {
      $messageBox.empty();
      if (errorList.length > 1) {
        for (var i = 0; i < errorList.length; i++) {
          errorList.pop();
        }
      }
      this.defaultShowErrors();
    },
    errorClass: 'has-error',
    submitHandler: function(form) {
      if ($form.data('submit')) return;
      $form.data('submit', true);
      validator.button($form);
      $.post($form.attr('action'), $form.serializeArray(), function(res) {
        if (res.state) {
          location.href = res.data;
          return;
        }
        validator.button($form, 'reset');
        $form.data('submit', false);
        refresh();
        if (res.data.verifyCode) {
          showError(res.data.verifyCode, 'verifyCode');
          return;
        }
        showError(res.data.password || res.data.username, 'username');
      }, 'json');
    }
  });
});
