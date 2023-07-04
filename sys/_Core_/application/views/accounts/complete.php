<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$data["page_setting"] = array(
  "title" => ADMINPAGE_NAME,
  "css"   => array(
    base_url().'css/compiled/form-showcase.css',
    base_url().'js/Remodal/jquery.remodal.css',
  ),
  "page_id" => "account_edit",
  "menu_active" => array()
);

$this->load->view("common/header.php",$data);
?>


<!-- main container -->
<div class="content edit-complete">
    
    
        <div id="pad-wrapper">

            <div class="row">
                <div class="col-md-12">
                    <h3 class="page-ttl icon_account">アカウント登録
                    </h3>
                    <div class="alert alert-complete">
                    <span class="info-icon"><i class="icon-exclamation-sign"></i></span>
                    <span class="info-text">登録が完了しました。
                    </span>
                    </div>
                </div>
            </div>

            <div style="margin-top:50px">
                <p class="text-center">
                <a href="<?= $list_page_url ?>" class="btn btn-lg btn-default">アカウント一覧へ</a>
                </p>
            </div>
        </div>
    </div>


</div>
<!-- end main container -->


<?php
// footer include
$this->load->view("common/footer1.php");
?>
<script src="<?= base_url() ?>js/Remodal/jquery.remodal.js"></script>
<script>
$(function(){
    gNaviActive('Account');
    subNaviActive('Account' , "edit");
});

</script>
<?php
// js include
$this->load->view("accounts/js/edit");
?>
<?php
// footer include
$this->load->view("common/footer2.php");
?>