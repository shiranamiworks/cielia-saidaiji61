<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$data["page_setting"] = array(
  "title" => ADMINPAGE_NAME,
  "css"   => array(
    base_url().'css/compiled/list.css',
    base_url().'css/compiled/tables.css',
    base_url().'js/Remodal/jquery.remodal.css',
  ),
  "page_id" => (!empty($dirname)?$dirname.'_':'') . "lists",
  "menu_active" => array()
);

$this->load->custom_view('' , "common/header" , $data);
?>


<!-- main container -->
<div class="content">
    
    
        <div id="pad-wrapper">

            <div class="row">
                <div class="col-md-12">
                    <h3 class="page-ttl icon_<?=$dirname?>">
                    <?php 
                    if(!empty($adminpage_title)) :
                        echo $adminpage_title;
                    else :
                    ?>
                    <?=DATA_NAME?>一覧
                    <?php endif; ?>
                    </h3>
                    <div class="alert alert-explain">
                    <span class="info-icon"><i class="icon-exclamation-sign"></i></span>
                    <span class="info-text">
                    <?php if(!empty($adminpage_infotext)) :
                    echo $_adminpage_infotext;
                    else : 
                    ?>
                    ここでは、登録されている<?=DATA_NAME?>の一覧が表示されており、登録内容の確認や削除などの操作を行うことができます。<br>登録内容の編集を行いたい場合は、タイトルまたは編集ボタンをクリックしてください。
                    <?php endif; ?>
                    </span>
                    </div>

                </div>
            </div>

            <?php
            //承認待ちデータの案内（承認フローオプションが有効の場合のみ表示）
            if(!empty($cnt_wait)) :
            ?>
            <div class="row info-alert">
                <div class="col-md-12">
            <?php
                if(!isset($_GET["wait"])) :
            ?>
                <div class="alert alert-warning">
                <i class="icon-ok-sign"></i>
                承認待ちの<?=DATA_NAME?>が<a href="<?= $waitdata_url ?>"><?= $cnt_wait ?>件</a>あります。
                <span class="text-right"><a href="<?= $waitdata_url ?>">承認待ち一覧を表示する</a></span>
                </div>
            <?php
                else:
            ?>
                <div class="alert alert-success">
                <i class="icon-ok-sign"></i>
                承認待ちの<?=DATA_NAME?>のみを表示しています。
                <span class="text-right"><a href="<?= $search_base_url ?>">すべての<?=DATA_NAME?>を表示する</a></span>
                </div>
            <?php
                endif;
            ?>
                </div>
            </div>
            <?php
            endif;
            ?>

            <?php
            //絞りこみされている場合
            if(!empty($search_result_text)) :
            ?>
            <div class="row info-alert">
                <div class="col-md-12">
                <div class="alert alert-success">
                <i class="icon-ok-sign"></i>
                <?= $search_result_text ?>の<?=DATA_NAME?>のみを一覧表示しています。<span class="text-right"><a href="<?= $search_base_url ?>">絞り込みを解除</a></span>
                </div>
                </div>
            </div>
            <?php endif; ?>

            <?php
            //その他ボタンの表示
            if(isset($data['listSetting']['display_etc_btn']) && !empty($data['listSetting']['display_etc_btn']['label'])) : 
            ?>
            <div class="row page-info" style="margin:10px 0">
                <div class="page-info-right col-md-12">
                <a href="<?= $data['listSetting']['display_etc_btn']['link'] ?>" class="btn-flat primary large" style="font-size:1.1em"><?= $data['listSetting']['display_etc_btn']['label'] ?></a>
                </div>
            </div>
            <?php
            endif;
            ?>

            <?php if(!empty($_search_html)) : ?>
            <div class="row page-info" style="margin-top:5px">
                <div class="page-info-right col-md-12">
                    
                    <?php echo form_open($search_base_url , array('name'=>'search_form' , 'id'=>'searchForm' , 'method'=>'get') ); ?>
                    <ul class="search-items">
                        <li>
                            <label>データ検索：</label>
                        </li>
                        <?php
                        echo $_search_html;
                        ?>
                        <li class="search-btn">
                            <button type="submit" class="btn-flat inverse">検索</button>
                        </li>
                    </ul>
                    <?php echo form_close(); ?>

                </div>
            </div>
            <?php endif; ?>

            <div class="row page-info" style="margin-top:5px">
                <div class="page-info-left col-md-4">
                <p><?= $_no_escape['html_pager_head'] ?></p>
                </div>
            </div>
            <!-- categories table-->
            <div class="row body-area">

            <?php echo form_open($delete_url , array('name'=>'del_select_form' , 'id'=>'delSelectForm' , 'method'=>'post') ); ?>
                <div class="table-wrapper">
                <div class="col-md-12">
                
                    <table class="table table-hover<?php /* table-striped*/?>">
                        <?php
                        echo $_html_list_header;
                        ?>
                        <tbody>
                        <?php
                        echo $_html_list_body;
                        ?>
                        </tbody>
                    </table>

                </div>


            <?php
            echo $_html_list_footer;
            ?>

            </div>

            <?php
            echo form_close();
            ?>

            </div>
            <!-- end category table -->

        </div>



        <?php
        //pager
        ?>
        <div class="text-center">
        <?= $_no_escape['pager_html'] ?>
        </div>

        <?php
        //登録ボタンの表示
        if(isset($data['listSetting']['display_registered_btn']) && $data['listSetting']['display_registered_btn'] === true) : 
        ?>
        <div class="text-center" style="margin-top:20px">
        <a href="<?=$edit_page_url?>" class="btn-flat primary large" style="font-size:1.1em">＋ <?=DATA_NAME?>を登録</a>
        </div>
        <?php
        endif;
        ?>

    </div>

    <?php
    //----------------------------------------------------------------
    //
    // Modal
    //
    //----------------------------------------------------------------
    ?>
    <div class="remodal1" id="delModal" data-remodal-id="delModal">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close remodal-close" data-remodal-action="close">&times;</button>
              <h4 class="modal-title">削除の確認</h4>
            </div>
            <div class="modal-body">
            
                <div class="alert alert-error">
                <span class="info-icon"><i class="icon-exclamation-sign"></i></span>
                <span class="info-text">「<b id="delDataName"></b>」を削除してもよろしいですか？
                </span>
                </div>

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
    </div><!-- /.remodal -->


    <div class="remodal1" id="publishModal" data-remodal-id="publishModal">
        <div class="modal-dialog">
          <div class="modal-content" style="width:700px;">
            <div class="modal-header">
              <button type="button" class="close remodal-close" data-remodal-action="close">&times;</button>
              <h4 class="modal-title">
              <?php 
              if(!empty($data['listSetting']['output_flag_str'])) {
                echo $data['listSetting']['output_flag_str'];
              } else {
                echo '公開';
              }
              ?>切り替え
              </h4>
            </div>
            <div class="modal-body">
            <iframe src="<?php echo $blank_page_url; ?>" width="99.6%" frameborder="0" scrolling="no"></iframe>
            </div>
          </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.remodal -->


    <div class="remodal1" id="chkselectDeleteModal" data-remodal-id="chkselectDeleteModal">
        <div class="modal-dialog">
          <div class="modal-content" style="width:700px;">
            <div class="modal-header">
              <button type="button" class="close remodal-close" data-remodal-action="close">&times;</button>
              <h4 class="modal-title">削除の確認</h4>
            </div>
            <div class="modal-body">

                <div class="alert alert-error">
                <span class="info-icon"><i class="icon-exclamation-sign"></i></span>
                <span class="info-text">チェックを入れたデータを削除してもよろしいですか？
                </span>
                </div>

            </div>
            <div class="modal-footer">
                <div class="text-center">
                  <button type="button" class="btn btn-default remodal-close" data-remodal-action="close">キャンセル</button>
                  <button type="button" id="btnChkSelectDelete" class="btn btn-primary">削除する</button>
                </div>
            </div>

          </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.remodal -->


</div>
<!-- end main container -->


<?php
// footer include
$this->load->custom_view('' , "common/footer1");
?>
<script src="<?= base_url() ?>js/Remodal/jquery.remodal.js"></script>
<script>
$(function(){
    gNaviActive('<?=ucwords($dirname)?>');
    subNaviActive('<?=ucwords($dirname)?>' , "list");

    $('.title a').popover();
});
</script>
<?php
// js include
$this->load->custom_view($dirname , "lists/js/common");
?>
<?php
// footer include
$this->load->custom_view('' , "common/footer2");
?>