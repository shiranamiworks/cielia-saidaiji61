<!DOCTYPE html>
<html class="login-bg">
<head>
<title>プレビュー</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- bootstrap -->
<link href="<?= base_url() ?>css/bootstrap/bootstrap.css" rel="stylesheet" />
<link href="<?= base_url() ?>css/bootstrap/bootstrap-overrides.css" type="text/css" rel="stylesheet" />

<!-- global styles -->
<link rel="stylesheet" type="text/css" href="<?= base_url() ?>css/compiled/layout.css" />
<link rel="stylesheet" type="text/css" href="<?= base_url() ?>css/compiled/elements.css" />
<link rel="stylesheet" type="text/css" href="<?= base_url() ?>css/compiled/icons.css" />

<!-- libraries -->
<link rel="stylesheet" type="text/css" href="<?= base_url() ?>css/lib/font-awesome.css" />

<link rel="stylesheet" type="text/css" href="<?= base_url() ?>css/compiled/preview.css" />

<!-- open sans font -->
<link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,700italic,800italic,400,300,600,700,800' rel='stylesheet' type='text/css' />

<!--[if lt IE 9]>
  <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
</head>
<body>

<div class="preview-menu navbar navbar-inverse navbar-fixed-top" role="navigation">
<div class="text-center" style="padding:10px 0;color:#FFF">プレビュー表示中です　<span><button class="btn btn-default" onclick="window.close();">閉じる</button></span></div>
</div>

<div class="preview-body">
<div class="iframe-container">
<iframe src="<?=$iframe_url?>" id="previewframe" onLoad="adjust_frame_css(this.id)" scrolling="no" frameborder="no"></iframe>
</div>
</div>



<!-- scripts -->
<script src="<?= base_url() ?>js/jquery-1.10.2.min.js"></script>
<script src="<?= base_url() ?>js/bootstrap.min.js"></script>
<script src="<?= base_url() ?>js/theme.js"></script>

<!-- pre load bg imgs -->
<script type="text/javascript">
$(function () {

  //プレビューするiframe内のサイト内のリンクタグを書き換える処理
  var this_url = window.location.href;
  var parser = $('<a>', { href:this_url } )[0];
  var preview_base_url = parser.protocol + '//' + parser.hostname + parser.pathname;
  var hostname = window.location.hostname;
  var client_site_url = '<?= $client_site_url ?>';
  $('iframe').on('load' , function(e) {
    $('a', this.contentWindow.document).each(function() {
      var href = $(this).attr('href');
      if(href && href.match(hostname)) {

        if(typeof hostname !== undefined) {
          href = href.replace('http://'+hostname+'/admin/' , client_site_url);
        }
        $(this).attr({'href' : preview_base_url + '?url=' + href , 'target' : '_parent'});
      }
    });
  });
});


$(window).resize(function() {
  adjust_frame_css('previewframe');
});
function adjust_frame_css(F){
  if(document.getElementById(F)) {
  var myF = document.getElementById(F);
  var myC = myF.contentWindow.document.documentElement;
  var myH = 100;
    if(document.all) {
      myH  = myC.scrollHeight;
    } else {
      myH = myC.offsetHeight;
    }
    myF.style.height = myH+"px";
  }
}
</script>
</body>
</html>