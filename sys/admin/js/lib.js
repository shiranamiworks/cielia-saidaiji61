
// global navigation active
var active_tag = '<div class="pointer"><div class="arrow"></div><div class="arrow_border"></div></div>';
function gNaviActive(target) {
  $("#"+target).addClass('active');
  /*
  if($("#"+target).children("a").hasClass("dropdown-toggle")) {
    $("#"+target).prepend(active_tag);
  } else {
    $("#"+target).children("a").prepend(active_tag);
  }
  */
}

function subNaviActive(target , classname) {
  $("#"+target+" .submenu_items").addClass('active');
  $("#"+target+" .submenu_items ."+classname).addClass('active');
}


var navAnimation = function(){

  $('#gNavi dt.menu_title a').click(function(e){

    //slide open
    $(this).parent().parent().find("dd").slideToggle('fast',function(){
      var target = $(this).parent().find("dt");
      if(target.hasClass("open"))
      {
        target.removeClass("open");
      }
      else
      {
        target.addClass("open");
      }
    });
    
    e.stopPropagation();
    return false;
  });

};

$(function(){
  $("#sidebar-nav").css("min-height" , ($(window).height() - $("header").height()));
});

function escapedHtml(val) {
  return $('<div />').text(val).html();
}