<!DOCTYPE html>
<html class="login-bg">
<head>
<title>鳥かごCMSセットアップ画面</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- bootstrap -->
<link href="<?= base_url() ?>css/bootstrap/bootstrap.css" rel="stylesheet" />

<!--[if lt IE 9]>
  <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
</head>
<body>

<div class="container">

<h3 style="margin-bottom:30px">鳥かごCMSセットアップ画面</h3>

<?php if($complete == true) : ?>

<p>セットアップが完了しました。管理画面へログインして下さい。</p>
<p><a href="<?php echo site_url('login') ?>">管理画面ログイン</a></p>

<?php else: ?>

    <?php if(!empty($error)) : ?>

        <h4>エラーが発生しました</h4>
        <p><?= $error ?></p>

    <?php endif; ?>

<p><a href="<?php echo site_url('setup') ?>">もどる</a></p>

<?php endif; ?>

</div>

<!-- scripts -->
<script src="<?= base_url() ?>js/jquery-1.10.2.min.js"></script>
<script src="<?= base_url() ?>js/bootstrap.min.js"></script>

<!-- pre load bg imgs -->
<script type="text/javascript">
$(function () {
});
</script>
</body>
</html>