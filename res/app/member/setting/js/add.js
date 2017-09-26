define(function(require, exports, module) {
  var validator = require('modules/form/js/validator.js');
  var $form = $('.frame-content form');
  var area = require('modules/area/js/area.js');
  var $area = $('.area div');
  area.select($area, {
    success: function(data) {
      $area.find('input').val(data);
    }
  });

  $form.validate({
    rules: {
      'info[companyname]': {
        required: true
      },
      'info[areaId]': {
        required: true
      },
      'info[address]': {
        required: true
      },
      'info[contactPerson]': {
        required: true
      },
      'info[phone]': {
        required: true,
        regexp: validator.regexps.mobile
      },
      'info[password]': {
        required: true,
        regexp: validator.regexps.password
      },
      'info[repassword]': {
        required: true,
        equalTo: '[name="info[password]"]'
      }
    },
    messages: {
      'info[companyname]': {
        required: '不能为空'
      },
      'info[areaId]': {
        required: '不能为空'
      },
      'info[address]': {
        required: '不能为空'
      },
      'info[contactPerson]': {
        required: '不能为空'
      },
      'info[phone]': {
        required: '不能为空',
        regexp: '请输入11位手机号码'
      },
      'info[password]': {
        required: '请输入6-16个字符，密码需字母和数字组合',
        regexp: '请输入6-16个字符，密码需字母和数字组合'
      },
      'info[repassword]': {
        required: '请再次输入密码',
        equalTo: '两次输入的密码不一致'
      }
    },
    submitHandler: function(form) {
      validator.formAjax($form, 'info', true);
    }
  });
});