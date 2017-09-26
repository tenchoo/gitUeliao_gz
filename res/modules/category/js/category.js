define(function(require, exports, module) {
  var categorys = require('api/ajax/category?callback=define')['data']['childs'];
  var template = require('libs/arttemplate/3.0.0/template.js');
  var caches = {};

  function render(data) {
    return template.compile('{{each list}}<option value="{{$index}}">{{$value}}</option>{{/each}}')({
      list: data
    });
  }

  function clearSelect($select, level) {
    $select.removeClass('input-disabled').prop('disabled', false).html('<option value="default">' + (level ? '请先选择' + level + '级类目' : '请选择') + '</option>');
  }

  function createOptions($select, data, defaultVal) {
    var haSize = $select.removeClass('input-disabled').prop('disabled', false).is('[size]');
    $select[haSize ? 'html' : 'append'](data).val(defaultVal || 'default');
  }

  function disableSelect($select) {
    $select.addClass('input-disabled').prop('disabled', true).html('<option value="">暂无分类</option>');
  }

  function clearRealField($field, clear) {
    if (!clear) return;
    $field.val('');
  }

  function getData(cateId, cb) {
    if (caches[cateId]) {
      return cb(caches[cateId]);
    }
    $.get(seajs.data.apiPath + '/ajax/category', {
      categoryid: cateId
    }, function(res) {
      if (res.state) {
        caches[cateId] = {
          parent: res.data.parent,
          data: render(res.data.childs)
        };
        cb(caches[cateId]);
        return;
      }
      //ajax出错处理

    }, 'jsonp');
  }


  exports.select = function($category, options) {
    options = $.extend({
      cate1: '.cate1',
      cate2: '.cate2',
      cate3: '.cate3',
      realField: '[name="category"]',
      level: 3,
      'default': '',
      success: function() {

      },
      falseClear: true,
      cate1Cb: function($cate1) {

      },
      cate2Cb: function($cate2) {

      },
      cate3Cb: function($cate3) {

      }
    }, options || {});
    var $cate1 = $category.find(options.cate1);
    var $cate2 = $category.find(options.cate2);
    var $cate3 = $category.find(options.cate3);
    var $field = $category.find(options.realField);
    createOptions($cate1, render(categorys));
    $category.on('change', options.cate1, function(event) {
      var $t = $(this);
      var cate1 = $t.val();
      options.cate1Cb($t);
      clearSelect($cate2);
      clearSelect($cate3, '二');
      clearRealField($field, options.falseClear);
      if (cate1 === 'default') return clearSelect($cate2, '一');
      getData(cate1, function(data) {
        if (data.data === '') {
          disableSelect($cate2);
          disableSelect($cate3);
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
      clearRealField($field, options.falseClear);
      if (cate2 === 'default') return clearSelect($cate3, '二');
      getData(cate2, function(data) {
        if (data.data === '' || options.level === 2) {
          disableSelect($cate3);
          $field.val(cate2);
          options.success(cate2);
          return;
        }
        createOptions($cate3, data.data);
      });
    }).on('change', options.cate3, function(event) {
      var $t = $(this);
      var val = $t.val();
      options.cate3Cb($t);
      if (val !== 'default') {
        $field.val(val);
        options.success(val);
      } else {
        clearRealField($field, options.falseClear);
      }
    });

    if (options['default']) {
      getData(options['default'], function(cate1) {
        if (cate1.parent === '') {
          $cate1.val(options['default']).trigger('change');
          return;
        }
        getData(cate1.parent, function(cate2) {
          if (cate2.parent === '') {
            createOptions($cate2, cate1.data);
            $cate1.val(cate1.parent).trigger('change');
            $cate2.val(options['default']).trigger('change');
            return;
          }
          getData(cate2.parent, function(cate3) {
            createOptions($cate3, cate2.data);
            createOptions($cate2, cate3.data);
            $cate1.val(cate2.parent).trigger('change');
            $cate2.val(cate1.parent).trigger('change');
            $cate3.val(options['default']).trigger('change');
          });
        });
      });

    }

  };
});