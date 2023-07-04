<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$data["page_setting"] = array(
  "title" => ADMINPAGE_NAME,
  "css"   => array(
    base_url().'css/compiled/form-showcase.css',
    base_url().'js/Remodal/jquery.remodal.css',
    base_url().'js/datetimepicker/jquery.datetimepicker.css',
  ),
  "page_id" => (!empty($dirname)?$dirname.'_':'') . "edit",
  "menu_active" => array()
);

$this->load->view("common/header.php",$data);
?>


<!-- main container -->
<div class="content edit-complete">
    
    
        <div id="pad-wrapper">

            <div class="row">
                <div class="col-md-12">
                    <h3 class="page-ttl icon_<?=$dirname?>"><?=DATA_NAME?>登録
                    </h3>
                    <div class="alert alert-complete">
                    <span class="info-icon"><i class="icon-exclamation-sign"></i></span>
                    <span class="info-text">登録が完了しました。</span>
                    <span class="info-text" id="infoPublishWay">
                    <?php if (!empty($this->session->flashdata('save_status')) && $this->session->flashdata('save_status') == 'wait') : ?>
                     承認待ちの状態として登録されています。管理者による承認操作が行われるまで公開されません。
                    <?php else : ?>
                     新規登録の場合、<?=DATA_NAME?>一覧画面において「公開切り替え」の操作を行われるまでは実際のサイト上には公開されません。
                    <?php endif; ?>
                    </span>
                    </div>
                </div>
            </div>

            <div style="margin-top:50px">
                <p class="text-center">
                <a href="<?= $list_page_url ?>" class="btn btn-lg btn-default"><?=DATA_NAME?>一覧へ</a>
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
    gNaviActive('<?=ucwords($dirname)?>');
    subNaviActive('<?=ucwords($dirname)?>' , "edit");
});

</script>

<?php
//add js include
if(!empty($add_js)) {
$this->load->custom_view($dirname , $add_js);
}
?>

<?php
// footer include
$this->load->view("common/footer2.php");
?>