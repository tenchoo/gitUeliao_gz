define(function(require, exports, module) {
  var $content = $('.content-wrap');
  
  $content.on('click','.assign',function(event){
    event.preventDefault();
    var $t =$(this);
    var $tr = $t.parents('tr:first');
    var $state = $tr.find('.assign-state');
    var $info = $tr.find('.assign-info');
    if ( $t.html() == '匹配'){
      $t.html('取消');
      $state.html('已匹配');
      $info.prop('disabled',false);
    } else {
      $t.html('匹配');
      $state.html('未匹配');
      $info.prop('disabled',true);
    }
  });
  
});