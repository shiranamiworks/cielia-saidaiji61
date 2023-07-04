window.onload = function() {
  const loadWrap = document.getElementById('load');
  loadWrap.classList.add('loaded');
}


$(function(){
  const init = {
      fade: true,
      slidesToShow: 1,
      autoplay: true,
      autoplaySpeed: 4000,
      speed: 1000,
      dots: true,
      customPaging: function(slick,index) {
        var targetTextt = slick.$slides.eq(index).find('.ttl').html();
        return '<p>' + targetTextt + '</p>';
      }
    },
    mql = window.matchMedia('screen and (max-width: 768px)');
  function checkBreakPoint(mql) {
    if (mql.matches) {
      $('#charmArea .slideArea.slick-initialized').slick('unslick');
    } else {
      $('#charmArea .slideArea').not('.slick-initialized').slick(init);
    }
  }
  mql.addListener(checkBreakPoint);
  checkBreakPoint(mql);


  $('body').append('<div id="modalArea"><div id="modalTable"><div id="modalCell"><div id="modalInner"><div id="modalContents"></div><div id="modalClose"></div></div></div></div></div>');
  $('.modalBtn').on('click', function(){
    const
      link = $(this).attr('href'),
      el = '<iframe width="560" height="315" src="https://www.youtube.com/embed/'+link+'?&mute=1&autoplay=1&loop=1&playlist='+link+'" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>'
    $('#modalContents').empty();
    $('#modalContents').append(el);
    $('#modalArea').fadeIn(300);
    return false;
  });
  $('#modalClose,#modalArea').on('click', function(){
    $('#modalArea').fadeOut(300,function(){
      $('#modalContents').empty();
    });
  });
  $('#modalContents').on('click', function(e){
    e.stopPropagation();
  });
});


