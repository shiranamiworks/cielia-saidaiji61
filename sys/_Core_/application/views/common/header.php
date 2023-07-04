<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html>
<head>
    <title><?=$page_setting["title"]?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
    <!-- bootstrap -->
    <link href="<?= base_url() ?>css/bootstrap/bootstrap.css" rel="stylesheet" />
    <link href="<?= base_url() ?>css/bootstrap/bootstrap-overrides.css" type="text/css" rel="stylesheet" />


    <!-- global styles -->
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>css/compiled/layout.css" />
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>css/compiled/gnavi.css" />
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>css/compiled/elements.css" />
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>css/compiled/icons.css" />

    <!-- libraries -->
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>css/lib/jquery-ui-1.10.2.custom.css" />
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>css/lib/font-awesome.css" />
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>js/notifIt/css/notifIt.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>js/jquery-toast-plugin/src/jquery.toast.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>css/compiled/web-app-icons.css" media="screen" />
    <!-- open sans font -->
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800' rel='stylesheet' type='text/css' />

    <!-- lato font -->
    <link href='https://fonts.googleapis.com/css?family=Lato:300,400,700,900,300italic,400italic,700italic,900italic' rel='stylesheet' type='text/css' />


  <?php
  if(isset($page_setting["css"]) && is_array($page_setting["css"])){
    foreach($page_setting["css"] as $value){
      if($value){
        echo "<link rel=\"stylesheet\" href=\"".$value."\" type=\"text/css\" />";
      }
    }
  }
  ?>
<?php
//add js include
if(!empty($add_css)) {
$this->load->custom_view($dirname , $add_css);
}
?>

  <?php
  if(!empty($hidden_navigation)) {
  ?>
  <style>
  header.navbar {
    display:none;
  }
  #sidebar-nav {
    display: none;
  }
  .content {
    margin-top:0;
    margin-left:0;
    margin-bottom:0;
  }
  </style>
  <?php
  }
  ?>

  <!-- scripts -->
  <script src="<?= base_url() ?>js/jquery-1.10.2.min.js"></script>
  <script src="<?= base_url() ?>js/bootstrap.min.js"></script>
  <!-- knob -->
  <script src="<?= base_url() ?>js/jquery.knob.js"></script>
  <!-- flot charts -->
  <script src="<?= base_url() ?>js/jquery.flot.js"></script>
  <script src="<?= base_url() ?>js/jquery.flot.stack.js"></script>
  <script src="<?= base_url() ?>js/jquery.flot.resize.js"></script>
  <script src="<?= base_url() ?>js/notifIt/js/notifIt.js"></script>
  <script src="<?= base_url() ?>js/jquery-toast-plugin/dist/jquery.toast.min.js"></script>
  <script src="<?= base_url() ?>js/jquery.matchHeight.js"></script>
  <script src="<?= base_url() ?>js/theme.js"></script>
  <script src="<?= base_url() ?>js/lib.js"></script>

  <script>
  var BASE_URL = '<?= base_url() ?>';
  $(function(){
    navAnimation();
  });
  </script>
  
</head>
<?php
$page_id = '';
if(!empty($page_setting['page_id'])) {
    $page_id = ' id="'.$page_setting['page_id'].'"';
}
?>
<body<?= $page_id ?>>
    
    <div id="container">

    <!-- navbar -->
    <header class="navbar navbar-inverse" role="banner">
        <div class="navbar-header">
            <button class="navbar-toggle" type="button" data-toggle="collapse" id="menu-toggler">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <span class="navbar-brand">
                <img src="<?= base_url() ?>img/logo102_main.png" alt="" width="180" />
            </span>
        </div>
        <ul class="nav navbar-nav pull-right hidden-xs">

            <?php if(!empty($header_info['account_name'])) : ?>
            <li>
                <a href="javascript:void(0);" class="hidden-xs hidden-sm">
                    <?= $header_info['account_name'] ?>
                </a>
            </li>
            <?php endif; ?>
            <li class="settings hidden-xs hidden-sm">
                <a href="<?=site_url('logout')?>" role="button">
                    <i class="icon-share-alt" style="font-size:92%"></i> ログアウト
                </a>
            </li>
        </ul>
        <?php /* if(!empty($header_info['site_url'])) : ?>
        <div class="pull-right hidden-xs sitewatch-btn">
        <a href="<?= $header_info['site_url'] ?>" target="_blank" class="btn btn-info"><i class="icon-search" style="font-size:15px"></i> サイトを確認する</a>
        </div>
        <?php endif; */ ?>
    </header>
    <!-- end navbar -->

    <!-- sidebar -->
    <div id="sidebar-nav">

        <nav id="gNavi">

          <?php
          if(!empty($admin_menu_list)) :
              foreach($admin_menu_list as $id => $menu_data) :
          ?>

          <dl id="<?= $menu_data["menu_id"]?>">
          <?php
                if(empty($menu_data["terms"]) || (!empty($menu_data["terms"]) && $this->auth->IsSuccess(false , $menu_data["terms"]))) :
                    if(empty($menu_data["submenu"])) :
                    //------------------------------------------------
          ?>
                <dt><a href="<?= site_url($menu_data["link"]) ?>"><span><?= $menu_data["label"] ?></span></a></dt>
                
          <?php
                    //------------------------------------------------
                    else:
                    //------------------------------------------------
          ?>
                <dt class="menu_title"><a href="#"><span><?= $menu_data["label"] ?></span></a></dt>
                <dd class="submenu"><div class="submenu_items" style="top: -43px;">
                    <?php
                    //------------------------------------------------------
                    //サブメニュー表示
                    //------------------------------------------------------
                    foreach($menu_data["submenu"] as $submenu_data) :
                      if(empty($submenu_data["terms"]) || (!empty($submenu_data["terms"]) && $this->auth->IsSuccess(false , $submenu_data["terms"]))) :
                    ?>
                    <p class="submenu_item_name"><a class="<?= $submenu_data["class"] ?>" href="<?= site_url($submenu_data["link"]) ?>"><span><?= $submenu_data["label"] ?></span></a></p>
                    <?php
                      endif;
                    endforeach;
                    ?>
                </div></dd>
          <?php
                  //------------------------------------------------
                    endif;

                  endif;
                  //------------------------------------------------
          ?>
          </dl>
          <?php
            endforeach;
          endif;
          ?>
          
        </nav>

    </div>
    <!-- end sidebar -->

