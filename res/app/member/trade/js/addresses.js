define(function(require, exports, module) {
  require('modules/area/css/style.css');
  var area = require('modules/area/js/area.js');
  var validator = require('modules/form/js/validator.js');
  var $form = $('.addresses form');
  var $addressList = $('.address-list');
  var dialog = require('modules/dialog/js/dialog.js');
  var $addresses = $('.list-page-body');

  function areaSelectInit() {
    area.select($('.area-select'), {
      realField: '[name="MemberAddress[areaId]"]',
      'default': $form.find('[name="MemberAddress[areaId]"]').val()
    });
  }
  areaSelectInit();

  $form.validate({
    rules: {
      'MemberAddress[name]': {
        required: true,
        rangelength: [2, 10]
      },
      'MemberAddress[address]': {
        required: true,
        rangelength: [2, 20]
      },
      'MemberAddress[mobile]': {
        required: true,
        regexp: validator.regexps.mobile
      }
    },
    messages: {
      'MemberAddress[name]': {
        required: '不能为空',
        rangelength: '请输入2-10个字符'
      },
      'MemberAddress[address]': {
        required: '不能为空',
        rangelength: '请输入2-20个字符'
      },
      'MemberAddress[mobile]': {
        required: '不能为空',
        regexp: '请输入11位手机号码'
      }
    },
    submitHandler: function(form) {
      validator.formAjax($form, 'MemberAddress', true);
    }
  });
  $addressList.on('click', '.edit', function(event) {
    event.preventDefault();
    $('.frame-tab-hd a').text('编辑地址');
    $form.load('/address/update/id/' + $(this).parents('tr').data('id'), function() {
      areaSelectInit();
      $form.find('#name').trigger('focus');
    });
  }).on('click', '.del', function(event) {
    event.preventDefault();
    var $t = $(this);
    dialog.confirm('确定删除？', function() {
      $.get($t.attr('href'), function(res) {
        if (res.state) {
          location.href = location.href;
          //$t.parents('tr').remove();
        } else {
          dialog.tip(res.message || '删除失败，请稍后重试！');
        }
      }, 'json');
    });
  }).on('click', '.radio-inline', function(event) {
    event.preventDefault();
    var $t = $(this);
    var id = $t.parents('tr').data('id');
    $.get('/address/isdefault/id/' + id, function(res) {
      if (res.state) {
        $addressList.find('.radio-inline span').remove();
        $t.append('<span>默认</span>');
        $t.find(':radio').prop('checked', true);
      }
    }, 'json');
  });

});
