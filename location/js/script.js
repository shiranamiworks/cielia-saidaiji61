(function($){
  $(function(){
    $('.mapWrap > div iframe').each(function(){
      $(this).attr('src2',$(this).attr('src'));
      $(this).attr('src','');
    });
  });
  $(window).on('load',function(){
    $('.mapWrap > div').hide();
    $('.mapWrap > div.active').show();
    var tgt_wrap = '.mapWrap > div.active';
    $(tgt_wrap+" iframe").attr("src",$(tgt_wrap+" iframe").attr("src2"));
    $('.mapWrapper > ul > li').on('click',function(){
      $('.mapWrapper > ul > li').removeClass("active");
      var tgt = $(this).attr('class');
      $(this).addClass('active');
      $('.mapWrap > div').stop().fadeOut(500);
      var tgt_wrap = '.mapWrap > div.'+tgt;
      $(tgt_wrap).stop().fadeIn(500);
      $(tgt_wrap+':not(.active) iframe').attr('src',$(tgt_wrap+' iframe').attr('src2'));
      $(tgt_wrap).addClass('active');
    });
  });
})(jQuery);