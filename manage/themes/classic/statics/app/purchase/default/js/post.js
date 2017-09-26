define(function(require, exports, module) {
  var $content = $('.content-wrap');

  $content.on('click','.del',function(event){
    event.preventDefault();
    if(!confirm('确定删除？')) return;
    $(this).parents('tr:first').remove();
  });


});