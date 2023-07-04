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
$publish_str = PUBLISH_STR;//"公開"
if($data["output_flag"] == 1)
{
    $publish_str = "非".PUBLISH_STR/*"公開"*/."に";
}
?>
<!-- main container -->
<div class="content">

            <?php echo form_open($publish_link , array('class'=>"form-horizontal" , 'role'=>"form" , 'id' => "publishForm"));?>
                「<?= $data["title"] ?>」を<?= $publish_str ?>しますか？

                <div class="text-center" style="margin-top:50px">
                    <button type="button" id="btnPublishCancel" class="btn btn-default" data-dismiss="modal">キャンセル</button>
                    <button type="submit" id="btnPublish" class="btn btn-primary"><?php echo $publish_str; ?>する</button>
                </div>
            <?php echo form_close(); ?>
            

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