define(function(require, exports, module) {
  var $allCate = $('.all-cate');
  var $subCate = $('.sub-cate');
  $allCate.on('mouseenter', function() {
    $allCate.find('.sub-cate').css('display', 'block');
  }).on('mouseleave', function() {
    $allCate.find('.sub-cate').css('display', 'none');
  });

  $subCate.on('click', '.arr', function(event) {
    var $hd = $(this).parent(),
      $cate = $hd.parent();
    if ($hd.is('.cate-2th-hd')) {
      $cate.is('.cate-2th-active') ? $cate.removeClass('cate-2th-active') : $cate.addClass('cate-2th-active');
      return;
    }
    if ($hd.is('.cate-1th-hd')) {
      $cate.is('.cate-1th-active') ? $cate.removeClass('cate-1th-active') : $cate.addClass('cate-1th-active');
      return;
    }
  });
});
