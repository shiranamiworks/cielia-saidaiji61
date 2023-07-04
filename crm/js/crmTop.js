/* ============================================================ 
ver.2020.04.01

//CRM領域

=============================================================== */



/*--------------------------------------

body
crm--blockBrand

// 初回セッション時のみ全画面ビジュアル表示
// 他のページから遷移時は非表示
// ブラウザクローズでリセット

--------------------------------------*/
var webStorage = function(){
  var $brand = $('body');
  
  if(sessionStorage.getItem('access')){
    $brand.addClass('is-active');
  } else {
    $brand.removeClass('is-active');
    sessionStorage.setItem('access', 0);
  }
}

webStorage();



/*--------------------------------------

crm--InfoArea
// 複数登録時に高さを自動取得

--------------------------------------*/
(function(){
	window.addEventListener('message', function(e){
		var changeHeight = e.data + 'px';
		$('#crmInfo').animate({height: changeHeight}, 300);
	},false);

})();



/*--------------------------------------

crm--blockBrand
// 全画面ビジュアル

--------------------------------------*/

// 時間制御 //
$(window).on('load', function() {
    setTimeout(function(){
        $('body').addClass('switch');
    },10000);
});

// クリックアクション //
$(function(){
    $('.crm--blockBrand .btn').click(function () {
        $('body').addClass('switch');
    });
});

// ビジュアル高さ自動取得 //
$(window).on('resize',function(){
    winH = $(window).height();
    $('.crm--blockBrand').outerHeight(winH);
});
