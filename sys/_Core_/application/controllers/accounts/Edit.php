<?php
/*
======================================================================
Project Name    : Mishima T.CMS  
 
Copyright © <2016> Teruhiko Mishima All rights reserved.
 
This source code or any portion thereof must not be  
reproduced or used in any manner whatsoever.
本ソースコードを無断で転用・転載することを禁じます。
======================================================================
*/
defined('BASEPATH') OR exit('No direct script access allowed');

require(dirname(__FILE__).'/../../core/controller/Admin/Base_Controller.php');

class Edit extends Base_Controller {

  private $edit_id = '';

	public function __construct(){
		parent::__construct();
    $this->_init();
	}


  private function _init() {

    define('ADMINPAGE_NAME' , 'アカウント編集 | '.(defined('ADMIN_BASE_TITLE') ? ADMIN_BASE_TITLE : $this->config->item('admin_title' , 'config_myapp')));
    
    $this->load->model('Admins_model');
    $this->load->library(array('auth' , 'urlsetting' , 'message'));

    //権限チェック
    $this->login_id = $this->auth->IsSuccess(true , "manage_account");

    $this->view_data['list_page_url'] = site_url('accounts/lists');

    $this->view_data['error_container_tag'] = '<div><span class="error-message"><i class="icon-remove-sign"></i> ';
    $this->view_data['error_container_tag2'] = '</span></div>';
  }


  public function index($id = '') {

    $this->view_data['submit_url'] = site_url(INDEX_CONTROLLER_PATH.'/'.$id);
    $this->view_data['error'] = array();
    $edit_flag = false;

    //if(empty($id)) {
    //  redirect(site_url('accounts/lists') , 'location');
    //}

    if(!empty($id)) {
      $editData = $this->Admins_model->find($id);
      //idの指定が不正だった場合
      if(empty($editData)) {
        $this->_error_notfound();
        return;
      }

      $this->view_data['edit_flag'] = true;
      $this->edit_id = $id;
      //編集モードで編集データ送信という流れでなければ
      if(empty($this->input->post('submit'))) {
        $_POST = $editData;
        //編集モードの場合、DBから取得したデータを入力画面にセット（パスワードは空にしておく）
        unset($_POST['password']);
        $this->_validation($id);
      }
    }

    //登録データ受信
    if(!empty($this->input->post('submit'))) {

        $validation = $this->_validation($id);

        if($validation) {

            if(empty($id)) {
              $registered_date = fn_get_date();
            }else{
              $registered_date = $editData['created'];
            }

            $db_dataset = array(
              'user_name'     => $this->input->post('user_name'),
              'login_account' => $this->input->post('login_account'),
              'email'         => 'cieliacms@cielia.com',
              'authority'     => 'MASTER',
            );

            if(!empty($this->input->post('password'))) {
              $db_dataset['password'] = $this->auth->makeEncryptedPassword($this->input->post('password') , $registered_date);
            }

            if(empty($id)) {
                //DB新規登録
                $db_dataset['login_id'] = substr( sha1(uniqid(rand(), true)) , 0 , 20); //ランダムなIDを発行
                $db_dataset['created'] = $registered_date;
            }

            $db_dataset['modified'] = $registered_date;

          try{
            //DB登録処理
            $save_where = array();
            if(!empty($id)) {
              $save_where = array('id' => $id);
            }
            $data_id = $this->Admins_model->replaceData($db_dataset , $save_where);

            if($data_id) {
              //登録成功
              $this->session->set_flashdata('save_success', true);

              redirect(site_url(INDEX_CONTROLLER_PATH.'/complete') , 'location');

            } else {
              //エラー
              throw new Exception('Data Insert Error');
            }

          } catch(Exception $e) {
            $this->_error('登録時にエラーが発生しました。恐れ入りますが再度登録をお試し下さい'.'（'.$e->getMessage().'）');
            return;
          }
        }
    }

    //権限タイプリストを取得
    $authority_list = $this->auth->get_AuthorityData();
    $this->view_data['authority_list'] = $authority_list;

    return $this->load->custom_view('' , 'accounts/edit' , $this->view_data);
  }


  /*
  登録完了画面
  */
  public function complete() {

    if(empty($this->session->flashdata('save_success'))) {
      redirect($this->view_data['list_page_url'] , 'location');
    }

    $this->load->custom_view('' , 'accounts/complete' , $this->view_data);

    return;
  }



  private function _validation($id = '') {

    $this->load->library('form_validation');
    $this->form_validation->set_rules('user_name', '担当者名', 'trim|required|max_length[30]');
    $this->form_validation->set_rules('login_account', 'アカウントID', 'trim|required|max_length[30]|callback__loginaccount_check');
    if(empty($id) || (!empty($id) && ($this->input->post('password') || $this->input->post('password_conf')))) {
      $this->form_validation->set_rules('password', 'パスワード', 'trim|required|min_length[8]|max_length[30]|matches[password_conf]');
      $this->form_validation->set_rules('password_conf', 'パスワード確認', 'trim|required|min_length[8]|max_length[30]');
    }
    //$this->form_validation->set_rules('email', 'メールアドレス', 'trim|required|valid_email|max_length[255]|callback__email_check');
    //$this->form_validation->set_rules('authority', '権限', 'trim|required|callback__authority_check');
  
    return $this->form_validation->run();

  }


  /**
   * 同じアカウントIDで登録がないかチェック
   */
  public function _loginaccount_check($val) {

    $result = $this->Admins_model->fetchOneByLoginAccount($val);
    if(!empty($result) && $result['id'] != $this->edit_id) {
      $this->form_validation->set_message('_loginaccount_check', 'このログインIDはすでに登録されています');
      return FALSE;
    }
    
    return TRUE;
  }

  /**
   * 同じメールアドレスで登録がないかチェック
   */
  public function _email_check($val) {

    $result = $this->Admins_model->fetchOneByEmail($val);
    if(!empty($result) && $result['id'] != $this->edit_id) {
      $this->form_validation->set_message('_email_check', 'このメールアドレスはすでに登録されています');
      return FALSE;
    }
    
    return TRUE;
  }

  /**
   * 権限の値が正しいかチェック
   */
  public function _authority_check($val) {

    //権限タイプリストを取得
    $authority_list = $this->auth->get_AuthorityData();

    if(empty($authority_list[$val])) {
      $this->form_validation->set_message('_authority_check', '権限の送信値が不正です');
      return FALSE;
    }
    
    return TRUE;
  }


}
