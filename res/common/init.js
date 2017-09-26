//var host = /\w+\.[\w\:]+$/.exec(location.host)[0];
var arr = (location.host).split(".");
arr.shift();
var host = (arr.toString()).replace(/,/g, ".");

seajs.config({

  // 别名配置
  alias: {

  },
  // 路径配置
  paths: {
    'api': location.protocol + '//member.' + host
  },



  // 预加载项
  preload: [

  ],

  // 调试模式
  debug: false,

  // Sea.js 的基础路径，也就是前端服务器
  base: location.protocol + '//res.' + host + '/',

  // 图片上传服务器
  uploaderPath: location.protocol + '//images.' + host + '/api/upfile',

  // ajax api接口
  apiPath: location.protocol + '//member.' + host
});

//全局支持ctrl+enter提交
$(document).on('keypress', 'form', function(event) {
  if (event.ctrlKey && (10 == event.which || 13 == event.which)) {
    $(this).trigger('submit');
  }
});

$(function() {
  $('[data-ad]').each(function() {
    var $t = $(this);
    $.get(seajs.data.apiPath + $t.data('ad'), function(res) {
      if (res.state) {
        if (/^<a/i.test(res.data)) {
          $t.html(res.data);
        }
        if (/^<li/i.test(res.data)) {
          $t.html('<ul class="list-unstyled">' + res.data.replace('<li', '<li class="active"') + '</ul>');
          seajs.use('modules/focusplay/js/focusplay.js', function(player) {
            player.play($t);
          });
        }
      }
    }, 'jsonp');
  });
});
