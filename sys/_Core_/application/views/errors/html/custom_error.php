<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>404 Page Not Found</title>
<link href="<?= config_item('base_url') ?>css/bootstrap/bootstrap.min.css" rel="stylesheet">
<link href="<?= config_item('base_url') ?>css/compiled/error.css" rel="stylesheet">
<link href='http://fonts.googleapis.com/css?family=Allerta' rel='stylesheet' type='text/css'>
</head>
<body>
<div class="container">
  <div class="row">
  <div class="col-md-8 col-md-offset-2">
    <div class="text-center">
    </div>

    <div class="error-container">
    <h3><?php echo $heading; ?></h3>
    <div class="error-text"><?php echo $message; ?></div>
    </div>
  </div>
  </div>
</div>


</body>
</html>