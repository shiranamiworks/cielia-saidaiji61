<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$data["page_setting"] = array(
  "title" => ADMINPAGE_NAME,
  "css"   => array(
    base_url().'css/compiled/form-showcase.css',
    base_url().'js/Remodal/jquery.remodal.css',
    base_url().'js/datetimepicker/jquery.datetimepicker.css'
  ),
  "page_id" => (!empty($dirname)?$dirname.'_':'') . "edit",
  "menu_active" => array()
);

$this->load->view("common/header.php",$data);
?>


<!-- main container -->
<div class="content edit-page">
    
    
        <div id="pad-wrapper">

            <div class="row">
                <div class="col-md-12">
                    <h3 class="page-ttl icon_<?=$dirname?>">
                    <?php 
                    if(!empty($adminpage_title)) :
                        echo $adminpage_title;
                    else :
                    ?>
                    <?=DATA_NAME?>登録
                    <?php
                    endif;
                    ?>
                    </h3>
                    <div class="alert alert-explain">
                    <span class="info-icon"><i class="icon-exclamation-sign"></i></span>
                    <span class="info-text">
                    <?php if(!empty($adminpage_infotext)) :
                    echo $_adminpage_infotext;
                    else : 
                    ?>
                    ここでは、<?=DATA_NAME?>の登録・編集を行うことができます。<br>画面が複数のタブに分かれている場合は、タブをクリックして画面の切り替えを行っていただき、各項目の入力・編集を行ってください。<br>
                    画面下部にある「プレビュー」ボタンをクリックすることで、登録を行う前でも実際の画面表示を確認することができます。
                    <?php endif; ?>
                    </span>
                    </div>
                </div>
            </div>

            <?php 
            if(!empty(validation_errors()))  :
            ?>
            <div class="row info-alert">
                <div class="col-md-12">
                <div class="alert alert-error">
                    <span class="info-icon"><i class="icon-remove-sign"></i></span>
                    <span class="info-text">
                    入力エラーがあります<br>
                    <span style="font-size:92%">エラーのある項目にエラーメッセージが表示されていますのでご確認下さい
                    </span>
                    </span>
                </div>
                </div>
            </div>
            <?php
            endif;
            ?>

            <div class="form-page">
            <div class="row form-wrapper">
                
                <?=$_no_escape['tab_html']?>

                <!-- left column -->
                <div class="col-md-12 column" style="margin-bottom:30px">
                    
                    <?php
                    echo form_open($submit_url , array('class'=>"form-horizontal" , 'role'=>"form" , 'id' => "editForm"));
                    ?>

                    <div id="tabContent" class="tab-content">
                    <?=$_no_escape['content_html']?>
                    </div>

                    <div class="separator"></div>
                    <div>
                        <p class="text-center">
                        <button type="button" class="btn btn-lg btn-default" style="width:130px" id="btnCancel">キャンセル</button>
                        <button type="button" class="btn btn-lg btn-success" style="width:130px" id="btnPreview">プレビュー</button>
                        <button type="button" class="btn btn-lg btn-primary" style="width:130px" id="btnSubmit">登　録</button>
                        <input type="hidden" name="submit_data" value="1">
                        <?php /*<button type="submit" class="btn btn-lg btn-primary" name="submit" value="1" style="width:130px">登　録</button>*/ ?>
                        </p>
                    </div>
                    
                    <?php
                    echo form_close();
                    ?>

                </div>


                <?php 
                if(!empty($tab_btm_html)) {
                    echo $_no_escape['tab_btm_html'];
                }
                ?>


            </div>
        </div>
        </div>



    <?php
    //プレビュー画像表示用のモーダル
    ?>
    <div class="remodal1" id="imgModal" data-remodal-id="imgModal">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close remodal-close" data-remodal-action="close">&times;</button>
              <h4 class="modal-title">画像ファイル</h4>
            </div>
            <div class="modal-body"><p class="text-center"></p></div>
            <div class="modal-footer">
                <div class="text-center">
                <button type="button" class="btn btn-default remodal-close" data-remodal-action="close">閉じる</button>
                </div>
            </div>
          </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

</div>
<!-- end main container -->


<?php
// footer include
$this->load->view("common/footer1.php");
?>

<script src="<?= base_url() ?>js/datetimepicker/build/jquery.datetimepicker.full.min.js"></script>
<script src="<?= base_url() ?>js/ckeditor-full/ckeditor.js"></script>
<script src="<?= base_url() ?>js/Remodal/jquery.remodal.js"></script>
<script src="<?= base_url() ?>js/jquery.tmpl.min.js"></script>
<script src="<?= base_url() ?>js/jquery.tile.min.js"></script>
<script>
$(function(){
    gNaviActive('<?=ucwords($dirname)?>');
    subNaviActive('<?=ucwords($dirname)?>' , "edit");
});

</script>
<?php
// js include
$this->load->custom_view($dirname , "edit/js/edit");
?>
<?php
//add js include
if(!empty($add_js)) {
$this->load->custom_view($dirname , $add_js);
}
?>
<?php
// js include
$this->load->custom_view($dirname , "media/js/common");
?>
<?php
// footer include
$this->load->view("common/footer2.php");
?>