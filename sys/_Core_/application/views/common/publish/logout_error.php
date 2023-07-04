<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$data["page_setting"] = array(
  "title" => "エラー",
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
Authorization error
</div>
<!-- end main container -->


<?php
// footer include
$this->load->view("common/footer1.php");
?>

<script>
//iframe内から親ウインドウをログイン画面へリダイレクトさせる
window.parent.location.href = '<?php echo site_url("login"); ?>';

</script>

<?php
// footer include
$this->load->view("common/footer2.php");
?>