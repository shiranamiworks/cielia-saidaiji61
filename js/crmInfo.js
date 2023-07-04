
/*--------------------------------------

crm--InfoArea
// 高さ自動取得 //

--------------------------------------*/
(function(){
	window.addEventListener('message', function(e){
		var changeHeight = e.data + 'px';
		$('#crmInfo').animate({height: changeHeight}, 300);
	},false);

})();
