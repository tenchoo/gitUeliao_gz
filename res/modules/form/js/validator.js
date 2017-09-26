define(function(require, exports, module) {
  var dialog = require('modules/dialog/js/dialog.js');
  var helpClass = 'has-help has-error has-success';
  require('libs/jquery-validation/1.13.1/jquery.validate.min.js');

  function getEnLeng(str) {
    return (str || '').replace(/[^\x00-\xff]/g, "xx").length;
  }


  $.validator.addMethod('regexp', function(value, element, param) {
    return (new RegExp(param)).test(value);
  }, $.validator.format('验证不通过'));

  $.validator.addMethod('numberMobile', function(value, element, param) {
    if (/^\d+$/.test(value)) {
      return (/^1[34578][0-9]{9}$/).test(value);
    } else {
      return true;
    }
  }, $.validator.format('请输入正确的手机号码'));

  $.validator.addMethod('mobileEmail', function(value, element, param) {
    if (/^1[34578][0-9]{9}$/.test(value)) {
      return true;
    } else {
      return (/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/).test(value);
    }
  }, $.validator.format('请输入正确的手机或邮箱'));

  $.validator.addMethod('different', function(value, element, param) {
    return value !== $(param).val();
  }, $.validator.format('不能输入相同的值'));

  $.validator.addMethod('minEnLength', function(value, element, param) {
    return getEnLeng(value) >= param;
  }, $.validator.format('不得少于{0}个英文'));

  $.validator.addMethod('maxEnLength', function(value, element, param) {
    return getEnLeng(value) <= param;
  }, $.validator.format('不得多于{0}个英文'));

  $.validator.addMethod('rangeEnLength', function(value, element, param) {
    var enLength = getEnLeng(value);
    return enLength >= param[0] && enLength <= param[1];
  }, $.validator.format('在{0} - {1} 个英文之间'));

  $.validator.setDefaults({
    errorElement: 'span',
    focusInvalid: true,
    onkeyup: false,
    onfocusin: function(element) {
      var $e = $(element).removeClass(helpClass);
      var $group = $e.parents('.form-group').removeClass(helpClass);
      var helpText = $e.data('help') || '';
	  if (!helpText) return;
      var help = '<span class="help-block has-help"><i class="icon icon-info"></i>' + helpText + '</span>';
      $group.find('span.help-block').remove();
      $e.addClass('has-help');
      $group.addClass('has-help');
      appendMessage(help, $e, $group);
    },
    onfocusout: function(element) {
      var $e = $(element).removeClass(helpClass);
      var $group = $e.parents('.form-group').removeClass(helpClass);
      $group.find('span.help-block').remove();	  
      if ($e.val().length === 0) return;
	   $e.valid();
     
    },
    highlight: function(element, errorClass, validClass) {

    },
    unhighlight: function(element, errorClass, validClass) {

    },
    errorPlacement: function(error, element) {
      var $e = element.removeClass(helpClass).addClass('has-error');
      var $group = $e.parents('.form-group').removeClass(helpClass).addClass('has-error');
      $group.find('span.help-block').remove();
      appendMessage(error, $e, $group);
    },
    success: function(error, element) {
      var $e = $(element).removeClass(helpClass);
      var $group = $e.parents('.form-group').removeClass(helpClass);
      var success = '<span class="help-block has-success"><i class="icon icon-success"></i></span>';
      $group.find('span.help-block').remove();
      if ($e.is('.no-success-help')) return;
      $e.addClass('has-success');
      $group.addClass('has-success');
      appendMessage(success, $e, $group);
      if ($e.is('[data-ajax]')) {
        $e.trigger('ajax');
      }
    },
    showErrors: function(errorMap, errorList) {
      for (var i = 0; i < errorList.length; i++) {		
        errorList[i].message = '<i class="icon icon-error"></i>' + errorList[i].message;
      }
      this.defaultShowErrors();
    },
    errorClass: 'help-block has-error'
  });

  function appendMessage(message, $e, $group) {
    if ($group.find('.help-after-this').length > 0) return $group.find('.help-after-this').after(message);
    if ($e.is('.append-help,:checkbox,:radio')) return $group.append(message);
    $e.after(message);
  }

  function showError($e, message) {
    var $group = $e.removeClass(helpClass).addClass('has-error').parents('.form-group').removeClass(helpClass).addClass('has-error');
    var error = '<span class="help-block has-error"><i class="icon icon-error"></i>' + (message || '验证不通过') + '</span>';
    $group.find('span.help-block').remove();
    appendMessage(error, $e, $group);
  }

  function formAjaxError(data, form, jump) {
    if (data.data) {
      $.each(data.data, function(k, v) {
        showError($('[name="' + form + '[' + k + ']"]'), v);
      });
      return;
    }
    if (data.message) {
      dialog.tip(data.message, {
        type: 'error',
        cb: function() {
          if (jump !== false) {
            location.href = data.data || location.href;
          }
        }
      });
    }
  }

  function button($form, state) {
    var $button = $form.find('button[data-loading]');
    switch (state) {
      case 'reset':
        $button.prop('disabled', false).removeClass('btn-disabled').text($button.data('default')).trigger('reset');
        break;
      default:
        $button.prop('disabled', true).addClass('btn-disabled').data('default', $button.text()).text($button.data('loading'));
    }
  }

  exports.button = button;

  exports.showError = showError;

  exports.hideError = function($e) {
    $e.removeClass('has-error').parents('.form-group').removeClass('has-error').find('span.help-block').remove();
  };

  exports.showSuccess = function($e, message) {
    var $group = $e.removeClass(helpClass).parents('.form-group').removeClass(helpClass);
    var success = '<span class="help-block has-success"><i class="icon icon-success"></i>' + (message || '') + '</span>';
    $group.find('span.help-block').remove();
    if ($e.is('.no-success-help')) return;
    $e.addClass('has-success');
    $group.addClass('has-success');
    appendMessage(success, $e, $group);
  };

  exports.showErrors = function($form) {
    $form.find('[data-error][data-error!=""]').each(function() {
      var $t = $(this);
      showError($t, $t.data('error'));
    });
  };

  exports.formAjax = function($form, form, wait) {
    if ($form.data('submit')) return;
    $form.data('submit', true);
    button($form);
    $.post($form.attr('action'), $form.serializeArray(), function(res) {
      $form.data('submit', false);
      if (res.state) {
        if (!wait) {
          location.href = res.data || location.href;
          return;
        }
        dialog.tip(res.message || '操作成功', {
          type: 'success',
          cb: function() {
            location.href = res.data || location.href;
          }
        });
        return;
      }
      button($form, 'reset');
      formAjaxError(res, form);
    }, 'json');
  };

  exports.regexps = {
    mobile: /^1[34578][0-9]{9}$/,
    idCard: /^[1-9](\d{14}|\d{16}[\dx])$/i,
    email: /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/,
    verifyCode: /^\d{6}$/,
    password: /^[\da-z]{6,16}$/i,
    qq: /^[1-9]\d{4,14}$/,
    captcha: /^[a-z]{4}$/i,
    postalcode: /^[1-9]\d{5}$/
  };

  exports.formAjaxError = formAjaxError;

  exports.sendVerifyCode = function($btn, options) {

    options = $.extend({
      field: '',
      fieldSuccess: false,
      form: '',
      leftTime: 60,
      postData: {},
      help: '2分钟内有效'
    }, options || {});
    $.extend(options.postData, { action: 'sendcode' });
    if (options.field) {
      if (options.field.is('.has-error')) return;
      if (options.fieldSuccess && options.field.is(':not(.has-success)')) {
        showError(options.field, '不能为空');
        return;
      }
    }
    if ($btn.data('posting')) return;
    $btn.data('posting', true);
    var leftTime = options.leftTime;
    var timer;
    $.post(seajs.data.apiPath + '/ajax', options.postData, function(res) {
      $btn.data('posting', false);
      if (res.state) {
        $btn.addClass('btn-disabled').prop('disabled', true).html('<strong>' + leftTime + '</strong> 秒后重新发').after('<span class="expired">' + options.help + '</span>');
        timer = setInterval(function() {
          if (leftTime === 0) {
            clearInterval(timer);
            $btn.html('重新发送验证码').removeClass('btn-disabled').prop('disabled', false).next('.expired').remove();
            return;
          }
          leftTime--;
          $btn.find('strong').text(leftTime);
        }, 1000);
        return;
      }
      formAjaxError(res, options.form);
    }, 'jsonp');
  };
});
