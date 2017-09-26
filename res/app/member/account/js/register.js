define(function(require, exports, module) {
  var validator = require('modules/form/js/validator.js');
  var $form = $('form');
  var $account = $('#account');
  var $submit = $form.find('.btn-warning');
  var $strength = $('.password-strength');
  var accounts = {
    exist: [],
    noexist: []
  };
  var companynames = {
    exist: [],
    noexist: []
  };
  var area = require('modules/area/js/area.js');
  var $area = $('.area div');
  area.select($area, {
    success: function(data) {
      $area.find('input').val(data);
    }
  });
  $form.on('change', '[name="RegForm[agree]"]', function() {
    if (this.checked) {
      $submit.removeClass('btn-disabled').prop('disabled', false);
    } else {
      $submit.addClass('btn-disabled').prop('disabled', true);
    }
  }).on('keyup', '[name="RegForm[password]"]', function() {
    var value = $(this).val();
    var length = value.length;
    var isEng = /^[a-z]+$/i.test(value);
    var isNumber = /^\d+$/.test(value);
    var level = 2;
    if (isEng || isNumber) {
      if (length < 11) {
        level = 1;
      }
    } else {
      if (length > 10) {
        level = 3;
      }
    }
    if (length < 6 || length > 16) {
      level = 0;
    }
    $strength.removeClass('password-level0 password-level1 password-level2 password-level3').addClass('password-level' + level);
  }).on('click', '.send-code', function() {
    validator.sendVerifyCode($(this), {
      field: $account,
      fieldSuccess: true,
      postData: {
        account: $account.val(),
        optype: 'reg'
      },
      form: 'RegForm'
    });
  }).on('ajax', '[name="RegForm[companyname]"]', function() {
    var $companyname = $(this);
    var companyname = $companyname.val();
    var msg = '您输入的手机号码已注册';
    if ($.inArray(companyname, companynames.noexist) > -1) return;
    if ($.inArray(companyname, companynames.exist) > -1) return validator.showError($companyname, msg);
    $.get($companyname.data('ajax'), {
      companyname: companyname
    }, function(res) {
      if (res.state) {
        companynames.noexist.push(companyname);
        validator.showSuccess($companyname);
        return;
      }
      companynames.exist.push(companyname);
      validator.showError($companyname, res.message||msg);
    }, 'json');
  }).on('ajax', '[name="RegForm[account]"]', function() {
    var $account = $(this);
    var account = $account.val();
    var msg = '您输入的手机号码已注册';
    if ($.inArray(account, accounts.noexist) > -1) return;
    if ($.inArray(account, accounts.exist) > -1) return validator.showError($account, msg);
    $.post($account.data('ajax'), {
      account: account
    }, function(res) {
      if (res.state) {
        accounts.noexist.push(account);
        validator.showSuccess($account);
        return;
      }
      accounts.exist.push(account);
      validator.showError($account, res.message||msg);
    }, 'json');
  }).validate({
    rules: {
      'RegForm[companyname]': {
        required: true
      },
      'RegForm[areaId]': {
        required: true
      },
      'RegForm[address]': {
        required: true
      },
      'RegForm[contactPerson]': {
        required: true
      },
      'RegForm[account]': {
        required: true,
        regexp: validator.regexps.mobile
      },
      'RegForm[password]': {
        required: true,
        regexp: validator.regexps.password
      },
      'RegForm[repassword]': {
        required: true,
        equalTo: '[name="RegForm[password]"]'
      },
      'RegForm[verifyCode]': {
        required: true,
        regexp: validator.regexps.verifyCode
      }
    },
    messages: {
      'RegForm[companyname]': {
        required: '不能为空'
      },
      'RegForm[areaId]': {
        required: '不能为空'
      },
      'RegForm[address]': {
        required: '不能为空'
      },
      'RegForm[contactPerson]': {
        required: '不能为空'
      },
      'RegForm[account]': {
        required: '不能为空',
        regexp: '请输入11位手机号码'
      },
      'RegForm[password]': {
        required: '请输入6-16个字符，密码需字母和数字组合',
        regexp: '请输入6-16个字符，密码需字母和数字组合'
      },
      'RegForm[repassword]': {
        required: '请再次输入密码',
        equalTo: '两次输入的密码不一致'
      },
      'RegForm[verifyCode]': {
        required: '请输入验证码',
        regexp: '验证码不正确，请重新输入'
      }
    },
    submitHandler: function(form) {
      validator.formAjax($form, 'RegForm');
    }
  });
  validator.showErrors($form);
});