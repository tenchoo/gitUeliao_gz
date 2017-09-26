define(function(require, exports, module) {
  var template = require('libs/arttemplate/3.0.0/template.js');
  var $content = $('.content-wrap');
  var $dataList = $content.find('.category-list');
  template.config('escape', false);

  function updateList() {
    $dataList.find('.del').removeClass('hide').each(function() {
      var $t = $(this);
      var $ul = $t.parent().parent().next();
      if ($ul.find('li').length > 1) {
        $t.addClass('hide');
      }
    });
    // $dataList.find('.dis').removeClass('dis');
    // $dataList.find('li:first-child').find('.glyphicon-arrow-up:first').addClass('dis');
    // $dataList.find('li:last-child,li li:nth-last-child(2)').find('.glyphicon-arrow-down:first').addClass('dis');
  }

  function formatData(data) {

    $.each(data, function(i, v) {
      data[i]['class'] = 'glyphicon-minus';
//      data[i]['child'] = v.childrens ? '' : template('cate3Items', {
//        parentId: data[i].warehouseId,
//        hide: 'hide'
//      });
      data[i]['del'] = '<a href="javascript:" class="del">删除</a>';
    });
    return data;
  }

  function updateAddBtn($form, data) {
    $form.find('input[name="form[warehouseId]"]').val(data.warehouseId);
    $form.parents('li:first').find('[data-templateid]').removeClass('text-disabled').prop('disabled', false).data('parentid', data.warehouseId);
    $form.parents('.clearfix:first').find('[data-href]').each(function() {
      var $t = $(this);
      $t.attr('href', $t.data('href').replace('warehouseId.', data.warehouseId + '.'));
    });
    //updateList();
  }

  function createChild($t, id) {
    var templateId = $t.is('.lever2') ? 'cate3Items' : 'cate2Items';
    var $div = $t.parents('div.clearfix:first');
    $.get(location.href, {
      id: id
    }, function(res) {
      if (res.state) {
        $div.after(template(templateId, {
          parentId: id,
          list: formatData(res.data)
        }));
        //updateList();
      }else{
		alert( res.message );
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
    $dataList.append(template(templateid));
    $dataList.find('.form-control:last').trigger('focus');
  });

  $dataList.on('click', '.name .glyphicon:not(.lever2)', function(event) {
    event.preventDefault();
    var $t = $(this);
    var id = $t.siblings('[name="form[warehouseId]"]').val();
    var $child = $t.parents('div.clearfix:first').next();
    if ($t.is('.glyphicon-plus')) {
      $t.removeClass('glyphicon-plus').addClass('glyphicon-minus');

     if ($child.length === 0) {
        return createChild($t, id);
      }
      $child.removeClass('hide');
      //updateList();
      return;
    }
    $t.removeClass('glyphicon-minus').addClass('glyphicon-plus');
    $child.addClass('hide');
    //updateList();
  }).on('click', '.glyphicon-arrow-up:not(.dis)', function(event) {
    event.preventDefault();
    var $li = $(this).parents('li:first');
    $.post('/warehouse/warehouse/move', {
      'form[warehouseId]': $li.find('input[name="form[warehouseId]"]:first').val(),
      'form[to]': 'up'
    }, function(res) {
      if (res.state) {
        $li.insertBefore($li.prev());
        //updateList();
      }
    }, 'json');

  }).on('click', '.glyphicon-arrow-down:not(.dis)', function(event) {
    event.preventDefault();
    $(this).parents('li:first').next().find('.glyphicon-arrow-up:first').trigger('click');
  }).on('click', '.del', function(event) {
    event.preventDefault();
    var $li = $(this).parents('li:first');
    if(!confirm('您确定要删除吗？')) return;
    if ($li.find('form:first').data('update')) {
      return $li.fadeOut(function() {
        $li.remove();
        //updateList();
      });
    }
    $.post('/warehouse/warehouse/del', {
      id: $li.find('input[name="form[warehouseId]"]:first').val()
    }, function(res) {
      if (res.state) {
        $li.fadeOut(function() {
          $li.remove();
          //updateList();
        });
      }else{
		alert('删除失败。'+res.message);
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
      }else{
		if ( !res.state ){
			alert(res.message);
		}		
	  }
    }, 'json');
  });
  //updateList();
});