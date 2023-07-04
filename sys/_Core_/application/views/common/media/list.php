<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$data["page_setting"] = array(
  "title" => 'ファイル選択 | '.ADMINPAGE_NAME,
  "css"   => array(
    base_url().'css/compiled/media.css',
    base_url().'js/Remodal/jquery.remodal.css'
  ),
  "page_id" => (!empty($dirname)?$dirname.'_':'') . "media_list",
  "menu_active" => array()
);
$data["hidden_navigation"] = true;

$this->load->view("common/header.php",$data);
?>


<!-- main container -->
<div class="content media-list">

        <div id="pad-wrapper" class="gallery">
            <ul class="nav nav-tabs" style="margin-bottom:20px">
              <li class="active"><a href="<?=$listpage_url?>">ファイル一覧</a></li>
              <li><a href="<?=$listpage_upload_url?>">アップロード</a></li>
            </ul>
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-explain">
                    <span class="info-icon"><i class="icon-exclamation-sign"></i></span>
                    <span class="info-text">ファイルを選択してください</span>
                    </div>
                </div>
            </div>

            <?php 
            if (!empty($this->session->flashdata('upload_file_num'))) :
            ?>
            <div class="row info-alert">
                <div class="col-md-12">
                <div class="alert alert-success">
                    <i class="icon-ok-sign"></i> <?php echo count($this->session->flashdata('upload_file_num')); ?>件のファイルをアップロードしました。
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
                        echo "【".$key."番目のファイル】 ".$err;
                    endforeach;
                    ?>
                </div>
                </div>
            </div>
            <?php
            endif;
            ?>

            <?php
            //キーワードで絞りこまれているか
            if(!empty($keyword)) :
            ?>
            <div class="row info-alert">
                <div class="col-md-12">
                <div class="alert alert-success">
                <i class="icon-ok-sign"></i>
                キーワード「<?= $keyword ?>」で検索した結果を一覧表示しています。<span class="text-right"><a href="<?= $listpage_url ?>">すべて表示する</a></span>
                </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="row page-info">
                <div class="page-info-left col-md-4">
                <p><?= $_no_escape['html_pager_head'] ?></p>
                </div>

                <div class="page-info-right col-md-8">
                    
                    <?php echo form_open('' , array('name'=>'search_form' , 'id'=>'searchForm' , 'method'=>'get')); ?>
                        <div class="pull-right" style="padding-right:5px">
                            <input type="text" name="keyword" class="search" placeholder="キーワード検索" value="<?= (!empty($keyword) ? $keyword : '') ?>">
                            <?php if(!empty($type)) : ?>
                            <input type="hidden" name="type" value="<?=$type?>">
                            <?php endif; ?>
                            <?php if(!empty($called)) : ?>
                            <input type="hidden" name="called" value="<?=$called?>">
                            <?php endif; ?>
                            <input type="hidden" name="target" value="<?=$target?>">
                        </div>
                    <?php echo form_close(); ?>
                </div>
            </div>

            <!-- gallery wrapper -->
            <div class="gallery-wrapper body-area">

                <?php
                if(!empty($result['medialist'])) :
                ?>
                <ul class="media-select-list">
                <?php
                    foreach($result['medialist'] as $media) :
                ?>
                <li data-toggle="tooltip" data-html="false" title="<?= $media['upload_filename'].(!empty($media['description']) ? '<br>（'.$media['description'].'）':'') ?>">
                <span class="cover" id="file_<?=$media["id"]?>" style="background-image:url('<?= $this->mediafile->get_thumbpath($media['filename'])?>')"></span>
                <span class="fileinfo" data-filetype="<?= $media['type'] ?>" data-filepath="<?=$this->mediafile->getUploadPath($media['filename'])?>" data-filename="<?= $media['upload_filename'] ?>" data-filesmpsizepath="<?=$this->mediafile->get_thumbpath($media['filename'] , '_smp')?>" data-filethumbpath="<?=$this->mediafile->get_thumbpath($media['filename'])?>" style="display:none"><?=$media["filename"]?></span>
                </li>
                <?php
                    endforeach;
                ?>
                </ul>

                <?php
                else :
                //------------------------------------------------------------------
                // データなし
                ?>

                <p>
                    ファイルが見つかりません
                </p>

                <?php
                endif;
                //------------------------------------------------------------------
                ?>

            </div>
            <!-- end gallery wrapper -->

        </div>

        <?php
        //pager
        ?>
        <div class="text-center" style="margin-top:10px">
        <?= $_no_escape['pager_html'] ?>
        </div>


        <div class="text-center">
          <button type="button" class="btn btn-default btn-win-close">ウインドウを閉じる</button>
        </div>


    </div>

    <!-- Modal -->
    <div class="remodal1" id="fileModal" data-remodal-id="fileModal">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close remodal-close" data-remodal-action="close">&times;</button>
              <h4 class="modal-title">このファイルを選択しますか？</h4>
            </div>
            <div class="modal-body"><div class="text-center"></div></div>
            <div class="modal-footer">
                <div class="text-center">
                    <button type="button" class="btn btn-default remodal-close" data-remodal-action="close">キャンセル</button>
                    <button type="button" id="btnSelect" class="btn btn-primary">選択する</button>
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