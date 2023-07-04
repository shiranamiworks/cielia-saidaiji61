jQuery(window).load(function(){
  try {
		
	} catch(e) {
		if (e.code == 22) {
			alert('Safariブラウザのプライベートモードでご覧の方は通常モードでの閲覧をお願い致します。');
					location.href = "member/login.html";
		}
	}
	$(".formMember").submit(function(event) {
		var pw = $("#password").val();
		var url = "";
		switch (pw) {
			case "cksa62":
				url = "member/index.html";
				break;

			default:
				alert("申し訳ございません。\nご入力されたパスワードが正しくありません。");
				break;
		}

	try {
		if (url != ""){
			window.sessionStorage.setItem(['login'],['member']);

			// ログインページ前にいたurlセッションを取得
			var urlSession = window.sessionStorage.getItem(['url']);

			if ( urlSession ) {
				location.href = urlSession;
			} else {
				location.href = url;
			}
		}
	} catch(e) {
		if (e.code == 22) {
			alert('Safariブラウザのプライベートモードでご覧の方は通常モードでの閲覧をお願い致します。');
					location.href = "member/login.html";
		}
	}
		return false;
	});
});

jQuery(window).load(function(){
  try {
		
	} catch(e) {
		if (e.code == 22) {
			alert('Safariブラウザのプライベートモードでご覧の方は通常モードでの閲覧をお願い致します。');
					location.href = "attendance/login.html";
		}
	}
	$(".formAttendance").submit(function(event) {
		var pw = $("#password02").val();
		var url = "";
		switch (pw) {
			case "cksa62rj":
				url = "attendance/index.html";
				break;

			default:
				alert("申し訳ございません。\nご入力されたパスワードが正しくありません。");
				break;
		}

	try {
		if (url != ""){
			window.sessionStorage.setItem(['login'],['attendance']);

			// ログインページ前にいたurlセッションを取得
			var urlSession = window.sessionStorage.getItem(['url']);

			if ( urlSession ) {
				location.href = urlSession;
			} else {
				location.href = url;
			}
		}
	} catch(e) {
		if (e.code == 22) {
			alert('Safariブラウザのプライベートモードでご覧の方は通常モードでの閲覧をお願い致します。');
					location.href = "attendance/login.html";
		}
	}
		return false;
	});
});
