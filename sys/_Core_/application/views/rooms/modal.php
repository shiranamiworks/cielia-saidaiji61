<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>
<head>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-WMCBSV');</script>
<!-- End Google Tag Manager -->
  
  <title>【公式】シエリア西大寺（<?php echo $data['title'] ?>号室）物件詳細ページ</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" type="text/css" href="../../../../attendance/plan/css/modal-3.css" />
  <link rel="stylesheet" type="text/css" href="../../../../attendance/plan/css/option.css" />
  <link rel="stylesheet" type="text/css" href="../../../../attendance/plan/css/torikago.css" />
  <script type="text/javascript" src="//code.jquery.com/jquery-1.11.0.min.js"></script>
  <script type="text/javascript" src="../../../../attendance/js/modal-3.js"></script>
  <script type="text/javascript" src="../../../../attendance/js/index.js"></script>
</head>
<body>
<!-- Google Tag Manager (noscript) -->
<noscript>
<iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WMCBSV" height="0" width="0" style="display:none;visibility:hidden"></iframe>
</noscript>
<!-- End Google Tag Manager (noscript) -->

  <ul class="tab-list">
     <?php if (isset($data['base_image']) && $data['base_image'] != '') { ?><li class="active" data-no="0"><a href="#base">基本</a></li><?php } ?>
    	<?php $cnt = 0; for ($i = 1; $i <= 4; $i++) { if (isset($data['menu' . $i . '_image']) && $data['menu' . $i . '_image'] != '') { ?>
        <li data-no="<?php echo $i; ?>"<?php if ((!isset($data['base_image']) || (isset($data['base_image']) && empty($data['base_image']))) && $cnt == 0) { ?> class="active"<?php } ?>><a href="#menu<?php echo $i; ?>">メニュー<?php echo $i; ?></a></li>
    <?php $cnt++; } } ?>
</ul>
<div class="image-container">
    <?php if (isset($data['base_image']) && !empty($data['base_image'])) { ?><p class="opened"><img src="<?php echo $data['base_image']; ?>" alt=""></p><?php } ?>
    <?php $cnt = 0; for ($i = 1; $i <= 4; $i++) { if (isset($data['menu' . $i . '_image']) && $data['menu' . $i . '_image'] != '') { ?>
    <p<?php if ((!isset($data['base_image']) || (isset($data['base_image']) && empty($data['base_image']))) && $cnt == 0) { ?> class="opened"<?php } ?>><img src="<?php echo $data['menu' . $i . '_image']; ?>" alt=""></p>
    <?php $cnt++; } } ?>
</div>
<script>
    (function () {
        $('.tab-list li').on('click', function () {
            $('.tab-list li').removeClass('active');
            $('.image-container p').removeClass('opened');

            $(this).addClass('active');
            var index = $('.tab-list li').index(this);
            $('.image-container p:eq(' + index + ')').addClass('opened');
        });
    }());
</script>

<!-- Yahoo Tag Manager -->
<script type="text/javascript">
  (function () {
    var tagjs = document.createElement("script");
    var s = document.getElementsByTagName("script")[0];
    tagjs.async = true;
    tagjs.src = "//s.yjtag.jp/tag.js#site=h8ocGPj";
    s.parentNode.insertBefore(tagjs, s);
  }());
</script>
<noscript>
  <iframe src="//b.yjtag.jp/iframe?c=h8ocGPj" width="1" height="1" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>
</noscript>
<!-- Yahoo Tag Manager -->

</body>
</html>