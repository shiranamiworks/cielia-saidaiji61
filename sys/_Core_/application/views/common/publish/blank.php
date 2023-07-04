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