define(function(require, exports, module) {
  var categoryId = $('#categoryId').val();
  var template = require('libs/arttemplate/3.0.0/template.js');
  var $content = $('.content-wrap');
  var getCheckds = require('modules/checkedall/js/checkedall.js')($content, {
    list: 'tbody :checkbox',
    async: true
  });
  var $list = $content.find('.table tbody');
  var $addForm = $content.find('form.add');
  var $addBtn = $addForm.find('button');
  var $spec = $addBtn.prev();

  var dialog = window;

  function toggleBtn() {
    $addBtn.prop('disabled', $list.find('tr').length > 1);
  }
  toggleBtn();

  $addForm.on('submit', function(event) {
    event.preventDefault();
    var specId = $spec.val();
    var name = $spec.find(':selected').text();
    if ($list.find('input[value="' + specId + '"]').length) return alert(name + ' 已添加');
    $.post($addForm.attr('action'), $addForm.serializeArray(), function(res) {
      if (res.state) {
        $list.append(template($addForm.data('templateid'), {
          id: specId,
          name: name
        }));
        toggleBtn();
        return;
      }
      alert('添加失败');
    }, 'json');

  });

  $content.on('click', '.del', function(event) {
    event.preventDefault();
    var $t = $(this);
    var $tr = $t.parents('tr');
    $.get($t.attr('href'), function(res) {
      if (res.state) {
        $tr.fadeOut(function() {
          $tr.remove();
        });
        $addBtn.prop('disabled', false);
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
    if (!confirm('确定要继承到所有子分类，如果继承，原有子分类\n的规格将会被现有规格覆盖。')) return;

    $checkeds.each(function() {
      ids.push($(this).val());
    });

    $.post('/category/spec/extend', {
      categoryId: categoryId,
      extendids: ids
    }, function(res) {
      if (res.state) {
        $t.prev().trigger('click');
      }
    }, 'json');

  });



});