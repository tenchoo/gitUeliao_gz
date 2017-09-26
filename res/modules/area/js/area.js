define(function(require, exports, module) {
  var dialog = require('modules/dialog/js/dialog.js');
  var template = require('libs/arttemplate/3.0.0/template.js');
  var render = {
    option: function(data) {
      return template.compile('{{each list}}<option value="{{$index}}">{{$value}}</option>{{/each}}')({
        list: data
      });
    },
    group: function(data) {
      return template.compile('<div class="area-checkbox">' +
        '  <ul class="list-unstyled">' +
        '    {{each groups}}<li class="clearfix area-item">' +
        '      <div class="pull-left area-hd">' +
        '        <label class="checkbox-inline area-label"><input type="checkbox" value="group-{{$index}}">{{$value.title}}</label>' +
        '      </div>' +
        '      <ul class="list-unstyled list-inline area-bd">' +
        '        {{each $value.childs}}<li>' +
        '          <div class="city-hd">' +
        '            <label class="checkbox-inline province-label"><input type="checkbox" value="{{$index}}">{{$value.title}}<span class="arr"><i></i></span></label>' +
        '          </div>' +
        '          <div class="city-bd">' +
        '            <ul class="list-unstyled list-inline">' +
        '              {{each $value.childs}}<li><label class="checkbox-inline"><input type="checkbox" value="{{$index}}">{{$value}}</label></li>{{/each}}' +
        '            </ul>' +
        '            <button type="button" class="btn btn-success btn-xs close">关闭</button>' +
        '          </div>' +
        '        </li>{{/each}}' +
        '      </ul>' +
        '    </li>{{/each}}' +
        '  </ul>' +
        '</div>')({
        groups: data
      });
    }
  };
  var maps = {
    'province': '省份',
    'city': '市',
    'county': '区/县'
  };

  var AREAS = require('api/ajax?action=levelcitys&callback=define')['data'];
  var $body = $('body');
  template.config('compress', true);

  function formatAreaData(data) {
    var groups = {};
    for (var i in data.provinces) {
      if (groups[data.provinces[i].groups]) {
        groups[data.provinces[i].groups].childs.push(i);
      } else {
        groups[data.provinces[i].groups] = {
          title: data.provinces[i].groupsname,
          childs: [i]
        };
      }
      data.provinces[i] = data.provinces[i].title;
    }
    data.groups = groups;
    return data;
  }

  AREAS = formatAreaData(AREAS);

  function createOptions($select, data, defaultVal) {
    $select.append(data).val(defaultVal || 'default');
  }

  function clearSelect($select, key) {
    $select.html('<option value="default">请选择' + (maps[key]) + '</option>');
  }


  function getData(areaId, level, cb) {
    return cb(AREAS[level][areaId]);
  }

  function setDefault(options) {
    getData(options.city, 'countys', function(data) {
      createOptions(options.$county, render.option(data), options.county);
    });

    $.get(seajs.data.apiPath + '/ajax/', {
      action: 'dynamiccitys',
      areaid: options.city
    }, function(res) {
      if (res.state) {
        data = res.data;
        options.$province.val(data.parent);
        getData(data.parent, 'citys', function(data) {
          createOptions(options.$city, render.option(data), options.city);
        });
        return;
      }
      //ajax出错处理

    }, 'jsonp');
  }

  function clearRealField($field, clear) {
    if (!clear) return;
    $field.val('0');
  }


  function changeChecked($checkbox, $area) {
    var checked = $area.find('ul :checked');
    var unChecked = $area.find('ul input:not(:checked)');
    if (unChecked.length < 1) {
      $checkbox.prop('indeterminate', false);
      $checkbox.prop('checked', true);
      return;
    }
    $checkbox.prop('checked', false);
    $checkbox.prop('indeterminate', !!checked.length);
  }


  function createProvinces(provinces) {
    var data = {};
    for (var i in provinces) {
      data[provinces[i]] = {
        title: AREAS.provinces[provinces[i]],
        childs: AREAS.citys[provinces[i]]
      };
    }
    return data;
  }

  function createCheckbox() {
    var groups = [];
    for (var i in AREAS.groups) {
      groups.push($.extend({}, AREAS.groups[i], {
        childs: createProvinces(AREAS.groups[i].childs)
      }));
    }
    return render.group(groups);
  }

  function bindCheckEvent($areaChcekbox) {
    $body.on('click', function(event) {
      $areaChcekbox.find('.active').removeClass('active');
    });
    $areaChcekbox.on('click', '.active', function(event) {
      event.stopPropagation();
    }).on('click', '.arr', function(event) {
      var $t = $(this);
      var $province = $t.parents('.city-hd').parent();
      if ($province.is('.active')) {
        $province.removeClass('active');
      } else {
        $areaChcekbox.find('.active').removeClass('active');
        $province.addClass('active');
      }
    }).on('click', 'label', function(event) {
      if ($(event.target).is('span.arr') || $(event.target).is('i')) return false;
    }).on('change', '.city-bd input', function(event) {
      var $province = $(this).parents('.city-bd').parent();
      var $area = $province.parents('.area-item');
      changeChecked($province.find('.province-label input'), $province);
      changeChecked($area.find('.area-label input'), $area);
    }).on('change', '.city-hd input', function(event) {
      var $t = $(this);
      var $province = $t.parents('.city-hd').parent();
      var $area = $province.parents('.area-item');
      $province.find('input').prop('checked', $t.prop('checked'));
      changeChecked($area.find('.area-label input'), $area);
    }).on('change', '.area-label input', function(event) {
      var $t = $(this);
      var $area = $t.parents('.area-item');
      var $checkbox = $area.find('ul input');
      $checkbox.prop('indeterminate', false);
      $checkbox.prop('checked', $t.prop('checked'));
    }).on('click', '.close', function(event) {
      $(this).parents('.active').removeClass('active');
    });
  }

  function defaultCheckbox(areas, groups) {
    for (var i in areas) {
      groups = groups.replace('value="' + areas[i] + '"', 'value="' + areas[i] + '" checked');
    }
    return groups;
  }

  exports.select = function($area, options) {
    options = $.extend({
      province: '.province',
      city: '.city',
      county: '.county',
      realField: '[name="address"]', //用于表单提交的字段
      success: function() {}, //选择正确的回调
      'default': '',
      falseClear: true //错误时是否自动清除字段的值
    }, options || {});
    var $province = $area.find(options.province);
    var $city = $area.find(options.city);
    var $county = $area.find(options.county);
    var $field = $area.find(options.realField);
    createOptions($province, render.option(AREAS.provinces));
    $area.on('change', options.province, function(event) { //省
      var province = $(this).val();
      clearSelect($county, 'county');
      clearSelect($city, 'city');
      getData(province, 'citys', function(data) {
        createOptions($city, render.option(data));
      });
      clearRealField($field, options.falseClear);
    }).on('change', options.city, function(event) { //市
      var city = $(this).val();
      clearSelect($county, 'county');
      getData(city, 'countys', function(data) {
        createOptions($county, render.option(data));
      });
      clearRealField($field, options.falseClear);
    }).on('change', options.county, function(event) { //县
      var val = $(this).val();
      if (val !== 'default') {
        $field.val(val);
        options.success(val);
      } else {
        clearRealField($field, options.falseClear);
      }
    });

    if (options['default']  && options['default']!= '0' ) {
      $.get(seajs.data.apiPath + '/ajax/', {
        action: 'dynamiccitys',
        areaid: options['default']
      }, function(res) {
        if (res.state) {
          setDefault({
            '$province': $province,
            '$city': $city,
            '$county': $county,
            city: res.data.parent,
            county: options['default']
          });
          return;
        }
        //ajax出错处理

      }, 'jsonp');
    }
  };

  exports.checkbox = function($area) {
    var groups = createCheckbox();
    return {
      open: function(options) {

        options = $.extend({
          okFun: function() {

          }
        }, options || {});

        dialog.confirm(options['default'] ? defaultCheckbox(options['default'].split(','), groups) : groups, $.extend({}, options, {
          okFun: function($dialog) {
            var checkeds = [];
            var texts = [];
            $dialog.find('.city-hd input:checked').parents('.city-hd').next().remove();
            $dialog.find('.area-bd input:checked').each(function() {
              var $t = $(this);
              checkeds.push($t.val());
              texts.push($t.parent().text());
            });
            options.okFun(checkeds, texts);
          },
          init: function() {
            var $areaChcekbox = $('.area-checkbox');
            bindCheckEvent($areaChcekbox);
            $areaChcekbox.find('.city-hd').each(function() {
              var $t = $(this);
              var $province = $t.find('input');
              $province.filter(':checked').trigger('change');
              changeChecked($province, $t.parent());
            });
            $areaChcekbox.find('.area-hd').each(function() {
              var $t = $(this);
              changeChecked($t.find('input'), $t.parent());
            });
          }
        }));
      }
    };
  };

});