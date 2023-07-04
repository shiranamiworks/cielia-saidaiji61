/* ============================================================ 
ver.2021.02.17

//CRM領域

=============================================================== */

/*--------------------------------------

// 外部ファイル読み込み //

--------------------------------------*/
function parts(rootDir,File,current){
  $.ajax({
    url: rootDir + "library/" + File,
    cache: false,
    async: false,
    dataType: 'html',
    success: function(html){
      html = html.replace(/\{\$root\}/g, rootDir);
      html = html.replace('nav_'+current, 'on nav_'+current);
      document.write(html);
    }
  });
}



/*--------------------------------------

 jQuery Actions

--------------------------------------*/
(function($){

  // 初期設定
  const default_set = function() {
    $('a[href^="tel:"]').on('click', function(){
      if( window.ontouchstart === null ){
        return;
      } else {
        return false;
      }
    });
  }

  // スムーススクロール
  const smooth = function() {
    $('a.smooth, .smoothList a').click(function(){
      const
        speed = 500,
        href = $(this).attr('href'),
        target = '#' + href.substring(href.indexOf("#")+1,href.length),
        el = $(target);
      if(el.length){
        const
          position = el.offset().top;
        $('html, body').animate({scrollTop:position}, speed, 'swing');
        return false;
      }
    });
  }

  // sp時のアクション
  const spActions = function(){
    const spTitleBtnTxt = $('.crm--header .mainNav li.on a').text();
    $('.crm--header .globalnavi .spTitleBtn span').text(spTitleBtnTxt);
    $('.crm--header .globalnavi .spTitleBtn').on('click',function(){
      $(this).toggleClass('on');
      $('.crm--header .spAccordions').stop().slideToggle(400);
    });
    $('.crm--header .spAccordions a').on('click',function(){
      const ww = $(window).width();
      if(ww<1281){
        $('.crm--header .globalnavi .spTitleBtn').removeClass('on');
        $('.crm--header .spAccordions').slideUp(400);
      }
    });
  }
  const spJsReset = function() {
    var
      mql = window.matchMedia('screen and (max-width: 1200px)')
    function checkBreakPoint(mql) {
      if (!(mql.matches)) {
        $('.crm--header .globalnavi .spTitleBtn').removeClass('on');
        $('.crm--header .spAccordions').removeAttr('style');
      }
    }
    mql.addListener(checkBreakPoint);
    checkBreakPoint(mql);
  }

  // globalnaviのアクション
  const globalnavi = function(){
    if($('.crm--header .globalnavi').length){
      const
        pos = $('.crm--header .globalnavi').offset().top,
        scroll = $(window).scrollTop();
      if(scroll>pos){
        $('.crm--header .globalnavi').addClass('fixed');
      } else {
        $('.crm--header .globalnavi').removeClass('fixed');
      }
    } else {
      $('#wrapper').addClass('globalnavi_off');
    }
  }

  // crm--cieriaclub js
  const cieriaclub = function(){
    if($('.crm--cieriaclub .list').length){
      $('.crm--cieriaclub .list').slick({
        arrows: false,
        slidesToShow: 3,
        responsive: [
          {
            breakpoint: 768,
            settings: {
              centerMode: true,
              centerPadding: '15px',
              slidesToShow: 1,
              slidesToScroll: 1,
              infinite: true,
              dots: true
            }
          }
        ]
      });
    }
  }

  // crm--requestbenefits js
  const requestbenefits = function(){
    if($('.crm--requestbenefits .list').length){
      $('.crm--requestbenefits .list').slick({
        arrows: false,
        slidesToShow: 3,
        responsive: [
          {
            breakpoint: 768,
            settings: {
              centerMode: true,
              centerPadding: '15px',
              slidesToShow: 1,
              slidesToScroll: 1,
              infinite: true,
              dots: true
            }
          }
        ]
      });
    }
  }

  // crm--banners js
  const banners = function(){
    if($('.crm--banners .list').length){
      $('.crm--banners .list').each(function(index, el) {
        const slideControls = $(this).closest('.crm--banners').find('.slideControls .el');
        $(this).slick({
          variableWidth: true,
          dots: true,
          // autoplay: true,
          // autoplaySpeed: 3000,
          // speed: 700,
          appendDots: slideControls,
          appendArrows: slideControls,
          responsive: [
            {
              breakpoint: 1350,
              settings: {
                variableWidth: false,
                slidesToShow: 2,
                slidesToScroll: 1,
                infinite: true,
                dots: true
              }
            },
            {
              breakpoint: 768,
              settings: {
                variableWidth: false,
                slidesToShow: 1,
                slidesToScroll: 1,
                infinite: true,
                dots: true
              }
            }
          ]
        });
      });
    }
  }

  // pageTopのアクション
  const pageTop = function(){
      const
        sc = $(window).scrollTop(),
        wh = window.innerHeight;
      if(sc > 200){
        $('.crm--pageTop').addClass('on');
      } else {
        $('.crm--pageTop').removeClass('on');
      }
  }


/* dom ready
-----------------------*/
  $(function() {
    default_set();
    spActions();
    spJsReset();
    cieriaclub();
    smooth();
    // requestbenefits();
    banners();
  });


/* dom load & scroll
-----------------------*/
  $(window).on('load scroll',function(){
    globalnavi();
    pageTop();
  });

	$(function() {

		var switch_flg = false;
		var main_h;
		var scrolling;
		
		$(window).on("scroll load", function(){
			var main_h = $(".commonKeyvisual").height() / 1.2;
			var scrolling = $(window).scrollTop();
			if(scrolling >= main_h && !switch_flg){
				switch_flg = true;
				$('.fadeActionArea').slick('slickPlay');
			}
		});


    $('body').append('<div id="movieModalArea"><div id="movieModalTable"><div id="movieModalCell"><div id="movieModalInner"><div id="movieModalContents"></div><div id="movieModalClose"></div></div></div></div></div>');
    $('.partsMovieModal').on('click', function(){
      const
        link = $(this).attr('href'),
        el = '<iframe width="560" height="315" src="https://www.youtube.com/embed/'+link+'?&mute=1&autoplay=1&loop=1&playlist='+link+'" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>'
      $('#movieModalContents').empty();
      $('#movieModalContents').append(el);
      $('#movieModalArea').fadeIn(300);
      return false;
    });
    $('#movieModalClose,#movieModalArea').on('click', function(){
      $('#movieModalArea').fadeOut(300,function(){
        $('#movieModalContents').empty();
      });
    });
    $('#movieModalContents').on('click', function(e){
      e.stopPropagation();
    });

	});
	
	
})(jQuery);


