(function($){
  $(function(){

    $('.modalSlideAera').slick({
        dots: true,
        fade: true,
        customPaging: function(slick,index) {
            var targetImage = slick.$slides.eq(index).find('img').attr('src');
            return '<img src=" ' + targetImage + ' "/>';
        },
        appendDots: $('.thumbnails')
    });

  });
})(jQuery);