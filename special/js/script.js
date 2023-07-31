$(function(){
	var video = document.getElementById("video");
	var video_btn = document.getElementById("video-btn");
	var btn_status = 0;
	video_btn.addEventListener("click", function () {
		video.autoplay = true;
		video.loop = true;
		video.muted = false;
		video.currentTime = 0;
	});
  $("#video-btn").on("click",function(){
      video.play();
  });
  $(".modal-close").on("click",function(){
      video.pause ();
  });
});


