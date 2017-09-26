define(function(require, exports, module) {
  var template = require('libs/arttemplate/3.0.0/template.js');
  module.exports = function($express, data) {
    $.get(seajs.data.apiPath + '/ajax/express', data, function(res) {
      var list = res.state ? res.data : [{
        time: '暂无物流信息'
      }];
      $express.html(template('expressInfo', {
        list: list
      }));
    }, 'jsonp');
  };
});