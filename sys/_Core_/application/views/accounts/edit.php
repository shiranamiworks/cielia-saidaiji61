<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$data["page_setting"] = array(
  "title" => ADMINPAGE_NAME,
  "css"   => array(
    base_url().'css/compiled/form-showcase.css',
    base_url().'js/Remodal/jquery.remodal.css',
  ),
  "page_id" => "account_edit",
  "menu_active" => array()
);

$this->load->view("common/header.php",$data);
?>


<!-- main container -->
<div class="content edit-page">
    
    
        <div id="pad-wrapper">

            <div class="row">
                <div class="col-md-12">
                    <h3 class="page-ttl icon_account">アカウント登録
                    </h3>
                    <div class="alert alert-explain">
                    <span class="info-icon"><i class="icon-exclamation-sign"></i></span>
                    <span class="info-text">ここでは、管理者アカウントの編集を行うことができます。</span>
                    <span class="close-icon"><a href="#" class="close-icon"><i class="icon-remove-sign"></i></a></span>
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
                    入力エラーがあります
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
                
                <!-- left column -->
                <div class="col-md-10 column">
                    
                    <?php
                    echo form_open($submit_url , array('class'=>"form-horizontal" , 'role'=>"form" , 'id' => "editForm"));
                    ?>

                    <div id="contentHome" style="margin:30px 0">

                        <div class="field-box">
                            <label class="itemname">担当者名 : <span style="font-size:70%" class="label label-danger">必須</span></label>
                            <div class="field-item">
                                <p class="comment comment-top">アカウントを利用される方の名前を入力してください</p>
                                <input name="user_name" max-length="100" class="form-control" type="text" style="width:300px" value="<?php echo set_value('user_name'); ?>">
                                <p class="comment comment-btm"></p>
                                <?php echo form_error('user_name' , $_error_container_tag ,$_error_container_tag2); ?>
                            </div>
                        </div>
                        <div class="field-box">
                            <label class="itemname">アカウントID : <span style="font-size:70%" class="label label-danger">必須</span></label>
                            <div class="field-item">
                                <p class="comment comment-top">半角英数字で入力してください</p>
                                <input name="login_account" max-length="100" class="form-control" type="text" style="width:300px" value="<?php echo set_value('login_account'); ?>">
                                <p class="comment comment-btm">（このIDは、ログインする際に入力が必要となるアカウントIDとなります）</p>
                                <?php echo form_error('login_account' , $_error_container_tag ,$_error_container_tag2); ?>
                            </div>
                        </div>
                        <div class="field-box">
                            <label class="itemname">パスワード : 
                            <?php if(empty($edit_flag)) : ?>
                            <span style="font-size:70%" class="label label-danger">必須</span>
                            <?php endif; ?>
                            </label>
                            <div class="field-item">
                                <p class="comment comment-top">半角英数字8文字〜30文字で入力してください</p>
                                <input name="password" max-length="30" class="form-control" type="password" style="width:200px">
                                <?php if(isset($edit_flag) && $edit_flag === true) : ?>
                                <p class="comment comment-btm">※パスワードを変更される場合に新しいパスワードを入力してください（変更がなければ空のまま）</p>
                                <?php endif; ?>
                                <?php echo form_error('password' , $_error_container_tag ,$_error_container_tag2); ?>
                            </div>
                        </div>

                        <div class="field-box">
                            <label class="itemname">パスワード(確認): 
                            <?php if(empty($edit_flag)) : ?>
                            <span style="font-size:70%" class="label label-danger">必須</span>
                            <?php endif; ?>
                            </label>
                            <div class="field-item">
                                <p class="comment comment-top">確認のため、再度入力してください</p>
                                <input name="password_conf" max-length="30" class="form-control" type="password" style="width:200px">
                                <?php if(isset($edit_flag) && $edit_flag === true) : ?>
                                <p class="comment comment-btm">※パスワードを変更される場合に新しいパスワードを入力してください（変更がなければ空のまま）</p>
                                <?php endif; ?>

                                <?php echo form_error('password_conf' , $_error_container_tag ,$_error_container_tag2); ?>
                            </div>
                        </div>


                    </div>


                    <div class="separator"></div>
                    <div style="padding-top:30px">
                        <p class="text-center">
                        <a href="<?=$list_page_url?>" type="button" class="btn btn-lg btn-default" style="width:130px">キャンセル</a>
                        <button type="submit" class="btn btn-lg btn-primary" name="submit" value="1" style="width:130px">登　録</button>
                        </p>
                    </div>

                    <?php
                    echo form_close();
                    ?>

                </div>
            </div>
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
    gNaviActive('Account');
    subNaviActive('Account' , "edit");
});

</script>
<?php
// js include
$this->load->view("accounts/js/edit");
?>
<?php
// footer include
$this->load->view("common/footer2.php");
?>