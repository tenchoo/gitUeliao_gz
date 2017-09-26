define(function(require, exports, module) {
  var template = require('libs/arttemplate/3.0.0/template.js');
  require('libs/lodash/3.10.1/lodash.min.js');
  var dialog = require('modules/dialog/js/dialog.js');
  var $productInfo = $('.product-info');
  var $bigImg = $productInfo.find('.c_img320 img');
  var $form = $('#orderForm');
  var $spes = $form.find('[name="spes"]');
  var specLength = $form.find('ul').length;

  var $price = $productInfo.find('.price');
  var price = $price.text();
  var $total = $form.find('.inventory b');
  var total = $total.text();

  var $chima = $form.find('input.chima');
  var $btnleft = $form.find('.btnleft');
  var $btnright = $form.find('.btnright');

  function resetProductInfo() {
    $price.text(price);
    $total.text(total);
    $chima.data('total', total).trigger('keyup');
    $spes.val('');
  }

  function formatNum(val) {
    return parseInt(val.replace(/\D/g, ''), 10) || 1;
  }

  function updateProductInfo() {
    var $selectSpec = $form.find('li.active');
    if ($selectSpec.length !== specLength) return resetProductInfo();
    var data = [];
    $selectSpec.each(function() {
      data.push($(this).data('id'));
    });
    var index = _.findIndex(specprice, {
      relation: data.join()
    });
    if (index > 0) {
      $spes.val(data.join());
      $price.html('&yen;' + specprice[index].price);
      $total.text(specprice[index].total);
      $chima.data('total', specprice[index].total).trigger('keyup');
    }
  }

  function updatePreview(src) {
    $bigImg.attr('src', src);
  }

  function updateControlBtn(num) {
    if (num === 1) {
      $btnleft.addClass('dis');
    } else {
      $btnleft.removeClass('dis');
    }
    if (num === $chima.data('total')) {
      $btnright.addClass('dis');
    } else {
      $btnright.removeClass('dis');
    }
  }

  $productInfo.on('mouseenter', '.smallimg li:not(.active)', function(event) {
    var $t = $(this).addClass('active');
    updatePreview($t.data('src'));
    $t.siblings('.active').removeClass('active');
  });

  $form.on('click', 'li:not(.active)', function(event) {
    var $t = $(this);
    $t.addClass('active').siblings('.active').removeClass('active');
    updateProductInfo();
    if ($t.is('.item-img')) {
      updatePreview($t.data('src'));
      $productInfo.find('.smallimg li.active').removeClass('active');
    }
  }).on('click', 'li.active', function(event) {
    var $t = $(this);
    $t.removeClass('active');
    updateProductInfo();
  }).on('click', '.control b:not(.dis)', function(event) {
    var $t = $(this);
    var num = formatNum($chima.val()) + ($t.is('.btnleft') ? -1 : 1);
    num = Math.max(num, 1);
    num = Math.min(num, $chima.data('total'));
    $chima.val(num);
    updateControlBtn(num);
  }).on('keyup', '.chima', function(event) {
    var $t = $(this);
    var val = formatNum($t.val());
    val = Math.min(val, $t.data('total'));
    $t.val(val);
    if (/^[1-9]\d*$/.test(val)) {
      updateControlBtn(val);
    }
  }).on('click', '.btn-success', function(event) {
    if ($form.data('submit')) return;

    if ($form.find('li.active').length !== specLength) return dialog.tip('请选择规格信息！');

    $form.data('submit', true);

    $.post($form.attr('action'), $form.serializeArray(), function(res) {
      $form.data('submit', false);
      if (res.state) {
        dialog.tip('成功加入购物车！');
        return;
      }
    }, 'jsonp');
  });



  $(function() {
    window._bd_share_config = {
      "common": {
        "bdSnsKey": {},
        "bdText": "",
        "bdMini": "2",
        "bdMiniList": false,
        "bdPic": "",
        "bdStyle": "0",
        "bdSize": "16"
      },
      "share": {}
    };
    require.async('http://bdimg.share.baidu.com/static/api/js/share.js?v=89860593.js?cdnversion=' + ~(-new Date() / 36e5));
  });


});