define(function(require, exports, module) {
  var template = require('libs/arttemplate/3.0.0/template.js');
  var $content = $('.content-wrap');
  var $categoryList = $content.find('.category-list');

  $content.on('click', '[data-templateid]', function(event) {
    event.preventDefault();
    $categoryList.append(template($(this).data('templateid')));
    $categoryList.find('.form-control:last').trigger('focus');
  });

  $categoryList.on('click', '.name .glyphicon', function(event) {
    event.preventDefault();
    var $t = $(this);
    var $child = $t.parent().parent().next();
    if ($t.is('.glyphicon-plus')) {
      $t.removeClass('glyphicon-plus').addClass('glyphicon-minus');
      $child.removeClass('hide');
      return;
    }
    $t.removeClass('glyphicon-minus').addClass('glyphicon-plus');
    $child.addClass('hide');
  }).on('click', '.glyphicon-arrow-up:not(.dis)', function(event) {
    event.preventDefault();
    var $li = $(this).parents('li:first');
    $li.insertBefore($li.prev());
  }).on('click', '.glyphicon-arrow-down:not(.dis)', function(event) {
    event.preventDefault();
    $(this).parents('li:first').next().find('.glyphicon-arrow-up:first').trigger('click');
  }).on('click', '.del', function(event) {
    event.preventDefault();
    var $t = $(this);
    var $li = $t.parents('li:first');
    var href = $t.attr('href');
    var vale = $t.parents('[name="form[specName]"]').val();
    if ( '#' === href && vale === null ) {
      return $li.fadeOut(function() {
        $li.remove();
      });
    }
    /*$.get(href, function(res) {
      if (res.state) {
        $li.fadeOut(function() {
          $li.remove();
        });
      }
    }, 'json');*/
  }).on('change', '[name="ispicture"]', function(event) {
    var specId = $(this).val();
    $.post('/category/spec/setspecpicture', {
      specId: specId
    }, function(res) {}, 'json');
  }).on('change', '[name="form[isColor]"]', function(event) {
    $(this).parents('form:first').trigger('submit', [true]);
  }).on('blur', 'input[name="form[specName]"]', function(event) {
    var $t = $(this);
    if ($t.val() === '') {
      $t.val($t.data('prev'));
    }
    $t.parents('form:first').trigger('submit');
  }).on('click', 'a[data-href]', function(event) {
    event.preventDefault();
  }).on('submit', 'form', function(event, enforce) {
    event.preventDefault();
    var $t = $(this);
    var $title = $t.find('input[name="form[specName]"]');
    var title = $title[0];
    var val = title.value;
    var $li = $t.parents('li:first');
    if (!enforce) {
      if (val === '' || val === title.defaultValue || val === $title.data('prev')) return;
    }
    $title.data('prev', val);
    $.post($t.attr('action'), $t.serializeArray(), function(res) {
      if (res.state && $t.data('update')) {
        $t.find('input[name="form[specId]"]').val(res.data.specId);
        $t.find(':disabled').prop('disabled', false);
        $li.find(':radio').val(res.data.specId).prop('disabled', false);
        $li.find('a[data-href]').each(function() {
          var $t = $(this);
          $t.attr('href', $t.data('href').replace('specId.', res.data.specId + '.')).removeAttr('data-href');
        });
      }
      if(res.state === false){
        alert(res.message || '保存失败，请稍后重试！');
        $t.find('input[name="form[specName]"]').trigger('focus');
      }
    }, 'json');
  });

});