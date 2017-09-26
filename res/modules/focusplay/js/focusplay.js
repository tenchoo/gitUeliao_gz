define(function(require, exports, module) {

  function playStyle(li, style) {
    var
      ul = li.parent(),
      n = li.index();
    ul.stop(true);
    switch (style) {
      case 1:
      case "top":
        ul.animate({
          marginTop: -li.height() * n
        }, "fast");
        break;
      case 2:
      case "left":
        ul.animate({
          marginLeft: -li.width() * n
        }, "normal");
        break;
      default:
        li.fadeIn("normal").siblings().fadeOut("fast");
    }
  }

  exports.playStyle = playStyle;

  exports.play = function(id, li, time, cur, style, tStyle) {
    var
      $id = $(id);
      li = li || "ul>li";
    var $uli = $id.find(li),
      $tli = $id.find("ul.t>li"),
      $length = $uli.length,
      $ol = '<ol><li class="active">1</li>',
      n = 1,
      playIng;
    time = time || 6000;
    cur = cur || "active";
    style = style || 0;
    tStyle = tStyle || 0;
    if ($length < 2) {
      return;
    }
    for (var i = 2; i <= $length; i++) {
      $ol += "<li>" + i + "</li>";
    }
    $ol += "</ol>";
    $id.append($ol);
    $id.find('ul>:not(li)').remove();
    $ol = $id.find("ol>li");
    var

      play = function() {
      $uli.stop(true, true);
      $ol.stop(true, true);
      playStyle($uli.eq(n), style);
      if ($tli.length) {
        $tli.stop(true, true);
        playStyle($tli.eq(n), tStyle);
      }
      $ol.eq(n).addClass(cur).siblings().removeClass();
      n = (n == $length - 1) ? 0 : n + 1;
    };
    playIng = setInterval(play, time);
    var hover = function(w) {
      w.hover(function() {
        clearInterval(playIng);
        n = $(this).index();
        play();
      }, function() {
        playIng = setInterval(play, time);
      });
    };
    hover($uli);
    hover($ol);
  };
});