define(function(require, exports, module) {
  var template = require('libs/arttemplate/3.0.0/template.js');
  var $content = $('.content-wrap');
  var $categoryList = $content.find('.tree-list');
  template.config('escape', false);

  function updateList() {
    $categoryList.find('.del').removeClass('hide').each(function() {
      var $t = $(this);
      var $ul = $t.parent().parent().next();
      if ($ul.find('li').length > 1) {
        $t.addClass('hide');
      }
    });
    $categoryList.find('.dis').removeClass('dis');
    $categoryList.find('li:first-child').find('.glyphicon-arrow-up:first').addClass('dis');
    $categoryList.find('li:last-child,li li:nth-last-child(2)').find('.glyphicon-arrow-down:first').addClass('dis');
  }


  function formatData(data) {
    return data;
    var hasChild;
    $.each(data, function(i, v) {
      hasChild = v.childrens && v.childrens.length > 0;
      data[i]['class'] = 'glyphicon-plus';
      data[i]['child'] = hasChild ? '' : template('cate3Items', {
        parentId: data[i].adPositionId,
        hide: 'hide'
      });
      data[i]['del'] = hasChild ? '' : '<a href="javascript:" class="del">删除</a>';
    });
    return data;
  }

  function updateAddBtn($form, data) {
    $form.find('input[name="form[adPositionId]"]').val(data.adPositionId);
    $form.parents('li:first').find('[data-templateid]').removeClass('text-disabled').prop('disabled', false).data('parentid', data.adPositionId);
    $form.parents('.clearfix:first').find('[data-href]').each(function() {
      var $t = $(this);
      $t.attr('href', $t.data('href').replace('adPositionId.', data.adPositionId + '.'));
    });
    updateList();
  }

  function createChild($t, adPositionId) {
    var templateId = $t.is('.lever2') ? 'cate3Items' : 'cate2Items';
    var $div = $t.parents('div.clearfix:first');
    $.get(location.href, {
      adPositionId: adPositionId
    }, function(res) {
      if (res.state) {
        $div.after(template(templateId, {
          parentId: adPositionId,
          list: formatData(res.data)
        }));
        updateList();
      }
    }, 'json');
  }

  $content.on('click', '[data-templateid]', function(event) {
    event.preventDefault();
    var $t = $(this);
    if ($t.is('.text-disabled')) return;
    var templateid = $t.data('templateid');
    var $parent = $t.parent();
    if ($parent.is('li')) {
      $parent.before(template(templateid, {
        parentId: $t.data('parentid')
      }));
      $parent.prev().find('.form-control').trigger('focus');
      return;
    }
    $categoryList.append(template(templateid));
    $categoryList.find('.form-control:last').trigger('focus');
  });

  $categoryList.on('click', '.name .glyphicon', function(event) {
    event.preventDefault();
    var $t = $(this);
    var adPositionId = $t.siblings('[name="form[adPositionId]"]').val();
    var $child = $t.parents('div.clearfix:first').next();
    if ($t.is('.glyphicon-plus')) {
      $t.removeClass('glyphicon-plus').addClass('glyphicon-minus');
      if ($child.length === 0) {
        return createChild($t, adPositionId);
      }
      $child.removeClass('hide');
      updateList();
      return;
    }
    $t.removeClass('glyphicon-minus').addClass('glyphicon-plus');
    $child.addClass('hide');
    updateList();
  }).on('click', '.glyphicon-arrow-up:not(.dis)', function(event) {
    event.preventDefault();
    var $li = $(this).parents('li:first');
    $.post('/content/helpcategory/move', {
      'form[adPositionId]': $li.find('input[name="form[adPositionId]"]:first').val(),
      'form[to]': 'up'
    }, function(res) {
      if (res.state) {
        $li.insertBefore($li.prev());
        updateList();
      }
    }, 'json');

  }).on('click', '.glyphicon-arrow-down:not(.dis)', function(event) {
    event.preventDefault();
    $(this).parents('li:first').next().find('.glyphicon-arrow-up:first').trigger('click');
  }).on('click', '.createjs', function(event) {
    event.preventDefault();
    var $li = $(this).parents('li:first');
    if ($li.find('form:first').data('update')) {
      return;
    }

    $.get('/content/advertisement/createjs', {
      adPositionId: $li.find('input[name="form[adPositionId]"]:first').val()
    }, function(res) {
      if (res.state) {
        var jscode = template('jscode', {
          id: res.data.id,
          mark: res.data.mark,
          url1: res.data.url1,
          url2: res.data.url2,
          url3: res.data.url3,
          url4: res.data.url4,
        });
        $("#jsdemo").html(jscode);
      } else {
        alert(res.message);
      }
    }, 'json');

  }).on('click', '.del', function(event) {
    event.preventDefault();
    if (!confirm('您确定要删除吗？')) return;
    var $li = $(this).parents('li:first');
    if ($li.find('form:first').data('update')) {
      return $li.fadeOut(function() {
        $li.remove();
        updateList();
      });
    }

    $.post('/content/advertisement/del', {
      adPositionId: $li.find('input[name="form[adPositionId]"]:first').val()
    }, function(res) {
      if (res.state) {
        $li.fadeOut(function() {
          $li.remove();
          updateList();
        });
      } else {
        alert(res.message);
      }
    }, 'json');
  }).on('blur', 'input[name="form[title]"]', function(event) {
    var $t = $(this);
    if ($t.val() === '') {
      $t.val($t.data('prev'));
    }
    $t.parent('form').trigger('submit');
  }).on('submit', '.name form', function(event) {
    event.preventDefault();
    var $t = $(this);
    var $title = $t.find('input[name="form[title]"]');
    var title = $title[0];
    var val = title.value;
    if (val === '' || val === title.defaultValue || val === $title.data('prev')) return;
    $title.data('prev', val);
    $.post($t.attr('action'), $t.serializeArray(), function(res) {
      if (res.state && $t.data('update')) {
        updateAddBtn($t.attr('action', $t.data('update')).data('update', false), res.data);
      }
    }, 'json');
  }).on('click', 'a[href$="javascript::"]', function(e) {
    e.preventDefault();
  });
  updateList();

});
