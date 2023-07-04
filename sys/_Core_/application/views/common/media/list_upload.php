<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$data["page_setting"] = array(
  "title" => 'ファイルアップロード | '.ADMINPAGE_NAME,
  "css"   => array(
    base_url().'css/compiled/media.css',
    base_url().'js/Remodal/jquery.remodal.css'
  ),
  "page_id" => (!empty($dirname)?$dirname.'_':'') . "media_list_upload",
  "menu_active" => array()
);
$data["hidden_navigation"] = true;

$this->load->view("common/header.php",$data);
?>

<!-- main container -->
<div class="content media-list-upload">

        <div id="pad-wrapper" class="gallery">
            <ul class="nav nav-tabs" style="margin-bottom:20px">
              <li><a href="<?=$listpage_url?>">ファイル一覧</a></li>
              <li class="active"><a href="<?=$listpage_upload_url?>">アップロード</a></li>
            </ul>
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-explain alert-fileup">
                    <span class="info-icon"><i class="icon-exclamation-sign"></i></span>
                    <span class="info-text">アップロードできるファイルは<strong> jpg , gif , png , pdf </strong>で、一度に<strong><?= $upload_maxsize ?>KB</strong>までになります。<br>
                    <strong>（ファイル名は半角英数字とハイフン、アンダーバーのみ利用可）</strong></span>
                    </div>
                </div>
            </div>

            <?php 
            if (!empty($success)) :
            ?>
            <div class="row info-alert">
                <div class="col-md-12">
                <div class="alert alert-success">
                    <i class="icon-ok-sign"></i> <?php echo count($success); ?>件のファイルをアップロードしました。
                    <span><a href="<?= $listpage_url ?>">ファイル一覧へ</a></span>
                </div>
                </div>
            </div>
            <?php
            endif;
            ?>
            <?php 
            if(!empty($errors)) :
            ?>
            <div class="row info-alert">
                <div class="col-md-12">
                <div class="alert alert-danger">
                    <i class="icon-remove-sign" style="margin-bottom:0"></i>
                    <?php
                    $cnt = 0;
                    foreach($errors as $key => $err) :
                        $cnt++;
                        if($cnt > 1) {
                            echo "<br>";
                            echo '<i class="icon-blank" style="margin-bottom:0"></i>';
                        }
                        //echo "【".$key."番目のファイル】 ".$err;
                        echo $err;
                    endforeach;
                    ?>
                </div>
                </div>
            </div>
            <?php
            endif;
            ?>


            <!-- gallery wrapper -->
            <?php echo form_open_multipart($listpage_upload_url , array('class'=>"form-horizontal" , 'role'=>"form" , 'id' => "uploadForm"));?>
            <div class="gallery-wrapper body-area">

                <?php for($i=0;$i<1;$i++) : ?>

                    <div class="fileup-items">
                    <div class="form-group">
                        <label for="input1" class="col-sm-2 control-label">ファイル選択:</label>
                        <div class="col-sm-4">
                            <input type="file" name="upfile[<?= $i ?>]" id="input<?= $i ?>" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="input1" class="col-sm-2 control-label">説明・タグ :</label>
                        <div class="col-sm-7">
                          <input type="text" name="upfile_desc[<?= $i ?>]" class="form-control" placeholder="説明・タグ" id="inputTag<?= $i ?>">
                        </div>
                    </div>
                    <div class="preview" id="preview<?= $i ?>" style="display:none"><img src="<?= base_url() ?>/img/lens.png"></div>
                    </div>

                <?php endfor; ?>
                <input type="hidden" name="upload_flg" value="1">

                <div class="modal-footer">
                    <div class="text-center">
                      <button type="button" class="btn btn-default btn-win-close">ウインドウを閉じる</button>
                      <button type="submit" id="btnUpload" class="btn btn-primary">アップロード</button>
                    </div>
                </div>

            </div>
            <?php echo form_close(); ?>
            <!-- end gallery wrapper -->

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
});
</script>
<?php
// js include
$this->load->custom_view($dirname , "media/js/list");
?>
<?php
// footer include
$this->load->view("common/footer2.php");
?>