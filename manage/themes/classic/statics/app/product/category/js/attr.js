define(function(require, exports, module) {
  var categoryId = $('#categoryId').val();
  var template = require('libs/arttemplate/3.0.0/template.js');
  var $content = $('.content-wrap');
  var getCheckds = require('modules/checkedall/js/checkedall.js')($content, {
    list: 'tbody .title :checkbox:not(disabled)',
    async: true
  });
  var $list = $content.find('.table tbody');
  var hasNewAttr = 0;
  var dialog = window;

  function updateList() {

    $list.find('.dis').removeClass('dis');
    $list.find('tr:first-child .glyphicon-arrow-up').addClass('dis');
    $list.find('tr:last-child .glyphicon-arrow-down').addClass('dis');
  }

  $content.on('click', '[data-templateid]', function(event) {
    event.preventDefault();
    $list.append(template($(this).data('templateid')));
    $list.find('tr:last .form-control:first').trigger('focus');
    updateList();
    hasNewAttr += 1;
  }).on('click', '.glyphicon-arrow-up:not(.dis)', function(event) {
    event.preventDefault();
    var $tr = $(this).parents('tr');
    $.post('/category/attr/move', {
      categoryId: categoryId,
      attributeId: $tr.find('[name="extendids[]"]').val(),
      'goto': 'up'
    }, function(res) {
      if (res.state) {
        $tr.insertBefore($tr.prev());
        updateList();
      }
    }, 'json');

  }).on('click', '.glyphicon-arrow-down:not(.dis)', function(event) {
    event.preventDefault();
    $(this).parents('tr').next().find('.glyphicon-arrow-up').trigger('click');
  }).on('click', '.del', function(event) {
    event.preventDefault();
    var $t = $(this);
    var $tr = $t.parents('tr');
    var $form = $tr.find('form');

    if ($form.data('update')) {
      hasNewAttr -= 1;
      $tr.fadeOut(function() {
        $tr.remove();
      });
      return;
    }
    $.get($t.attr('href'), function(res) {
      if (res.state) {
        $tr.fadeOut(function() {
          $tr.remove();
        });
      }
    }, 'json');
  }).on('click', 'button.save', function(event) {
    event.preventDefault();
    var $t = $(this);
    var $checkeds = getCheckds();
    var ids = [];
    var $trs = $checkeds.parents('tr');
    if ($checkeds.length < 1) {
      return dialog.alert('请选择数据后操作');
    }
    if (!confirm('确定要继承到所有子分类，如果继承，原有子分类\n的属性将会被现有属性覆盖。')) return;

    $checkeds.each(function() {
      ids.push($(this).val());
    });

    $.post('/category/attr/extend', {
      categoryId: categoryId,
      extendids: ids
    }, function(res) {
      if (res.state) {
        $t.prev().trigger('click');
      }
    }, 'json');

  }).on('change', 'td input:not([name="extendids[]"]),td select', function() {
    var $tr = $(this).parents('tr');
    var $form = $tr.find('form');
    if (!$form.data('update')) {
      $form.trigger('submit');
    }
  }).on('click', 'a.save', function(event) {
    event.preventDefault();
    $(this).parents('tr').find('form').trigger('submit');
  }).on('submit', 'tr form', function(event) {
    event.preventDefault();
    var $t = $(this);
    var $tr = $t.parent();
    if ($t.is('[data-update]')) {
      $t.empty().html($tr.find('input').clone());
      $t.append('<input type="hidden" name="form[type]" value="' + $tr.find('[name="form[type]"]').val() + '" />');
      $t.append('<input type="hidden" name="form[setGroupId]" value="' + $tr.find('[name="form[setGroupId]"]').val() + '" />');
    }
    $.post($t.attr('action'), $t.serializeArray(), function(res) {
      if (res.state) {
        $tr.removeClass('has-error');
        if ($t.data('update')) {
          $t.data('update', false);
          hasNewAttr -= 1;
          $tr.find('.save').remove();
          $tr.find(':disabled').prop('disabled', false).val(res.data.attributeId).after('<input type="hidden" name="form[attributeId]" value="' + res.data.attributeId + '">');
        }
        return;
      }
      alert(res.message || '操作失败');
      hasNewAttr += 1;
      $tr.addClass('has-error');
    }, 'json');
  });

  $(window).on('beforeunload', function() {
    if (hasNewAttr > 0) return '没有保存的属性将丢失';
  });

});
