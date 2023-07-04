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

<!-- main container -->
<div class="content">

        <div id="pad-wrapper">

            <!-- categories table-->
            <div class="row">
            <?php echo form_open($publish_link , array('class'=>"form-horizontal" , 'role'=>"form" , 'id' => "publishForm"));?>
                <div class="alert alert-success">
                    <i class="icon-ok-sign"></i> 「<?= $data["title"] ?>」を<strong>承認・公開</strong>、または、<strong>差戻し</strong>を行うことができます。<br />差戻しの場合、差戻し理由を記載して通知することができます。
                </div>
                <div class="text-center">
<p><label>メッセージ</label><textarea name="message" id="app_message" style="width:90%" rows="5"><?= set_value('message') ?></textarea></p>

                </div>
                <div class="text-center">
                    <button type="submit" name="app" value="1" id="btnApp" class="btn btn-primary">承認・公開</button>
                    <button type="submut" name="back" value="1" id="btnBack" class="btn btn-default">差戻し</button>
                </div>
            <?php echo form_close(); ?>
            </div>


        </div>

</div>
<!-- end main container -->


<?php
// footer include
$this->load->view("common/footer1.php");
?>

<script>
$(function(){
});

</script>

<?php
// footer include
$this->load->view("common/footer2.php");
?>