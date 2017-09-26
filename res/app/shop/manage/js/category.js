define(function(require, exports, module) {
  var $category = $('.category');
  var $categoryList = $category.find('.category-list');
  var render = require('libs/arttemplate/3.0.0/template.js');
  var dialog = require('modules/dialog/js/dialog.js');
  var groupId = $('#groupId').val() || 99;
  var validator = require('modules/form/js/validator.js');

  function updateList() {
    $categoryList.find('.cate1 .move').html('<i class="icon icon-control icon-green-up"></i>\n<i class="icon icon-control icon-green-down"></i>');
    $categoryList.find('.cate1 .control').addClass('hide');
    $categoryList.find('.add:first-child').parent().prev().find('.control').removeClass('hide');
    $categoryList.find('.item:first .icon-green-up').removeClass('icon-green-up').addClass('icon-green-up-dis');
    $categoryList.find('.item:last .icon-green-down').removeClass('icon-green-down').addClass('icon-green-down-dis');
    $categoryList.find('ul .move').html('<i class="icon icon-control icon-blue-up "></i>\n<i class="icon icon-control icon-blue-down "></i>');
    $categoryList.find('ul li:first-child .icon-blue-up').removeClass('icon-blue-up').addClass('icon-blue-up-dis');
    $categoryList.find('ul li:nth-last-child(2) .icon-blue-down').removeClass('icon-blue-down').addClass('icon-blue-down-dis');
    $categoryList.find('input[name$="[listOrder]"]').each(function(i) {
      $(this).val(i + 1);
    });
  }

  $category.on('click', 'button.add', function(event) {
    $categoryList.append(render('cate1Template', {
      groupId: groupId++
    })).find('.item:last').find('input').trigger('focus');
    updateList();
  }).on('click', '.add a', function(event) {
    event.preventDefault();
    var $t = $(this);
    var $li = $t.parents('.item');
    var hasParent = $li.data('categoryid');
    $t.parent('.add').before(render('cate2Template', {
      groupId: groupId++,
      parent: hasParent ? hasParent : $li.data('groupid'),
      parentType: hasParent ? 'parentId' : 'parentKey'
    })).prev().find('input').trigger('focus');
    updateList();
  }).on('click', '.name .icon-control', function(event) {
    var $t = $(this);
    var $li = $t.parents('.item');
    if ($li.is('.active')) {
      $li.removeClass('active');
      $t.removeClass('icon-control-minus').addClass('icon-control-plus');
    } else {
      $li.addClass('active');
      $t.removeClass('icon-control-plus').addClass('icon-control-minus');
    }
  }).on('click', '.icon-green-down,.icon-blue-down', function(event) {
    var $t = $(this);
    var $li = $t.parents('li:first');
    $li.insertAfter($li.next());
    updateList();
  }).on('click', '.icon-green-up,.icon-blue-up', function(event) {
    var $t = $(this);
    $t.parents('li:first').prev().find('.icon-green-down,.icon-blue-down').trigger('click');
  }).on('click', '.del', function(event) {
    event.preventDefault();
    var $li = $(this).parents('li:first');
    var shopCategoryId = $li.data('categoryid');
    if (!shopCategoryId) {
      $li.remove();
      updateList();
      return;
    }
    $.post('/ajax/shopcategory', {
      optype: 'del',
      shopCategoryId: shopCategoryId
    }, function(res) {
      if (res.state) {
        $li.remove();
        updateList();
        return;
      }
      dialog.tip(res.message || '删除失败，请稍后重试！');
    }, 'json');
  }).on('submit', 'form', function(event) {
    event.preventDefault();
    validator.formAjax($(this), 'cateForm', true);
  });
});