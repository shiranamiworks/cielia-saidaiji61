<!DOCTYPE html>
<html class="login-bg">
<head>
<title><?= ADMIN_TITLE ?></title>
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

<!-- this page specific styles -->
<link rel="stylesheet" href="<?= base_url() ?>css/compiled/signin.css" type="text/css" media="screen" />

<!-- open sans font -->
<link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,700italic,800italic,400,300,600,700,800' rel='stylesheet' type='text/css' />

<!--[if lt IE 9]>
  <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
</head>
<body>


    <?php echo form_open('login/auth');?>
    <div class="login-wrapper">
        <a href="javascript:void(0);">
            <img class="logo" src="<?= base_url() ?>img/logo102_main.png" alt="" width="180" />
        </a>

        <div class="box">
            
            <div class="content-wrap">
                <h6>ログイン</h6>
                <input class="form-control" name="loginID" type="text" placeholder="ユーザーID（半角英数字）" value="<?= set_value('loginID') ?>">
                <input class="form-control" name="password" type="password" placeholder="パスワード" value="<?= set_value('password') ?>">
                <div style="margin-top:15px">
                <input type="submit" class="btn-glow primary login" value="ログイン">
                </div>
            </div>
        </div>

    </div>
    <?php echo form_close(); ?>




<!-- scripts -->
<script src="<?= base_url() ?>js/jquery-1.10.2.min.js"></script>
<script src="<?= base_url() ?>js/bootstrap.min.js"></script>
<script src="<?= base_url() ?>js/theme.js"></script>

<!-- pre load bg imgs -->
<script type="text/javascript">
$(function () {
<?php
//エラーメッセージ
if(!empty($error['message'])) {
?>
var error='ログインエラー：<?= $error['message'] ?>';
alert(error);
<?php
}
?>
});
</script>
</body>
</html>