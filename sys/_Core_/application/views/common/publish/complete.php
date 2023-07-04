<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$data["page_setting"] = array(
  "title" => ADMINPAGE_NAME,
  "css"   => array(
    base_url().'css/compiled/publish.css',
  ),
  "page_id" => (!empty($dirname)?$dirname.'_':'') . "publish",
  "menu_active" => array()
);

$this->load->view("common/header.php",$data);
?>

<?php
$status_str = PUBLISH_STR;/*"公開"*/
if($data["status"] == "back") {
    $status_str = "差戻し";
} else if($data["output_flag"] != 1) {
    $status_str = "非".PUBLISH_STR/*公開*/."に";
}
?>
<!-- main container -->
<div class="content">
<?=DATA_NAME?>が<?=$status_str?>されました

<div class="text-center" style="margin-top:50px">
<button type="button" id="btnPublishCancelAndReload" class="btn btn-default" data-dismiss="modal">閉じる</button>
</div>

</div>
<!-- end main container -->


<?php
// footer include
$this->load->view("common/footer1.php");
?>

<?php
// js include
$this->load->custom_view($dirname , "publish/js/common");
?>
<script>
$(function(){
});
</script>

<?php
// footer include
$this->load->view("common/footer2.php");
?>