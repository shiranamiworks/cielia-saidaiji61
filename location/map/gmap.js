

var infoWnd, gmap;

function initialize() {

	//地図を表示

	var gmap = new google.maps.Map(document.getElementById('gmap'), {});

	gmap.setMapTypeId(google.maps.MapTypeId.ROADMAP);

	//情報ウィンドウの作成
	infoWnd = new google.maps.InfoWindow();

	//地図上にマーカーを配置していく
	var bounds = new google.maps.LatLngBounds();
	var point, i, latlng;
	for (i in pointList) {

		//マーカーを作成
		point = pointList[i];
		latlng = new google.maps.LatLng(point.latlng[0], point.latlng[1]);
		ico = point.ico;
		bounds.extend(latlng);
		var marker = createMarker(
			gmap, latlng, point.name, ico
		);

		//サイドバーのボタンを作成
		createMarkerButton(marker);
	}

	//マーカーが全て収まるように地図の中心とズームを調整して表示
	gmap.fitBounds(bounds);
	
}

function createMarker(map, latlng, title, icon) {

	//マーカーを作成
	var marker = new google.maps.Marker({
		position: latlng,
		map: map,
		title: title,
		icon: ico,
		animation: google.maps.Animation.DROP
	});

	//マーカーがクリックされたら、情報ウィンドウを表示
	google.maps.event.addListener(marker, "click", function() {
		infoWnd.setContent("<div>" + title + "</div>");
		infoWnd.open(map, marker);
		map.panTo(latlng);
	});

	return marker;
}

function createMarkerButton(marker) {
	//サイドバーにマーカ一覧を作る
	var ul = document.getElementById("marker_list");
	var li = document.createElement("li");
	var title = marker.getTitle();
	var irans = marker.title.split('<div>')[0].split('</span>')[1];
	marker.title = irans;
	li.innerHTML = title;
	ul.appendChild(li);

	//サイドバーがクリックされたら、マーカーを擬似クリック
	google.maps.event.addDomListener(li, "click", function() {
		google.maps.event.trigger(marker, "click");
	});
}
google.maps.event.addDomListener(window, "load", initialize);