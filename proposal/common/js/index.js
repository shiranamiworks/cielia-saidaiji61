$(window).on('load', function() {
  $('body').attr('data-loading', 'true');
});

/* 外部ファイル */
function parts(rootDir,File){
    $.ajax({
        url: rootDir + "library/" + File,
        cache: false,
        async: false,
        dataType: 'html',
        success: function(html){
            html = html.replace(/\{\$root\}/g, rootDir);
            document.write(html);
        }
    });
}

/* ページトップ */
$(function() {
    var showFlag = false;
    var topBtn = $('.page-top');    
    topBtn.css('bottom', '-100px');
    var showFlag = false;
    //スクロールが100に達したらボタン表示
    $(window).scroll(function () {
        if ($(this).scrollTop() > 350) {
            if (showFlag == false) {
                showFlag = true;
                topBtn.stop().animate({'bottom' : '10%'}, 400); 
            }
        } else {
            if (showFlag) {
                showFlag = false;
                topBtn.stop().animate({'bottom' : '-100px'}, 400); 
            }
        }
    });
    //スクロールしてトップ
    topBtn.click(function () {
        $('body,html').animate({
            scrollTop: 0
        }, 500);
        return false;
    });
});

