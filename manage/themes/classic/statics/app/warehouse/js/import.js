define(function(require, exports, module) {
  var template = require('libs/arttemplate/3.0.0/template.js');
  var warehouse = require('modules/warehouse/js/warehouse.js');
  var input = require('modules/form/js/input.js');
  var $content = $('.content-wrap');
  var $tbody = $content.find('.import tbody');
  var $name = $content.find('input[name="form[name]"]');
  var $id = $content.find('input[name="form[id]"]');
  var $contact = $content.find('input[name="form[contact]"]');
  var $phone = $content.find('input[name="form[phone]"]');
  var $product = $content.find('.product-search');
  var $addlm=$('.addlm');
  var $imporsub=$('.imporsub')

  require('modules/form/js/input.js').numFloatOnly();


  $content
    .on('click', '[data-templateid]', function(event) {
      event.preventDefault();
      var id = $(this).data('postid');
      $.get('/api/post_order_product_serial', {
        id: id
      }, function(res) {
        var t = (new Date()).getTime();
        $tbody.append(template('importlist', $.extend(res.data, {
          id: t
        })));

        var $m = $('#J_' + t);
        var $psearch = $m.find('input[data-suggestion]');
        var $c = $m.find('.warehouse-list').children('input');
        warehouse.select($m, {
          realField: $c,
          level: 3
        });
        input.suggestion($psearch, {
          cb: function($li, data) {}
        });
      }, 'json');

    })
    .on('click', '.del', function(event) {
      event.preventDefault();
      $(this).parents('tr:first').remove();
    })
    .on('submit', 'form', function(event) {
      event.preventDefault();
      $addlm.prop('disabled', true);
      $addlm.val('请稍候..');
      $imporsub.prop('disabled', true);
      $imporsub.val('请稍候..');
      var $form = $(this);
      $.post(location.href, $form.serializeArray(), function(res) {
        if (res.state) {
          location.href = res.data || location.href;
          return;
        }
        alert(res.message || '保存失败，请稍后重试！');
        $addlm.prop('disabled', false);
        $addlm.val('确认入库');
        $imporsub.prop('disabled', false);
        $imporsub.val('已提交');
      }, 'json');
    });
  $tbody.find('tr').each(function() {
    var $t = $(this);
    var $n = $t.find('.warehouse-list').children('input');
    warehouse.select($t, {
      realField: $n,
      level: 3
    });
  });

  input.suggestion($name, {
    er: function() {
      $id.val('');
      $contact.val('');
      $phone.val('');
    },
    cb: function($li, data) {
      $id.val(data.id);
      $contact.val(data.contact);
      $phone.val(data.phone);
    }
  });

  input.suggestion($product, {
    cb: function($li, data) {}
  });

});
