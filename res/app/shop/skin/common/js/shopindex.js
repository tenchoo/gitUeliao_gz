define(function(require, exports, module) {
  require('app/shop/skin/common/js/jquery.SuperSlide.js');
  $(".banner_c").slide({
    titCell:".hd ul", mainCell:".bd ul", effect:"fold",  autoPlay:true, autoPage:true, trigger:"click"
  }).hover(
      function(){ $(this).find(".prev,.next").stop(true,true).fadeTo("show",0.2) },
      function(){ $(this).find(".prev,.next").fadeOut() }
  );
  $("#leftMarquee").slide({ mainCell:".bd",effect:"leftMarquee",vis:6,interTime:40,autoPlay:true });
});