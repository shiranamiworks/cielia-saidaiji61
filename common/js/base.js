/* ============================================================ 

// サイト独自の共通js

=============================================================== */

// スクロールすると画像を表示 //
$(function() {
	$('.fade,.fade01,.fade02,.fade03,.fade04,.fade05,.delay01,.delay02,.delay03,.delay04,.delay05, .fadeBT, .fadeTB, .fadeLR, .fadeRL').on('inview', function(event, isInView, visiblePartX, visiblePartY) {
		if(isInView){
			$(this).stop().addClass('mv');
		}
	});
});


// spモーダル //
$(function() {
  if($('.commonSpModalImage').length){
    $('body').append('<div id="spCommonModalArea"><div id="spCommonModalTable"><div id="spCommonModalCell"><div id="spCommonModalInner"><div id="spCommonModalContents"></div><div id="spCommonModalClose"></div></div></div></div></div>');
    $('.commonSpModalImage').on('click', function(){
      const
        material = $(this).attr('data-spmodal'),
        element = '<figure><img src="'+material+'"></figure>';
      $('#spCommonModalContents').empty();
      $('#spCommonModalContents').removeClass('icoOff');
      $('#spCommonModalContents').append(element);
      $('#spCommonModalArea').fadeIn(300);
    });
    $('#spCommonModalClose,#spCommonModalArea').on('click', function(){
      $('#spCommonModalArea').fadeOut(300);
    });
    $('#spCommonModalContents').on('click', function(e){
      e.stopPropagation();
    });
    $('#spCommonModalContents').on('scroll',function(){
      if($('#spCommonModalContents').scrollLeft() > 20){
        $('#spCommonModalContents').addClass('icoOff');
      }
    });
  }
});



// PCモーダル //
$(function() {
  if($('.commonPcModalImage').length){
    $('body').append('<div id="pcCommonModalArea"><div id="pcCommonModalTable"><div id="pcCommonModalCell"><div id="pcCommonModalInner"><div id="pcCommonModalContents"></div><div id="pcCommonModalClose"></div></div></div></div></div>');
    $('.commonPcModalImage').on('click', function(){
      const
        material = $(this).attr('data-spmodal'),
        element = '<figure><img src="'+material+'"></figure>';
      $('#pcCommonModalContents').empty();
      $('#pcCommonModalContents').removeClass('icoOff');
      $('#pcCommonModalContents').append(element);
      $('#pcCommonModalArea').fadeIn(300);
    });
    $('#pcCommonModalClose,#spCommonModalArea').on('click', function(){
      $('#pcCommonModalArea').fadeOut(300);
    });
    $('#pcCommonModalContents').on('click', function(e){
      e.stopPropagation();
    });
    $('#pcCommonModalContents').on('scroll',function(){
      if($('#pcCommonModalContents').scrollLeft() > 20){
        $('#pcCommonModalContents').addClass('icoOff');
      }
    });
  }
});