define(function(require, exports, module) {
  var category = require('modules/category/js/category.js');
  var $form = $('.create-cate form');
  var $select;
  var $selectTxt = $form.find('.text-warning');
  var dialog = require('modules/dialog/js/dialog.js');

  function upSelect($cate) {
    var selects = [];
    if ($cate.is('.cate1')) {
      $select = $cate;
    } else if ($cate.is('.cate2')) {
      $select = $form.find('.cate1,.cate2');
    } else {
      $select = $form.find('select');
    }

    $select.each(function() {
      selects.push($(this).find(':selected').text());
    });
    $selectTxt.html(selects.join(' &gt; '));
  }

  category.select($form, {
    realField: '[name="categoryId"]',
    'default': $form.find('input').val(),
    cate1Cb: upSelect,
    cate2Cb: upSelect,
    cate3Cb: upSelect
  });

  $form.on('submit', function(event) {
    if ($form.find('[name="categoryId"]').val() === '') {
      dialog.tip('请选择正确的产品分类！');
      event.preventDefault();
    }
  });

});