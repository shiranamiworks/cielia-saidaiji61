$(function() {
	var url_param = $("#link").attr("href");
	url_param = url_param.replace(/～/g, "");
	url_param = url_param.replace(/万円/g, "");
	$("#link").attr("href", url_param);
});