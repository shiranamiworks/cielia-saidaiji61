<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$data["page_setting"] = array(
  "title" => "カテゴリ管理",
  "css"   => array(
    base_url().'css/compiled/tables.css',
    base_url().'css/compiled/category.css',
    base_url().'js/Remodal/jquery.remodal.css',
  ),
  "page_id" => (!empty($dirname)?$dirname.'_':'') . "category",
  "menu_active" => array()
);

$this->load->custom_view('' , "common/header.php" , $data);
?>


<!-- main container -->
<div class="content category-list">
    
    
        <div id="pad-wrapper" class="gallery">

            <div class="row">
                <div class="col-md-12">
                    <h3 class="page-ttl icon_<?=$dirname?>">
                    <?php 
                    if(!empty($adminpage_title)) :
                        echo $adminpage_title;
                    else :
                    ?>
                    カテゴリ管理
                    <?php
                    endif;
                    ?>
                    </h3>

                    <div class="alert alert-explain">
                    <span class="info-icon"><i class="icon-exclamation-sign"></i></span>
                    <span class="info-text">ここでは、登録されているカテゴリの一覧が表示されており、登録内容の確認や削除の操作を行うことができます。<br>カテゴリ名の変更を行いたい場合はカテゴリ名または編集ボタンをクリックしてください。</span>
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
                    <i class="icon-ok-sign"></i> 登録が完了しました。
                </div>
                </div>
            </div>
            <?php
            endif;
            ?>

            <!-- categories table-->
            <div class="row body-area">
                <div class="table-wrapper">
                <div class="col-md-6">
                <?php
                if(!empty($result)) :
                ?>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th class="col-md-3">
                                    カテゴリ名
                                </th>
                                <th class="col-md-1 text-center">
                                    記事数
                                </th>
                                <th class="col-md-1 text-center">
                                    操作
                                </th>
                            </tr>
                        </thead>
                        <tbody>

                        <?php
                            $cnt = 0;
                            foreach($result as $category) :
                                $tr_class = "";
                                $cnt++;
                                if($cnt == 1) {
                                    $tr_class = ' class="first"';
                                }
                        ?>

                        <!-- row -->
                        <tr<?= $tr_class ?>>
                            <td>
                                <a href="#editModal" data-toggle="modal" class="btn-edit" data-name="<?= $category['category_name'] ?>" data-id="<?= $category['category_id'] ?>"><?= $category['category_name'] ?></a>
                            </td>
                            <td class="text-center">
                            <?php
                            echo !empty($cnt_category_by[$category["category_id"]]) ? $cnt_category_by[$category["category_id"]] : 0;
                            ?>
                            </td>
                            <td class="edit-menu text-center">
                                <a href="#" class="btn-edit" data-toggle="modal" data-name="<?= $category['category_name'] ?>" data-id="<?= $category['category_id'] ?>"><i class="icon-pencil" data-toggle="tooltip" title="編集"></i></a>　
                                <?php //id=1 のカテゴリは消さない
                                    if($category["category_id"] != 1) :
                                ?>
                                <a href="#" class="btn-delete" data-toggle="modal" data-name="<?= $category['category_name'] ?>" data-id="<?= $category['category_id'] ?>"><i class="icon-trash" data-toggle="tooltip" title="削除"></i></a>
                                <?php
                                    else :
                                ?>
                                　
                                <input type="hidden" id="catID1Name" value="<?= $category['category_name'] ?>">
                                <?php
                                    endif;
                                ?>
                            </td>

                        </tr>
                        <!-- row -->

                        <?php
                            endforeach;
                        ?>
                        </tbody>
                    </table>

                <?php
                else:
                ?>
                <div class="alert alert-info">
                    <i class="icon-exclamation-sign"></i>
                    カテゴリの登録がありません
                </div>
                <?php
                endif;
                ?>

                <div style="margin-top:30px">
                <a href="#" class="btn-edit btn-flat primary large">＋ カテゴリ登録</a>
                </div>

                </div>
            </div>
            </div>
            <!-- end category table -->

        </div>


    </div>

    <?php
    //----------------------------------------------------------------
    //
    // Modal
    //
    //----------------------------------------------------------------
    ?>
    <div class="remodal1" id="editModal" data-remodal-id="editModal">        
        <div class="modal-dialog">
          <div class="modal-content" style="width:750px">
            <div class="modal-header">
              <button type="button" class="close remodal-close" data-remodal-action="close">&times;</button>
              <h4 class="modal-title">カテゴリ登録・編集</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-explain">
                <span class="info-icon"><i class="icon-exclamation-sign"></i></span>
                <span class="info-text">カテゴリ名の登録、または編集を行って下さい。</span>
                </div>

                <?php echo form_open($edit_url , array('class'=>"form-horizontal" , 'role'=>"form" , 'id' => "editForm"));?>

                <div class="row" style="margin-top:30px">
                <div class="field-box">
                    <div class="col-sm-2 text-right"><label>カテゴリ名 :</label></div>
                    <div class="col-sm-8">
                        <input type="text" name="name" id="inputCategoryName" class="form-control" maxlength="30" />
                        <input type="hidden" name="edit_id" id="editID" value="">
                    </div>                            
                </div>

                </div>

                <?php echo form_close(); ?>

            </div>
            <div class="modal-footer">
                <div class="text-center">
                    <button type="button" class="btn btn-default remodal-close" data-remodal-action="close">キャンセル</button>
                    <button type="button" id="btnEdit" class="btn btn-primary">登録する</button>
                </div>
            </div>
          </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.remodal -->

    <div class="remodal1" id="delModal" data-remodal-id="delModal">
        <div class="modal-dialog">
          <div class="modal-content" style="width:750px">
            <div class="modal-header">
              <button type="button" class="close remodal-close" data-remodal-action="close">&times;</button>
              <h4 class="modal-title">カテゴリの削除</h4>
            </div>
            <div class="modal-body">
            
                <div class="alert alert-error">
                <span class="info-icon"><i class="icon-exclamation-sign"></i></span>
                <span class="info-text">カテゴリ「<b id="delCategoryName"></b>」を削除してもよろしいですか？<br>
                <small>※このカテゴリを選択しているお知らせがある場合、自動的に「<b id="categoryID1Name"></b>」カテゴリに変更されます。</small>
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

</div>
<!-- end main container -->


<?php
// footer include
$this->load->custom_view('' , "common/footer1.php");
?>
<script src="<?= base_url() ?>js/Remodal/jquery.remodal.js"></script>
<script>
$(function(){
    gNaviActive('<?=ucwords($dirname)?>');
    subNaviActive('<?=ucwords($dirname)?>' , "category");
});
</script>
<?php
// js include
$this->load->custom_view($dirname , 'category/js/common');
?>
<?php
// footer include
$this->load->custom_view('' , "common/footer2.php");
?>