define(function(require, exports, module) {
  var categorys = require('localApi/api/storage_position_info?callback=define')['data']['childs'];
  var template = require('libs/arttemplate/3.0.0/template.js');
  var caches = {};

  function render(data) {
    return template.compile('{{each list}}<option value="{{$index}}">{{$value}}</option>{{/each}}')({
      list: data
    });
  }

  function clearSelect($select, level) {
    $select.removeClass('input-disabled').prop('disabled', false).html('<option value="default">' + ('请选择') + '</option>');
  }

  function createOptions($select, data, defaultVal) {
    var haSize = $select.removeClass('input-disabled').prop('disabled', false).is('[size]');
    $select[haSize ? 'html' : 'append'](data).val(defaultVal || 'default');
  }

  function disableSelect($select) {
    $select.addClass('input-disabled').prop('disabled', true).html('<option value="">暂无数据</option>');
  }

  function clearRealField($field, clear) {
    if (!clear) return;
    $field.val('');
  }

  function getData(name,id, cb) {
    if (caches[name+'_'+id]) {
      return cb(caches[id]);
    }

	if( name == 'parent' ){
		var param = { id: id };
	}else{
		var param = { warehouseId: id };
	}

    $.get('/api/storage_position_info', param, function(res) {
      caches[id] = {
        parent: id,
        data: res.state ? render(res.data.childs) : ''
      };
      cb(caches[id]);
      //ajax出错处理

    }, 'jsonp');
  }


  exports.select = function($category, options) {
    options = $.extend({
      cate1: '.cate1',
      cate2: '.cate2',
      cate3: '.cate3',
      cate4: '.cate4',
      realField: '[name="category"]',
      level: 4,
      'default': '',
      success: function() {

      },
      falseClear: true,
      cate1Cb: function($cate1) {

      },
      cate2Cb: function($cate2) {

      },
      cate3Cb: function($cate3) {

      },
      cate4Cb: function($cate4) {

      }
    }, options || {});
    var $cate1 = $category.find(options.cate1);
    var $cate2 = $category.find(options.cate2);
    var $cate3 = $category.find(options.cate3);
    var $cate4 = $category.find(options.cate4);
    var $field = $category.find(options.realField);
    createOptions($cate1, render(categorys));
    $category.on('change', options.cate1, function(event) {
      var $t = $(this);
      var cate1 = $t.val();
      options.cate1Cb($t);
      clearSelect($cate2);
      clearSelect($cate3, '二');
      clearSelect($cate4, '三');
      clearRealField($field, options.falseClear);
      if (cate1 === 'default') return clearSelect($cate2, '一');
      getData('warehouseId',cate1, function(data) {
        if (data.data === '') {
          disableSelect($cate2);
          disableSelect($cate3);
          disableSelect($cate4);
          $field.val(cate1);
          options.success(cate1);
          return;
        }
        createOptions($cate2, data.data);
      });
    }).on('change', options.cate2, function(event) {
      var $t = $(this);
      var cate2 = $t.val();
      options.cate2Cb($t);
      clearSelect($cate3);
      clearSelect($cate4,'三');
      clearRealField($field, options.falseClear);
      if (cate2 === 'default') return clearSelect($cate3, '二');
      getData('parent',cate2, function(data) {
        if (data.data === '' || options.level === 2) {
          disableSelect($cate3);
          disableSelect($cate4);
          $field.val(cate2);
          options.success(cate2);
          return;
        }
        createOptions($cate3, data.data);
      });
    }).on('change', options.cate3, function(event) {
      var $t = $(this);
      var cate3 = $t.val();
      options.cate3Cb($t);
      clearSelect($cate4);
      clearRealField($field, options.falseClear);
      if (cate3 === 'default') return clearSelect($cate4, '三');
      getData('parent',cate3, function(data) {
        if (data.data === '' || options.level === 3) {
          disableSelect($cate4);
          $field.val(cate3);
          options.success(cate3);
          return;
        }
        createOptions($cate4, data.data);
      });
    }).on('change', options.cate4, function(event) {
      var $t = $(this);
      var val = $t.val();
      options.cate4Cb($t);
      if (val !== 'default') {
        $field.val(val);
        options.success(val);
      } else {
        clearRealField($field, options.falseClear);
      }
    });

    if (options['default']) {
      getData('warehouseId',options['default'], function(cate1) {
        if (cate1.parent === '') {
          $cate1.val(options['default']).trigger('change');
          return;
        }
        getData('parent',cate1.parent, function(cate2) {
          if (cate2.parent === '') {
            createOptions($cate2, cate1.data);
            $cate1.val(cate1.parent).trigger('change');
            $cate2.val(options['default']).trigger('change');
            return;
          }
          getData('parent',cate2.parent, function(cate3) {
            if (cate3.parent === '') {
              createOptions($cate3, cate2.data);
              createOptions($cate2, cate3.data);
              $cate2.val(cate3.parent).trigger('change');
              $cate3.val(options['default']).trigger('change');
              return;
          }
          getData('parent',cate3.parent, function(cate4) {
          createOptions($cate4, cate3.data);
          createOptions($cate3, cate4.data);
          $cate3.val(cate4.parent).trigger('change');
          $cate4.val(options['default']).trigger('change');
          });
        });
      });
      });
    }

  };
});