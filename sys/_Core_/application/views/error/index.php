<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$data["page_setting"] = array(
  "title" => "エラー",
  "css"   => array(),
  "menu_active" => array()
);

$this->load->view("common/header.php",$data);
?>



<div class="container app-main-content">

  <div class="row">
  <div class="col-md-10">

  <div class="error-container">
  <h3 style="margin:50px 0"><strong>エラーが発生しました</strong></h3>
  <p class="error-text">
  <?php if(!empty($error_message)) { ?>
  <?= $error_message ?>
  <?php } ?>
  </p>


  </div>

  </div>
  </div>

</div>


<?php
// footer include
$this->load->view("common/footer1.php");
?>


<?php
// footer include
$this->load->view("common/footer2.php");
?>
