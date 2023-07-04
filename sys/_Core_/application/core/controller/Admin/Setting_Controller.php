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
require(dirname(__FILE__).'/Edit_Controller.php');

class Setting_Controller extends Edit_Controller
{

    protected $editSetting = [];

    public function __construct() {
        parent::__construct(false);
        $this->_init();
    }

    private function _init() {

      define('ADMINPAGE_NAME' , 'サイト設定 | '.(defined('ADMIN_BASE_TITLE') ? ADMIN_BASE_TITLE : $this->config->item('admin_title' , 'config_myapp')));
      $this->load->library('settinglib');
      $this->load->library(array('auth' , 'urlsetting'));

      //権限チェック
      $this->login_id = $this->auth->IsSuccess(true , "manage_setting");

      $this->view_data['filelist_url'] = site_url($this->dirname.'/media/lists');
      $this->view_data['list_page_url'] = site_url($this->dirname.'/edit');
    }



    public function index($id = '') {

      $id = 1;
      $this->view_data['submit_url'] = site_url(INDEX_CONTROLLER_PATH);
      $this->view_data['cancel_url'] = site_url(INDEX_CONTROLLER_PATH);
      $this->view_data['preview_url'] = site_url($this->dirname.'/preview');
      $this->view_data['preview_site_url'] = $this->_clientsInfo('site_url').'top';
      $this->view_data['preview_client_base_url'] = $this->_clientsInfo('site_url');
      
      $this->view_data['edit_authority'] = true;
      $this->view_data['error'] = array();
      //編集モードの場合、そのデータをユーザーが編集可能かどうかをチェック
      if(!empty($id)) {
        $settingData = $this->settinglib->getData($id);

        if(empty($settingData)) {
          $this->_error_notfound();
          return;
        }
      }

      //データ送信があれば
      if($this->input->post('submit_data')) {

        $validation = $this->_validation();

        if(!$validation) {
          //バリデーションエラー
          $this->view_data['error'] = validation_errors();
          $settingData = $this->input->post();

        } else {  

          $db_dataset = $this->_db_dataset();
          
          if(!empty($id)) {
            //DB更新
            $db_dataset['data']['id'] = $id;
          }

          try{
            //DB登録処理
            $data_id = $this->settinglib->dataSave($db_dataset['data'] , $db_dataset['meta']);

            if($data_id) {
              //登録成功
              $this->session->set_flashdata('save_success', true);
              //記事の登録状態をチェック
              $dataChk = $this->settinglib->getData($data_id);

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

      $tab_setting = $this->_get_tabs_setting();
      $this->view_data['tab_html'] = $this->load->view('edit_form/parts/tab' , array('data' => $tab_setting) , true);
      if(count($tab_setting) > 1) {
        $this->view_data['tab_btm_html'] = $this->load->view('edit_form/parts/tab' , array('data' => $this->_get_tabs_setting() , 'tab_position' => 'bottom') , true);
      }

      if(empty($settingData)) {
        $settingData = array();
      }
      $this->view_data['content_html'] = $this->_convertDisplayHtml($settingData , $this->view_data['error']);
        
      $this->view_data['active_base_design'] = $settingData['_meta_']['design_base_type'];

      //読み込むjsファイル追加あれば
      if(!empty($this->editSetting['add_view']['js'])) {
        $this->view_data['add_js'] = $this->editSetting['add_view']['js'];
      }
      
      $this->load->custom_view($this->dirname , 'edit/index' , $this->view_data);


      return;

    }

    /*
    登録完了画面
    */
    public function complete() {

      if(empty($this->session->flashdata('save_success'))) {
        redirect($this->view_data['list_page_url'] , 'location');
      }

      redirect($this->view_data['list_page_url'].'?editcomp' , 'location');

      return;
    }

}
