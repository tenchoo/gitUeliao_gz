define(function(require, exports, module) {
  var template = require('libs/arttemplate/3.0.0/template.js');
  var $content = $('.content-wrap');
  var $categoryList = $content.find('.helpcategory-list');
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
        parentId: data[i].categoryId,
        hide: 'hide'
      });
      data[i]['del'] = hasChild ? '' : '<a href="javascript:" class="del">删除</a>';
    });
    return data;
  }

  function updateAddBtn($form, data) {
    $form.find('input[name="form[categoryId]"]').val(data.categoryId);
    $form.parents('li:first').find('[data-templateid]').removeClass('text-disabled').prop('disabled', false).data('parentid', data.categoryId);
    $form.parents('.clearfix:first').find('[data-href]').each(function() {
      var $t = $(this);
      $t.attr('href', $t.data('href').replace('categoryId.', data.categoryId + '.'));
    });
    updateList();
  }

  function createChild($t, categoryId) {
    var templateId = $t.is('.lever2') ? 'cate3Items' : 'cate2Items';
    var $div = $t.parents('div.clearfix:first');
    $.get(location.href, {
      categoryId: categoryId
    }, function(res) {
      if (res.state) {
        $div.after(template(templateId, {
          parentId: categoryId,
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
      var categoryId = $t.siblings('[name="form[categoryId]"]').val();
      var $child = $t.parents('div.clearfix:first').next();
      if ($t.is('.glyphicon-plus')) {
        $t.removeClass('glyphicon-plus').addClass('glyphicon-minus');
        if ($child.length === 0) {
          return createChild($t, categoryId);
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
        'form[categoryId]': $li.find('input[name="form[categoryId]"]:first').val(),
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

      $.post('/content/helpcategory/del', {
        categoryId: $li.find('input[name="form[categoryId]"]:first').val()
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
      $(this).parents('form').trigger('submit');
    }).on('change', 'form[action*="edit.html"] :radio', function() {
      $(this).parents('form').trigger('submit');
    }).on('submit', 'li form', function(event) {
      event.preventDefault();
      var $t = $(this);
      $.post($t.attr('action'), $t.serializeArray(), function(res) {
        if (res.state && $t.data('update')) {
          updateAddBtn($t.attr('action', $t.data('update')).data('update', false), res.data);
        }
        if (!res.state) {
          alert(res.message || '操作失败，请刷新重试！');
          //location.href = location.href;
        }
      }, 'json');
    })
    .on('click', 'a[href$="javascript::"]', function(e) {
      e.preventDefault();
    });
  updateList();

});
