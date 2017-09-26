//var host = /\w+\.[\w\:]+$/.exec(location.host)[0];

var arr = (location.host).split(".");
arr.shift();
var host = (arr.toString()).replace(/,/g, ".");

seajs.config({


  // 路径配置
  paths: {
    'localApi': location.protocol + '//' + location.host,
    'statics': '/themes/classic/statics',
    'libs': '/themes/classic/statics/libs',
    'modules': '/themes/classic/statics/modules',
    'api': location.protocol + '//member.' + host
  },

  // 别名配置
  alias: {
    vue: 'libs/vue/1.0.24/vue.min.js'
  },

  // 预加载项
  preload: [

  ],

  // 调试模式
  debug: false,

  // 图片上传服务
  uploaderPath: location.protocol + '//images.' + host + '/api/upfile',

  // ajax api接口
  apiPath: location.protocol + '//member.' + host

  // Sea.js 的基础路径
  //base: '//res.'+host+'/'

});

//全局支持ctrl+enter提交
$(document).on('keypress', 'form', function(event) {
  if (event.ctrlKey && (10 == event.which || 13 == event.which)) {
    $(this).trigger('submit');
  }
});

var leftNav = true;

$(function() {
  var $frameset = $('frameset ', parent.document.body);
  var frameContent = parent.window.frameContent;
  $('.content-wrap').append('<div class="hide-left"><a href="javascript:" class="glyphicon glyphicon-menu-left"></a></div>');
  $('.hide-left').on('click', 'a', function(e) {
    e.preventDefault();
    var $t = $(this);
    if (leftNav) {
      $t.removeClass('glyphicon-menu-left').addClass('glyphicon-menu-right');
      $frameset.attr('cols', '0,*');
    } else {
      $frameset.attr('cols', '240,*');
      $t.removeClass('glyphicon-menu-right').addClass('glyphicon-menu-left');
    }
    leftNav = !leftNav;
  });
  window.showLeftNav = function() {
    leftNav = true;
    $frameset.attr('cols', '240,*');
    $('.hide-left a').removeClass('glyphicon-menu-right').addClass('glyphicon-menu-left');
  };

  $('.head-nav').on('click', 'a', function(e) {
    $(this).parent().addClass('active').siblings('.active').removeClass('active');
    if (!frameContent.leftNav) frameContent.showLeftNav();
  }).find('li:first').addClass('active');
});

var $bd = $('body');
seajs.use('libs/my97datepicker/4.8.0/WdatePicker.js', function() {
  $bd.on('click', '.input-date', WdatePicker);
});

$.ajaxSetup({
  error: function(XMLHttpRequest, textStatus, errorThrown) {
    switch (textStatus) {
      case 'parsererror':
        alert('接口请求发生错误！');
        break;
      case 'timeout':
        alert('网络请求出错，请刷新页面重试！');
        break;
      default:
        console.log(XMLHttpRequest, textStatus, errorThrown);
    }
  }
});
