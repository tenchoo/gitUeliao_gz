define(function(require, exports, module) {
  var getCheckeds = require('app/member/frame/js/checkedAll.js')();
  var $list = $('.frame-list-bd');
  var template = require('libs/arttemplate/3.0.0/template.js');
  var dialog = require('modules/dialog/js/dialog.js');

  function update($tr) {
    var $category = $tr.find('.categorys');
    var $categorys = $category.find('li');
    var $select = $tr.find('select');
    var options = [];
    $select.find(':disabled').prop('disabled', false);
    if ($categorys.length) {
      $categorys.each(function() {
        options.push('[value="' + $(this).data('categoryid') + '"]');
      });
      $category.children('span').text($categorys.filter(':first').text());
      $category.find('.count').text(options.length);
      $select.find(options.join()).prop('disabled', true);
      return;
    }
    $category.html('<span class="default">未分类</span>');
  }

  function addCategory($tr, categoryId, title) {
    var $categorys = $tr.find('.more-category-bd ul');
    var data = {
      title: title,
      categoryId: categoryId
    };
    if ($categorys.length) {
      if ($categorys.find('li[data-categoryid="' + categoryId + '"]').length) return;
      $categorys.prepend(template('itemTemplate', data));
      update($tr);
      return;
    }
    $tr.find('.categorys').html(template('categorysTemplate', data));
    update($tr);
  }

  function doAjax(ids, shopCategoryId, cb, optype) {
    optype = optype || 'add';
    ids = $.isArray(ids) ? ids : [ids];
    $.post('/ajax/shopproduct', {
      productIds: ids,
      optype: optype,
      shopCategoryId: shopCategoryId
    }, function(res) {
      if (res.state) {
        cb();
        return;
      }
      dialog.tip(res.message || '操作失败，请稍后重试！');
    }, 'json');
  }


  $list.on('mouseenter', '.more-category', function(event) {
    $(this).addClass('more-category-active');
  }).on('mouseleave', '.more-category', function(event) {
    $(this).removeClass('more-category-active');
  }).on('click', '.icon-close', function(event) {
    event.preventDefault();
    $(this).parents('.more-category-active').removeClass('more-category-active');
  }).on('click', '.icon-close-sm', function(event) {
    event.preventDefault();
    var $li = $(this).parent('li');
    var $tr = $li.parents('tr');
    doAjax($tr.data('productid'), $li.data('categoryid'), function() {
      $li.remove();
      update($tr);
    }, 'del');

  }).on('change', 'thead select,tfoot select', function(event) {
    var $t = $(this);
    var $checkeds = getCheckeds();
    var categoryTitle = $t.find(':selected').text();
    var categoryId = $t.val();
    var ids = [];
    if ($checkeds.length) {
      $checkeds.each(function() {
        ids.push($(this).val());
      });
      doAjax(ids, categoryId, function() {
        $checkeds.each(function() {
          addCategory($(this).parents('tr'), categoryId, categoryTitle);
        });
        $t.val('').prev().trigger('click');
      });
      return;
    }
    dialog.tip('请选择数据后操作！');
    $t.val('');
  }).on('change', 'tbody select', function(event) {
    var $t = $(this);
    var $tr = $t.parents('tr');
    var categoryId = $t.val();
    doAjax($tr.data('productid'), categoryId, function() {
      addCategory($tr, categoryId, $t.find(':selected').text());
      $t.val('');
    });
  }).find('tbody tr').each(function() {
    update($(this));
  });
});