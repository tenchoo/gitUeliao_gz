define(function(require, exports, module) {
  var $cart = $('#J_cart');
  var $count = $cart.find('.count');
  var count = $cart.data('count');
  var $bd = $cart.find('.top-cart-bd');

  if (count === 0) {
    $bd.html('<div class="cart-empty">您购物车里还没有任何产品。</div>');
  } else {
    $bd.html('<div class="cart-loading"></div>');
  }

  $cart.on('mouseenter', function(e) {
    if (!$cart.data('cache')) {
      load();
      $cart.data('cache', true);
    }
  }).on('click', '.del', function(e) {
    e.preventDefault();
    $.get($(this).attr('href'), load, 'jsonp');
  }).on('click', 'button[data-href]', function(e) {
    e.preventDefault();
    location.href = $(this).data('href');
  }).on('update', load);

  function load() {
    $.get(seajs.data.apiPath + '/ajax', {
      action: 'cart'
    }, function(res) {
      if (res.state) {
        $count.text(' ' + res.data.totalProduct);
        if (res.data.totalProduct === 0) {
          return $bd.html('<div class="cart-empty">您购物车里还没有任何产品。</div>');
        }
        require.async('libs/arttemplate/3.0.0/template.js', function(template) {
          $bd.html(template('cart-list', res.data)).find('img').each(function() {
            var $t = $(this);
            $t.attr('src', $t.data('src').replace(/^htt.*?htt/, 'htt'));
          });
        });
      }
    }, 'jsonp');
  }

  var $search = $('.search');

  var uploader = require('modules/uploader/js/uploader.js').uploader({
    server: '/search/upload',
    fileVal: 'image',
    paste: '.search-form',
    pick: {
      id: '.image-search',
      multiple: false,
      style: ''
    },
    success: function(file, res) {
      if (res.state) {
        location.href = res.data.url;
      }
    }
  });

  uploader.on('uploadBeforeSend', function() {
    $search.append('<div class="uploading">图片匹配中，请稍后……</div>');
  });


});
