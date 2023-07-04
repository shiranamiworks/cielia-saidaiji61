<?php
$dir = dirname($_SERVER["SCRIPT_NAME"]);
?>

$(function(){

  var $container = $('#torikagoSp');

  //loading
  $container.html('<div style="text-align:center"><img src="<?= $dir ?>/loading.gif" width="30"></div>');

  $.ajax({
    url : '<?= $dir ?>/rooms/lists' + location.search,
    dataType : 'html',
    cache : false
  }).done(function(data) {
    $container.empty();
    $container.html(data);
  }).fail(function(data) {
    $container.empty();
    $container.html(data);
  });

  $(document).on("click", ".btn-simulation button", function () {
    var param = $(this).attr('data-param');
    var openWinUrl = TorikagoSimulatorLinkUrl;
    if(param){
      openWinUrl +='?'+param;
    }
    if(TorikagoSimulatorLinkWindow == 'self') {
      window.location.href = openWinUrl;
    }else{
      window.open(openWinUrl , 'win1' , 'width='+TorikagoSimulatorWinSizeW+',height='+TorikagoSimulatorWinSizeH+',toolbar=no,location=no,menubar=no,scrollbars=yes');
    }
  });
});
