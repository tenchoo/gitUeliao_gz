define(function(require, exports, module) {
  require('modules/editor/js/editor.js')();
  require('libs/my97datepicker/4.8.0/WdatePicker.js');
  var validator = require('modules/form/js/validator.js');
  var template = require('libs/arttemplate/3.0.0/template.js');
  var dialog = require('modules/dialog/js/dialog.js');
  template.config('escape', false);
  var $uploader;
  var uploader = require('modules/uploader/js/uploader.js').uploader({
    success: function(file, res) {
      $uploader = $('#rt_' + file.source.ruid).parents('.uploader');
      $uploader.find('button').html('<img src="' + seajs.data.uploaderPath + '/../..' + res.data + '" alt="" width="80" height="80"><span class="bg"></span><span>重新上传</span>');
      if ($uploader.is('.main-image')) {
        $uploader.find('[name="product[pictures][]"]').val(res.data);
      }
      if ($uploader.is('.spec-image')) {
        $uploader.find('input.picture').val(res.data);
        $uploader.parent('li').data('picture', res.data);
      }
    }
  });

  uploader.on('uploadStart', function(file) {
    var $file = $('#rt_' + file.source.ruid).parents('.uploader');
    uploader.option('formData', {
      'case': $file.is('.main-image') ? 'product' : 'res'
    });
  });

  var $form = $('.create-info form');

  var $hasSpec = $('#hasSpec');
  var hasSpec = $hasSpec.length;
  var specLength = $hasSpec.find('.form-group').length;

  var $colorsImage = $form.find('.colors-image ul');

  var unit = $('[name="product[unitType]"] :selected').text();

  var $tran = $('#tran');

  var $publicTime = $form.find('[name="product[publicTime]"]');

  var $state = $form.find('[name="product[state]"]');
  /*
    交易信息代码
    三位字符串数字
    第一位 是否有规格
    第二位 批发或零售
    第三位 报价方式
   */

  function updatePriceAdd($table) {
    var $tfoot = $table.find('tfoot');
    updatePricePreview();
    if ($table.find('tbody tr').length < 3) {
      $tfoot.removeClass('hide');
      return;
    }
    $tfoot.addClass('hide');
  }

  function updatePricePreview() {
    var list = [];
    var $pricePreview = $form.find('.price-preview ul');
    $form.find('.prices-table tbody tr').each(function() {
      var $t = $(this);
      var number = $t.find('input:first').val();
      var price = $t.find('input:last').val();
      if (number && price) {
        list.push({
          number: number,
          price: price
        });
      }
    });
    if (list.length === 0) return $pricePreview.html('');
    $pricePreview.html(template('pricePreview', {
      list: list,
      unit: unit
    }));
  }

  function getTranCode(all) {
    return '' + hasSpec + $form.find('[name="product[publishType]"]:checked').val() + (all ? ($form.find('[name="product[quotedMethod]"]:checked').val() || '0') : '');
  }

  function renderTran(code) {
    $tran.html(template('tran' + code, {
      unit: unit
    }));
    if (hasSpec) {
      renderNumbersTable(code + '1');
    }
  }

  function renderBatch(code) {
    $('#batch').html(template('tran' + code, {
      unit: unit
    }));
    if (hasSpec) {
      renderNumbersTable(code);
    }
  }

  function createTitle(code) {
    var titles = [];
    $hasSpec.find('.block-control .control-label').each(function() {
      titles.push($(this).text().replace('：', ''));
    });
    return template('numbersTitle', {
      list: titles
    }) + (code !== '111' ? template('priceTitle', {}) : '');
  }

  function createList(code) {
    var list = [];
    var realList = [];
    $hasSpec.find('.form-group').each(function(i) {
      var $group = $(this);
      list.push([]);
      $group.find(':checkbox').each(function() {
        var $t = $(this);
        list[i].push(template('specItem', {
          group: $group.data('group'),
          value: $t.val(),
          text: $.trim($t.parents('li').text()),
          show: '' + $t.prop('checked')
        }));
      });
    });
    if (specLength === 1) {
      realList = list[0];
    } else if (specLength === 2) {
      for (var i = 0; i < list[0].length; i++) {
        for (var j = 0; j < list[1].length; j++) {
          realList.push(list[0][i] + list[1][j]);
        }
      }
    }
    if (code !== '111') {
      for (var k = 0; k < realList.length; k++) {
        realList[k] += template('specPriceItem', {});
      }
    }
    return realList;
  }

  function renderNumbersTable(code) {
    $('#table').html(template('numbersTable', {
      title: createTitle(code),
      list: createList(code)
    }));
    updateNumbersTable();
  }

  function updateNumbersTable() {
    var $table = $('#table');
    var specShowLength = 0;
    $hasSpec.find('.form-group').each(function() {
      var $t = $(this);

      if ($t.find(':checkbox:checked').length > 0) {
        specShowLength += 1;
        $table.find('th[data-spec-group="' + $t.index() + '"]').removeClass('hide');
      } else {
        $table.find('th[data-spec-group="' + $t.index() + '"]').addClass('hide');
      }
    });
    $table.find('td[data-show="true"]').removeClass('hide');
    $table.find('td[data-show="false"]').addClass('hide');
    $table.find('tbody tr').each(function() {
      var $t = $(this);
      var $td = $t.find('td[data-show="true"]');
      if (specShowLength > 0 && specShowLength === $td.length) {
        $t.removeClass('hide');
      } else {
        $t.addClass('hide');
      }
    });
    if (specShowLength === 0) {
      $table.find('th').removeClass('hide');
    }
    if (specShowLength === 1) {
      $table.find('td[data-show="true"]').each(function() {
        var spec = $(this).data('spec');
        var $spec = $table.find('td[data-spec="' + spec + '"]');
        if ($spec.length > 1) {
          $spec.filter(':gt(0)').parent().addClass('hide');
        }
      });
    }
    createSpecData();
    $table.trigger('count');
  }

  function toggleColorImgTip() {
    if ($colorsImage.find('li:not(.hide)').length) return $colorsImage.prev('span.muted').addClass('hide');
    $colorsImage.prev('span.muted').removeClass('hide');
  }

  renderTran(getTranCode());

  function renderColorsImage() {
    var colors = [];
    $hasSpec.find('.colors-cate :checkbox').each(function() {
      var $t = $(this);
      colors.push({
        spec: $t.val(),
        color: $.trim($t.siblings('.t').text())
      });
    });
    $colorsImage.append(template('colorItems', {
      list: colors
    }));
  }

  if (hasSpec) {
    renderColorsImage();
  }


  function createSpecData() {
    var list = [];

    $('#table').find('tbody tr:visible').each(function(i) {
      var $tr = $(this);
      var item = {};
      var relation = [];
      $tr.find('td:visible input').each(function() {
        var $t = $(this);
        item[$t.data('name')] = $t.val();
      });
      $tr.find('td[data-show="true"]').each(function() {
        var $t = $(this);
        relation.push($t.data('specGroup') + ':' + $t.data('spec'));
      });
      item.price = item.price || 0;
      item.relation = relation.join(',');

      $tr.attr('data-relation', item.relation);

      list.push(item);
    });

    $('.spec-data').html(template('specData', {
      list: list
    }));

  }


  $form.on('count', '#table', function(event) {
    var count = 0;
    $(this).find('[data-name="total"]:visible').each(function() {
      count += (parseInt(this.value, 10) || 0);
    });
    $form.find('.count-total').text(count);
  }).on('click', '.sale-way,.quoted-way', function(event) {
    var $t = $(this);
    var $radio = $t.find(':radio');
    if ($radio.prop('checked')) return;
    event.preventDefault();
    dialog.confirm('切换' + ($t.is('.sale-way') ? '销售类型' : '报价方式') + '后，当前报价信息将被清空，确定继续么？', function() {
      $radio.prop('checked', true).trigger('change');
    });
  }).on('blur', '.colors-cate .input-xs', function(event, update) {
    var $t = $(this);
    var color = $t.val();
    var $label = $t.prev();
    var spec = '[data-spec="' + $label.find('input').val() + '"]';
    var $li = $form.find('li' + spec);
    if (update || color !== this.defaultValue) {
      $label.find('.t').text(color);
      $form.find('td' + spec).text(color);
      $li.find('.color').text(color);
    }
  }).on('blur', '#hasSpec .input-xs', function(event) {
    var $t = $(this);
    var val = $t.val();
    var defaultValue = this.defaultValue;
    if (val === '') return $t.val(defaultValue).trigger('blur', [true]);
  }).on('blur', '#table input[data-name="total"]', function(event) {
    var $t = $(this);
    var val = $t.val().replace(/\D/g, '');
    $t.val(val).parents('#table').trigger('count');
  }).on('change', '#hasSpec :checkbox', function() {
    var $t = $(this);
    var checked = $t.prop('checked');
    var $span = $t.siblings('.t');
    var $input = $span.parent().next();
    var isColor = $t.parents('.colors-cate').length;
    var spec = $t.val();
    var $colorImage;
    var $table = $('#table');
    $table.find('td[data-spec="' + spec + '"]').attr('data-show', checked);
    updateNumbersTable();
    if (checked) {
      $input.removeClass('hide').prop('disabled', false);
      $span.addClass('hide');

      if (isColor) {
        $colorImage = $colorsImage.find('li[data-spec="' + spec + '"]').removeClass('hide');
        $colorImage.find('.picture').prop('disabled', false);
        if (!$colorImage.data('upload')) {
          uploader.addButton({
            id: $colorImage.find('.uploader-image'),
            multiple: false
          });
          $colorImage.data('upload', true);
        }
        toggleColorImgTip();
      }
      return;
    }
    $span.removeClass('hide');
    $input.addClass('hide').prop('disabled', true);
    if (isColor) {
      $colorImage = $colorsImage.find('li[data-spec="' + spec + '"]').addClass('hide');
      $colorImage.find('.picture').prop('disabled', true);
      toggleColorImgTip();
    }
  }).on('change', '[name="product[publishType]"]', function() {
    renderTran(getTranCode());
  }).on('change', '[name="product[quotedMethod]"]', function() {
    renderBatch(getTranCode(true));
  }).on('change', '[name="product[unitType]"]', function() {
    unit = $(this).find(':selected').text();
    $form.find('.unit').text(unit);
  }).on('change', '.prices-table input', updatePricePreview).on('blur', '.numonly', function() {
    var $t = $(this);
    $t.val($t.val().replace(/\D/g, ''));
  }).on('click', '.add', function(event) {
    event.preventDefault();
    var $table = $(this).parents('table');
    var $tbody = $table.find('tbody');
    $tbody.append(template('priceItem', {
      unit: unit
    }));
    updatePriceAdd($table);
  }).on('click', '.del', function(event) {
    event.preventDefault();
    var $tr = $(this).parents('tr');
    var $table = $tr.parents('table');
    $tr.remove();
    updatePriceAdd($table);
  }).on('change', '.categorys-list input', function(event) {
    var $t = $(this);
    var checked = $t.prop('checked');
    var $ul = $t.parents('ul:first');
    var $cate1 = $ul.prev().find('input');
    if ($t.is('.cate1')) {
      $t.parents('li').find('ul input').prop('checked', checked);
      return;
    }
    if (!checked || $ul.find('input:not(:checked)').length) {
      $cate1.prop('checked', false);
    } else {
      $cate1.prop('checked', true);
    }
  }).on('change', '[name="product[timeType]"]', function(event) {
    if ($(this).val() === '2') {
      $publicTime.removeClass('input-disabled').prop('disabled', false);
      return;
    }
    $publicTime.addClass('input-disabled').prop('disabled', true);
  }).on('click', '[name="product[publicTime]"]', function(event) {
    WdatePicker({
      minDate: '%y-%M-{%d+1} %H:%m:%s',
      maxDate: '%y-%M-{%d+7} %H:%m:%s',
      dateFmt: 'yyyy-MM-dd HH:mm:ss'
    });
  }).on('click', '.btn-preview', function(event) {
    $form.trigger('submit', [1]);
  }).on('click', '.btn-draft', function(event) {
    $form.trigger('submit', [2]);
  }).on('submit', function(event, state) {
    createSpecData();
    $state.val(state || 0);
  }).validate({
    rules: {
      'product[productName]': {
        required: true
      },
      'product[productNum]': {
        required: true
      }
    },
    messages: {
      'product[productName]': {
        required: '不能为空'
      },
      'product[productNum]': {
        required: '不能为空'
      }
    },
    submitHandler: function(form) {
      validator.formAjax($form, 'product');
    }
  });


  $(function() {
    if (window.specPrice) {
      $form.find('.colors-cate :checked').trigger('change');
      var $table = $('#table');
      $.each(specPrice, function(i, v) {
        var $tr = $table.find('tr:visible[data-relation="' + v.relation + '"]');
        $tr.find('input').each(function() {
          var $t = $(this);
          $t.val(v[$t.data('name')]);
        });
      });
      $.each(specPicture, function(i, v) {
        var $li = $colorsImage.find('li:visible[data-spec="' + i + '"]');
        console.log($li);
        $li.find('.picture').val(v);
        $li.find('button').html('<img src="' + seajs.data.uploaderPath + '../../..' + v + '" alt="" width="80" height="80"><span class="bg"></span><span>重新上传</span>');
      });
    }
    if (window.priceRange) {
      var $pricesTable = $form.find('.prices-table');
      if (priceRange.price.length > 1) {
        $pricesTable.find('.add').trigger('click');
        if (priceRange.price.length === 3) {
          $pricesTable.find('.add').trigger('click');
        }
      }

      $.each(priceRange.price, function(i, v) {
        var $tr = $pricesTable.find('tbody tr:eq(' + i + ')');
        $tr.find('input:last').val(v);
        $tr.find('input:first').val(priceRange.miniMun[i]);
      });
    }
  });

});