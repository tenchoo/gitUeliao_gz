define(function(require, exports, module) {
  var input = require('modules/form/js/input.js');
  input.intOnly();
  input.numFloatOnly();
  var checkedAll = require('modules/checkedall/js/checkedall.js');
  var newCheckAll;
  var dialog = window;
  var template = require('libs/arttemplate/3.0.0/template.js');
  var $content = $('.content-wrap');
  var $tbody = $content.find('.receipt tbody');
  var $tr;
  var batch;


  var $addproduct = $('.add-product');
  $addproduct.on('shown.bs.modal', function(event) {
    newCheckAll = checkedAll($(this), {
      all: 'thead .checkedall'
    });

  }).on('click', '.btn-success', function() {
    var checkeds = newCheckAll();
    if (checkeds.length < 1) {
      dialog.alert('请选择数据后操作');
      return false;
    }
    var html = '';
    var t = (new Date()).getTime();
    $.each(checkeds, function(k, i) {
      html += template('receiptlist', $.extend(productData[i.value], { id: t + i.value }));
    });
    $tbody.append(html);
    $addproduct.find(':checked').prop('checked', false);
    $addproduct.modal('hide');

  });

  $content.on('click', '.del', function(event) {
    event.preventDefault();
    $(this).parents('tr:first').remove();
  });


  var $addposition = $('.add-position');
  var warehouseid = $content.find('[name="warehouseid"]').val();
  $.get('/api/warehouse_by_id', {
    warehouseId: warehouseid
  }, function(res) {
    if (res.data) {
      $addposition.find('.cate1').html(template('area', res.data));
    }
  }, 'json');

  $content.on('click', '.choose-position', function(event) {
    event.preventDefault();
    $tr = $(this).parents('tr');
    $addposition.modal();
  }).
  on('click', '.batch-position', function() {
    batch = true;
    $addposition.modal();
  });
  $addposition.on('change', '.cate1', function() {
    var t = this;
    var id = t.value;
    $.get('/api/warehouse_by_id', {
      parentId: id
    }, function(res) {
      if (res.data) {
        $addposition.find('.cate2').html(template('area', res.data));
      }
    }, 'json');
  }).on('click', '.btn-success', function() {
    var val = $addposition.find('.cate2 :selected').val();
    var text = $addposition.find('.cate2 :selected').text();
    if (batch) {
      $content.find('.positioninfo span').text(text);
      $content.find('.positioninfo input.positionId').val(val);
      $content.find('.positioninfo input.positionTitle').val(text);
    } else {
      $tr.find('.positioninfo span').text(text);
      $tr.find('.positioninfo input.positionId').val(val);
      $tr.find('.positioninfo input.positionTitle').val(text);
    }

    $addposition.modal('hide');
    $addposition.find('.cate1,.cate2').val('');
  });

  $addposition.on('hide.bs.modal', function() {
    batch = false;
  });

});
