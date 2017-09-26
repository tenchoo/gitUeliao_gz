define(function(require, exports, module) {
  var editor = require('modules/editor/js/editor.js');
  var template = require('libs/arttemplate/3.0.0/template.js');
  editor('textarea');
  var $cate1 = $('.cate1');
  var $realCate = $('input[name="data[categoryId]"]');
  var cache = {};


  function render(data) {
    return template.compile('<select  class="pull-left form-control input-sm cate2" style="width:40%;margin-left:20px"><option value="">请选择分类</option>{{each list}}<option value="{{$value.categoryId}}">{{$value.title}}</option>{{/each}}</select>')({
      list: data
    });
  }

  $cate1.on('change', function(e) {
    var val = this.value;
    var $item = $cate1.find('option[value="' + val + '"]');
    $cate1.next('.cate2').remove();
    if ($item.data('child') !== 1) {
      $realCate.val(val);
      return;
    }
    $realCate.val('');
    if (cache[val]) {
      $cate1.after(cache[val]);
      return;
    }
    $.get('/content/helpcategory/getcategory', {
      parentId: val
    }, function(res) {
      if (res.state) {
        cache[val] = render(res.data);
        $cate1.after(cache[val]);
      }
    }, 'json');
  });

  $('.content-wrap').on('change', '.cate2', function(e) {
    $realCate.val(this.value);
  });

});