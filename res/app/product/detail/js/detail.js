define(function(require, exports, module) {
  var $body = $('body');
  var $previewWrap = $('.preview-wrap');
  var $preview = $previewWrap.find('.preview img');
  var $tabBd = $('.detail-tab-bd');
  var $form = $('.product-info form');
  var $colors = $form.find('.colors');
  var $group = $colors.find('.group');
  var $colorsItem = $colors.find('ul.list:first li');
  var $selected = $colors.find('.selected');
  var $countWrap = $form.find('div.selected');
  var productId = location.pathname.match(/\d+/)[0];
  var $fav = $('.share-fav .fav');
  var template = require('libs/arttemplate/3.0.0/template.js');
  var price = parseInt($('[data-price]').data('price') || 1, 10);
  var isLogin = require('api/ajax/?action=checklogin&callback=define').state;
  var dialog = require('modules/dialog/js/dialog.js');
  template.config('escape', false);
  var input = require('modules/form/js/input.js');
  require('libs/jquery-elevatezoom/3.0.8/jquery.elevatezoom.min.js');
  var cache = {};
  var searchTimer;
  input.plusMinus();
  input.clearZero($('.colors .int-only'));

  function updateSelect() {
    var selected = [];
    var count = 0;
    var data = {};
    var $t;
    var $num;
    var num;
    var img;
    var group;

    $group.find('li').removeClass('selected');

    $colorsItem.each(function() {
      $t = $(this);
      $num = $t.find('.int-only');
      num = parseFloat($num.val() || 0, 10) * 10;
      img = $t.data('img');
      if (num !== 0) {
        group = $t.data('group');
        $group.find('li[data-group="' + group + '"]').addClass('selected');
        data = {
          num: num / 10,
          rel: $t.data('rel'),
          code: $t.data('code'),
          title: $t.data('title'),
          img: img ? '<img src="' + img + '" width="28" height="28" alt="" />' : '',
          group: group,
          stockid: $t.data('stockid')
        };
        count += num;
        selected.push(data);
      }
    });



    if (count === 0) {
      $countWrap.addClass('hide');
      return;
    }

    $selected.html(template('selected', {
      list: selected
    }));
    $countWrap.removeClass('hide').find('.count').html(template('count', {
      count: count / 10,
      price: ((count * price) / 1000).toFixed(2)
    }));
  }

  function clearSelect() {
    $colors.find('.int-only').val(0).filter(':last').trigger('change');
  }

  function toggleShow(filter) {
    var $items = $colorsItem.addClass('hide').filter(filter).removeClass('hide');
    $colors.addClass('less');
    if ($items.length < 6) {
      $colors.find('.more').addClass('hide');
    } else {
      $colors.find('.more.hide').removeClass('hide');
    }
  }

  function search() {
    var keyword = $form.find('.serial').val();

    if (keyword === '') return $colorsItem.removeClass('hide');
    $colors.find('.group li.active').removeClass('active');
    if (cache[keyword]) return toggleShow(cache[keyword]);
    $.get(seajs.data.apiPath + '/ajax', {
      action: 'color',
      keyword: keyword
    }, function(res) {
      if (res.state) {
        toggleShow(res.data);
        cache[keyword] = res.data || true;
      }
    }, 'jsonp');
  }

  $('.detail-tab').on('click', 'li:not(.active)', function(event) {
    var $t = $(this).addClass('active');
    var index = $t.index();
    $t.siblings('.active').removeClass('active');
    var $item = $tabBd.find('.tab-bd-item:eq(' + index + ')');
    $item.addClass('active').siblings('.active').removeClass('active');
    if (index === 1 && !$tabBd.data('render')) {
      $tabBd.data('render', true).find('li').height($tabBd.find('ul.attr-group').height());
    }
    if ($item.data('rel') && !$item.data('load')) {
      $item.data('load', true).load($item.data('rel'));
    }
  });
  $tabBd.on('click', '.page a', function(e) {
    e.preventDefault();
    var $t = $(this);
    $t.parents('.tab-bd-item').load($t.attr('href'));
  });

  $previewWrap.on('mouseenter', '.thumb li:not(.active)', function(event) {
    var $t = $(this);
    $t.addClass('active').siblings('.active').removeClass('active');
    $preview.attr('src', $t.data('src'));
    $('.preview img').elevateZoom({
      zoomWindowWidth: 340,
      zoomWindowHeight: 340
    });
  });
  $('.preview img').elevateZoom({
    zoomWindowWidth: 340,
    zoomWindowHeight: 340
  });

  $colors.on('click', '.more', function(event) {
      if ($colors.is('.less')) {
        $colors.removeClass('less');
        return;
      }
      $colors.addClass('less');
    })
    .on('click', '.group li:not(.active)', function(event) {
      var $t = $(this);
      var group = $t.data('group');
      $t.addClass('active').siblings('.active').removeClass('active');
      toggleShow('[data-group="' + group + '"]');
    })
    .on('change', '.int-only', updateSelect)
    .on('keyup', '.int-only', function(event) {
      setTimeout(updateSelect, 10);
    })
    .on('click', 'ul.selected', function(event) {
      event.stopPropagation();
    });

  $(document).on('click', function(event) {
    $form.find('div.selected a').removeClass('active');
    $selected.addClass('hide');
  });


  $form
    .on('keyup', '.serial', function(event) {
      clearTimeout(searchTimer);
      searchTimer = setTimeout(search, 200);
    })
    .on('keypress', '.serial', function(event) {
      if (event.keyCode === 13) {
        event.preventDefault();
        search();
      }
    })
    .on('click', '.search', search)
    .on('click', '.add-cart', function(event) {
      updateSelect();
      $form.trigger('submit', ['cart']);
    })
    .on('click', 'div.selected a', function(event) {
      event.stopPropagation();
      var $t = $(this);
      if ($t.is('.active')) {
        $t.removeClass('active');
        $selected.addClass('hide');
        return;
      }
      $t.addClass('active');
      $selected.removeClass('hide');
    })
    .on('submit', function(event, type) {
      if ($colors.next().is('.hide')) {
        event.preventDefault();
        return dialog.tip('请选择产品！');
      }

      $form.attr('action', $form.data(type || 'action'));
      if (!isLogin) {
        event.preventDefault();
        require.async('modules/login/js/login.js', function(l) {
          l.open();
        });
        $body.on('loginsuccess.fav', function() {
          isLogin = true;
          $form.trigger('submit', [type]);
        });
      }
      if (type === 'cart') {
        event.preventDefault();
        $.get($form.attr('action'), $form.serializeArray(), function(res) {
          if (res.state) {
            $('#J_cart').trigger('update');
            dialog.tip('加入成功！');
            return clearSelect();
          }
          dialog.tip(res.message || '加入失败！');
        }, 'jsonp');
      }
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

  require.async('api/ajax?action=collection&productId=' + productId + '&optype=check&callback=define', function(c) {
    if (c.state) {
      $fav.removeClass('fav').addClass('faved').html('<i class="ico-fav pull-left"></i>已收藏');
    }
  });
  $fav.on('click', function(e) {
    e.preventDefault();
    if (!isLogin) {
      event.preventDefault();
      require.async('modules/login/js/login.js', function(l) {
        l.open();
      });
      $body.on('loginsuccess.submit', function() {
        isLogin = true;
        $fav.trigger('click');
      });
      return;
    }
    $.get(seajs.data.apiPath + '/ajax', {
      action: 'collection',
      productId: productId,
      optype: 'add'
    }, function(res) {
      if (res.state) {
        $fav.removeClass('fav').addClass('faved').html('<i class="ico-fav pull-left"></i>已收藏');
        return;
      }
      dialog.alert(res.message || '收藏失败，请稍后重试！');
    }, 'jsonp');
  });

});
