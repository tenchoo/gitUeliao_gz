define(function(require, exports, module) {
  var template = require('libs/arttemplate/3.0.0/template.js');
  var area = require('modules/area/js/area.js');
  var areaCheckbox = area.checkbox();
  var dialog = require('modules/dialog/js/dialog.js');
  var validator = require('modules/form/js/validator.js');

  var $areaSelect = $('.area-select');
  var $form = $('.freight-form');
  var $freightMethod = $('.freight-method');
  var units = {
    '0': {
      way: '件',
      unit: '件'
    },
    '1': {
      way: '重量',
      unit: 'kg'
    },
    '2': {
      way: '体积',
      unit: 'm<sup>3</sup>'
    }
  };

  template.config('escape', false);

  function clearFreightmethod(priceWay) {
    var $li = $freightMethod.find('li').removeClass('active');
    $li.find(':checkbox').prop('checked', false);
    $li.find('.freight-template').remove();
    $li.each(function(i) {
      $(this).append(template('freightTemplate', $.extend({
        type: i
      }, units[priceWay])));
    });
  }

  $form.on('click', 'td .del', function(event) {
    event.preventDefault();
    $(this).parents('tr').remove();
  }).on('click', '.freight-foot .add', function(event) {
    event.preventDefault();
    var $li = $(this).parents('li');
    $li.find('tbody').append(template('freightItem', {
      type: $li.find(':checkbox').val(),
      i: (new Date()).getTime()
    }));
  }).on('change', '.freight-method :checkbox', function(event) {
    var $t = $(this);
    var $li = $t.parents('li');
    if ($t.prop('checked')) {
      $li.addClass('active');
    } else {
      $li.removeClass('active');
    }

  }).on('click', '.price-way label', function(event) {
    var $radio = $(this).find(':radio');
    if ($form.find('.has-method :checked').length === 0) {
      $form.find('.unit').html(units[$radio.val()].unit);
      return;
    }
    event.preventDefault();
    dialog.confirm('切换计价方式后，所设置当前模板的运输信息将被清空，确定继续么？', function() {
      $radio.prop('checked', true);
      clearFreightmethod($radio.val());
    });
  }).on('click', '.edit-areas', function(event) {
    var $td = $(this).parent();
    var $areas = $td.find('input');
    event.preventDefault();
    areaCheckbox.open({
      'default': $areas.val(),
      okFun: function(areasArray, areasTextArray) {
        $areas.val(areasArray.join());
        $td.find('.areas-text').text(areasTextArray.join());
      }
    });
  }).validate({
    rules: {
      'Express[title]': {
        required: true,
        maxlength: 20
      },
      'county': {
        regexp: /^\d+$/
      },
      'ExpressLog[type][]': {
        required: true
      }
    },
    messages: {
      'Express[title]': {
        required: '不能为空',
        maxlength: '模板名称不能超过20个字'
      },
      'county': {
        regexp: '请选择地址'
      },
      'ExpressLog[type][]': {
        required: '请选择运送方式'
      }
    },
    submitHandler: function(form) {
      validator.formAjax($form, 'Express', true);
    }
  });

  area.select($areaSelect, {
    'default': $areaSelect.find('[name="Express[areaId]"]').val(),
    success: function(areaId) {
      $areaSelect.find('[name="Express[areaId]"]').val(areaId);
    }
  });

});