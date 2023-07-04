<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$data["page_setting"] = array(
  "title" => ADMINPAGE_NAME,
  "css"   => array(
    base_url().'css/compiled/media.css',
    base_url().'js/Remodal/jquery.remodal.css',
    base_url().'js/boxer/jquery.fs.boxer.min.css'
  ),
  "page_id" => (!empty($dirname)?$dirname.'_':'') . "media",
  "menu_active" => array()
);

$this->load->custom_view('' , "common/header.php" , $data);
?>


<!-- main container -->
<div class="content media-index">
    
    
        <div id="pad-wrapper" class="gallery">

            <div class="row">
                <div class="col-md-12">
                    <h3 class="page-ttl icon_<?=$dirname?>">
                    <?php 
                    if(!empty($adminpage_title)) :
                        echo $adminpage_title;
                    else :
                    ?>
                    ファイル管理
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
                    ここでは、<?= DATA_NAME ?>コンテンツ内で利用できる画像やPDF等のファイルをアップロードすることができます。<br>
                    アップロードされたファイルは、以下に一覧で表示されており、ファイル内容の確認や削除などの操作を行うことができます。
                    <?php endif; ?>
                    </span>
                    <span class="close-icon"><a href="#" class="close-icon"><i class="icon-remove-sign"></i></a></span>
                    </div>
                </div>
            </div>

            <?php 
            if(!empty($this->session->flashdata('success'))) :
            ?>
            <div class="row info-alert">
                <div class="col-md-12">
                <div class="alert alert-success">
                    <i class="icon-ok-sign"></i> <?php echo count($this->session->flashdata('success')); ?>件のファイルをアップロードしました。
                </div>
                </div>
            </div>
            <?php
            endif;
            ?>
            <?php 
            if(!empty($this->session->flashdata('errors'))) :
            ?>
            <div class="row info-alert">
                <div class="col-md-12">
                <div class="alert alert-danger">
                    <i class="icon-remove-sign" style="margin-bottom:0"></i>
                    <?php
                    $cnt = 0;
                    foreach($this->session->flashdata('errors') as $key => $err) :
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

            <div class="row filter-block" style="margin-top:20px">
                <div class="col-md-3">
                    <a href="#" class="btn-upload btn-flat primary large">　＋ ファイルを追加　</a>
                </div>
            </div>
            

            <div class="row page-info">

                <?php
                //キーワードで絞りこまれているか
                if(!empty($keyword)) :
                ?>
                <div class="row info-alert">
                    <div class="col-md-12">
                    <div class="alert alert-success">
                    <i class="icon-ok-sign"></i>
                    キーワード「<?= $keyword ?>」で検索した結果を一覧表示しています。<span class="text-right"><a href="<?= $index_url ?>">すべて表示する</a></span>
                    </div>
                    </div>
                </div>
                <?php endif; ?>

                <div class="page-info-left col-md-4">
                <p><?= $_no_escape['html_pager_head'] ?></p>
                </div>

                <div class="page-info-right col-md-8">
                    
                    <?php echo form_open('' , array('name'=>'search_form' , 'id'=>'searchForm' , 'method'=>'get')); ?>
                        <div class="pull-right" style="padding-right:5px">
                            <input type="text" name="keyword" class="search" placeholder="キーワード検索" value="<?= (!empty($keyword) ? $keyword : '') ?>">
                        </div>
                    <?php echo form_close(); ?>
                </div>
            </div>


            <!-- gallery wrapper -->
            <div class="gallery-wrapper body-area">

                <?php echo form_open($delete_url , array('name'=>'del_form' , 'id'=>'delForm' , 'method'=>'post') ); ?>

                <?php
                if(!empty($result['medialist'])) :
                ?>
                <ul class="media-list">
                <?php
                    foreach($result['medialist'] as $media) :
                ?>
                <li data-toggle="tooltip" title="<?= $media['upload_filename'] ?>">
                <span class="cover" id="file_<?=$media["id"]?>" style="background-image:url('<?= $this->mediafile->get_thumbpath($media['filename'])?>')"></span>
                <div class="over-menu" id="over_menu_<?=$media["id"]?>">
                    <?php
                    $icon1_title = '詳細表示';
                    $icon1_src = 'ico_zoom_w.png';
                    if($media['type'] == 'word' || $media['type'] == 'excel') {
                        $icon1_title = 'ダウンロード';
                        $icon1_src = 'ico_download_w.png';
                    }
                    ?>
                    <span class="menu-item action-preview file-<?= $media['type'] ?>"><a href="<?=$this->mediafile->getUploadPath($media['filename'])?>" title="<?=$media["filename"]?>" data-imgpath="<?=$this->mediafile->get_thumbpath($media['filename'] , '_smp')?>" data-toggle="lightbox" data-footer="<?= $media['description'] ?>"><img src="<?= base_url() ?>/img/icons/<?= $icon1_src; ?>" alt="<?= $icon1_title ?>" class="rover" data-toggle="tooltip" title="<?= $icon1_title ?>"></a></span>
                <?php
                    //権限チェック
                    if($this->auth->chk_controll_limit("delete_files")) :
                ?>
                    <span class="menu-item action-delete"><a href="javascript:void(0);"><img src="<?= base_url() ?>/img/icons/ico_trashbox_w.png" alt="削除" class="rover" data-toggle="tooltip" title="ファイルの削除"></a></span>
                <?php
                    endif;
                ?>
                
                <span class="filename" style="display:none" data-id="<?=$media["id"]?>"><?=$media["filename"]?></span>
                </div>
                </li>
                <?php
                    endforeach;
                ?>
                </ul>
                <input type="hidden" name="del_file" id="deleteFilename" value="" />
                <input type="hidden" name="del" value="1" />
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

                <?php
                echo form_close();
                ?>

            </div>
            <!-- end gallery wrapper -->

        </div>

        <?php
        //pager
        ?>
        <div class="text-center">
        <?= $_no_escape['pager_html'] ?>
        </div>

    </div>

    <?php
    //----------------------------------------------------------------
    //
    // Modal
    //
    //----------------------------------------------------------------
    ?>
    <div class="remodal1" id="uploadModal" data-remodal-id="uploadModal">
        <div class="modal-dialog">
          <div class="modal-content" style="width:850px;">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal remodal-close" data-remodal-action="close">&times;</button>
              <h4 class="modal-title">ファイルアップロード</h4>
            </div>
            <div class="modal-body">

                <div class="alert alert-explain alert-fileup">
                <span class="info-icon"><i class="icon-exclamation-sign"></i></span>
                <span class="info-text">同時に4つまでのファイルをアップロードできます。<br>
                <strong>（ファイル名は半角英数字とハイフン、アンダーバーのみ利用可）</strong><br>
                アップロードできるファイルは<strong> jpg , gif , png , pdf </strong>で、一度に<strong><?= $upload_maxsize ?>KB</strong>までになります。</span>
                </div>
                <?php echo form_open_multipart($upload_url , array('class'=>"form-horizontal" , 'role'=>"form" , 'id' => "uploadForm"));?>
                <?php for($i=0;$i<4;$i++) : ?>

                    <div class="fileup-items">
                    <div class="form-group">
                        <label for="input1" class="col-md-3 control-label">ファイル（<?= ($i+1) ?>）:</label>
                        <div class="col-md-9">
                            <input type="file" name="upfile[<?= $i ?>]" id="input<?= $i ?>" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="input1" class="col-md-3 control-label">説明・タグ :</label>
                        <div class="col-md-6">
                          <input type="text" name="upfile_desc[<?= $i ?>]" class="form-control" placeholder="説明・タグ" id="inputTag<?= $i ?>">
                        </div>
                    </div>
                    <div class="preview" id="preview<?= $i ?>" style="display:none"><img src="<?= base_url() ?>/img/lens.png"></div>
                    </div>

                    <?php if($i!=4) : ?>
                    <?php endif; ?>
                <?php endfor; ?>
                <input type="hidden" name="upload_flg" value="1">
                <?php echo form_close(); ?>
            </div>
            <div class="modal-footer">
                <div class="text-center" style="padding-bottom:20px">
                  <button type="button" class="btn btn-default remodal-close" data-remodal-action="close">キャンセル</button>
                  <button type="button" id="btnUpload" class="btn btn-primary">アップロード</button>
                </div>
            </div>
          </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.remodal -->


    <div class="remodal1" id="delModal" data-remodal-id="delModal">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close remodal-close" data-remodal-action="close">&times;</button>
              <h4 class="modal-title">ファイルの削除</h4>
            </div>
            <div class="modal-body">
            このファイルを削除してもよろしいですか？
            </div>
            <div class="modal-footer">
                <div class="text-center">
                  <button type="button" class="btn btn-default remodal-close" data-remodal-action="close">キャンセル</button>
                  <button type="button" id="btnDelete" class="btn btn-primary">削除する</button>
              </div>
            </div>

            <?php echo form_open($delete_url , array('name'=>'del_form' , 'id'=>'delForm' , 'method'=>'post') ); ?>
            <input type="hidden" name="del_id" id="deleteID" value="" />
            <input type="hidden" name="del" value="1" />
            <?php
            echo form_close();
            ?>
          </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div class="remodal1" id="imgModal" data-remodal-id="imgModal">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close remodal-close" data-remodal-action="close">&times;</button>
              <h4 class="modal-title">画像ファイル</h4>
            </div>
            <div class="modal-body" style="padding-bottom:5px"><p class="text-center"></p></div>
            <div class="modal-footer" style="padding-top:10px">
                <div class="text-center">
                <p class="filename-str" style="font-size:0.9em"></p>
                <button type="button" class="btn btn-default remodal-close" data-remodal-action="close">閉じる</button>
                </div>
            </div>
          </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div class="remodal1" id="pdfModal" data-remodal-id="pdfModal">
        <div class="modal-dialog">
          <div class="modal-content" style="width:900px; margin-left: 0px;">
            <div class="modal-header">
              <button type="button" class="close remodal-close" data-remodal-action="close">&times;</button>
              <h4 class="modal-title">PDFファイル</h4>
            </div>
            <div class="modal-body">
            <iframe src="#" width="99.6%" height="550" frameborder="0"></iframe>
            </div>
            <div class="modal-footer" style="margin-top:5px;padding-top:7px">
                <div class="text-center">
                <p style="width:70%;margin:0 auto;"><input class="form-control" id="pdfFilePath" title="" type="text" value=""></p>
                <p style="margin-top:10px"><button class="clip-btn btn btn-primary" data-clipboard-action="copy" data-clipboard-target="#pdfFilePath">ファイルパスをコピー</button>
                <button type="button" class="btn btn-default remodal-close" data-remodal-action="close">閉じる</button>
                </p>
                </div>
            </div>
          </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

</div>
<!-- end main container -->


<?php
// footer include
$this->load->custom_view('' , "common/footer1.php");
?>
<script src="<?= base_url() ?>js/Remodal/jquery.remodal.js"></script>
<script src="<?= base_url() ?>js/boxer/jquery.fs.boxer.min.js"></script>
<script src="<?= base_url() ?>js/clipboard.min.js"></script>
<script>
$(function(){
    gNaviActive('<?=ucwords($dirname)?>');
    subNaviActive('<?=ucwords($dirname)?>' , "media");

    var clipboard = new Clipboard('.clip-btn');
});

</script>
<?php
// js include
$this->load->custom_view($dirname , "media/js/index");
?>
<?php
// footer include
$this->load->custom_view('' , "common/footer2.php");
?>