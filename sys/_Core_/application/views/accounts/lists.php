<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$data["page_setting"] = array(
  "title" => ADMINPAGE_NAME,
  "css"   => array(
    base_url().'css/compiled/list.css',
    base_url().'css/compiled/tables.css',
    base_url().'js/Remodal/jquery.remodal.css',
  ),
  "page_id" => "account_lists",
  "menu_active" => array()
);

$this->load->view("common/header",$data);
?>


<!-- main container -->
<div class="content">
    
    
        <div id="pad-wrapper">

            <div class="row">
                <div class="col-md-12">
                    <h3 class="page-ttl icon_account">アカウント一覧
                    </h3>
                    <div class="alert alert-explain">
                    <span class="info-icon"><i class="icon-exclamation-sign"></i></span>
                    <span class="info-text">アカウントの編集を行いたい場合は、担当者名または編集ボタンをクリックしてください。</span>
                    </div>

                </div>
            </div>

            <!-- account data table-->
            <div class="row body-area">

                <div class="table-wrapper">
                <div class="col-md-12">
                
                    <table class="table table-hover<?php /* table-striped*/?>">
                        <thead>
                            <tr>
                                <th width="2%"> </th>
                                <th width="15%">担当者名</th>
                                <th width="15%" class="text-center">アカウントID</th>
                                <?php /*
                                <th width="7%" class="text-center">権限</th>
                                <th width="18%" class="text-center">メールアドレス</th>
                                */ ?>
                                <th width="10%" class="text-center">最終ログイン</th>
                                <th width="10%" class="text-center">操作</th>
                            </tr>
                        </thead>
                        <tbody>

                        <?php
                        if(!empty($result)) :
                            $cnt = 0;
                            foreach($result as $data) :
                                $tr_class = '';
                                $cnt++;
                                if($cnt == 1) {
                                    $tr_class = ' class="first"';
                                }
                        ?>

                        <!-- row -->
                        <tr<?= $tr_class ?>>
                            <td></td>
                            <td>
                                <a href="<?php echo $data['edit_link']; ?>" class="btn-edit list-title"><?= $data['user_name'] ?></a>
                            </td>

                            <td class="text-center"><?= $data['login_account'] ?></td>

                            <?php /*
                            <td class="text-center">
                            <?php
                            if(!empty($authority_list[$data['authority']])) {
                                echo $authority_list[$data['authority']];
                            }
                            ?>
                            </td>

                            <td class="text-center"><?= $data['email'] ?></td>

                            */
                            ?>
                            <td class="text-center">
                            <?php 
                            if((int)$data['last_login'] !== 0) {
                                echo $data['last_login'];
                            }
                            ?>
                            </td>

                            <td class="edit-menu text-center">
                                <a href="<?php echo $data['edit_link']; ?>" class="btn-edit"><i class="icon-pencil" data-toggle="tooltip" title="編集"></i></a>　
                                <?php //id=1 のカテゴリは消さない
                                    if($data["id"] != 1) :
                                ?>
                                <a href="#" class="btn-delete" data-toggle="modal" data-name="<?= $data['login_account'] ?>" data-id="<?= $data['id'] ?>"><i class="icon-trash" data-toggle="tooltip" title="削除"></i></a>
                                <?php
                                    else :
                                ?>
                                <input type="hidden" id="accountID1Name" value="<?= $data['login_account'] ?>">
                                <?php
                                    endif;
                                ?>
                            </td>

                        </tr>
                        <!-- row -->

                        <?php
                            endforeach;
                        endif;
                        ?>
                        </tbody>
                    </table>

                </div>
            </div>


            <div class="col-md-12">
            <?php
            if(empty($result)) :
            //------------------------------------------------------------------
            // データなし
            ?>
            <p>アカウントの登録がありません</p>
            <?php
            endif;
            ?>

            </div>


            </div>
            <!-- end account data table -->

        </div>



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
          <div class="modal-content" style="width:750px">
            <div class="modal-header">
              <button type="button" class="close remodal-close" data-remodal-action="close">&times;</button>
              <h4 class="modal-title">アカウントの削除</h4>
            </div>
            <div class="modal-body">
            
                <div class="alert alert-error">
                <span class="info-icon"><i class="icon-exclamation-sign"></i></span>
                <span class="info-text">アカウントID「<b id="delAccountName"></b>」のアカウントを削除してもよろしいですか？<br>
                <small>※このアカウントが作成したデータは、オーナーアカウントである「<b id="accountID1Name"></b>」に引き継がれます。</small>
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
$this->load->view("common/footer1");
?>
<script src="<?= base_url() ?>js/Remodal/jquery.remodal.js"></script>
<script>
$(function(){
    gNaviActive('Account');
    subNaviActive('Account' , "list");
});
</script>
<?php
// js include
$this->load->view("accounts/js/list");
?>
<?php
// footer include
$this->load->view("common/footer2");
?>