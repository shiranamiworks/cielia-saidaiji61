<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$data["page_setting"] = array(
  "title" => ADMINPAGE_NAME,
  "css"   => array(
    base_url().'css/compiled/list.css',
    base_url().'css/compiled/tables.css',
    base_url().'js/Remodal/jquery.remodal.css',
    base_url().'css/torikago.php'
  ),
  "page_id" => "rooms_list",
  "menu_active" => array()
);

$this->load->view("common/header",$data);
?>


<!-- main container -->
<div class="content">
    
    
        <div id="pad-wrapper">

            <div class="row">
                <div class="col-md-12">
                    <h3 class="page-ttl icon_<?=$dirname?>">鳥かご管理 / 物件一覧
                    </h3>
                    <div class="alert alert-explain">
                    <span class="info-icon"><i class="icon-exclamation-sign"></i></span>
                    <span class="info-text">各お部屋をクリックすると、そのお部屋のデータ編集を行えます。</span>
                    </div>
                </div>
            </div>

            <!-- account data table-->
            <div class="row body-area">

                <div class="table-wrapper">
                <div class="col-md-12">
                
                <?php
                if(!empty($torikago_html['error'])) :
                ?>
                
                <h4>テンプレートエラー</h4>
                <p><?= $torikago_html['error'] ?></p>

                <?php else: ?>

                <?= $_no_escape['torikago_html'] ?>

                <?php endif; ?>

                </div>
                </div>


            </div>
            <!-- end account data table -->

        </div>


    </div>


</div>
<!-- end main container -->


<?php
// footer include
$this->load->view("common/footer1");
?>
<script src="<?= base_url() ?>js/Remodal/jquery.remodal.js"></script>
<script>
$(function(){
    gNaviActive('Rooms');
    subNaviActive('Rooms' , "list");
});
</script>
<?php
// js include
$this->load->view("rooms/js/list");
?>
<?php
// footer include
$this->load->view("common/footer2");
?>