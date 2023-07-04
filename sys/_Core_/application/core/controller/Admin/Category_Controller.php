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
require(dirname(__FILE__).'/Base_Controller.php');

class Category_Controller extends Base_Controller
{


    public function __construct() {
        parent::__construct();

        $this->_init();
    }

    private function _init() {

      define('ADMINPAGE_NAME' , 'カテゴリ管理 | '.(defined('ADMIN_BASE_TITLE') ? ADMIN_BASE_TITLE : $this->config->item('admin_title' , 'config_myapp')));
      
      $this->load->library('categorylib' , array(
        'category_model_name' => $this->dirname.'/'.$this->dirname.'_category_model' ,
        'data_model_name' => $this->dirname.'/'.$this->dirname.'_data_model' 
      ));


      $this->view_data['edit_url'] = INDEX_CONTROLLER_PATH;
      $this->view_data['delete_url'] = INDEX_CONTROLLER_PATH.'/delete';

      //権限チェック
      $this->login_id = $this->auth->IsSuccess(true , "manage_category");

    }


    public function index() {
      //登録データ受信
      if(!empty($this->input->post('name'))) {
          $validation = $this->_validation();

          if($validation) {
              $edit_id = $this->input->post('edit_id');
              //IDの有効性チェック
              if(!empty($edit_id)) {
                $editData = $this->categorylib->getData($edit_id);
                if(empty($editData)) {
                  //不正なID指定の場合
                  redirect(INDEX_CONTROLLER_PATH , 'location');
                }
              }

              if(empty($edit_id)) {
                  $edit_id = '';
                  //DB新規登録
                  $db_data = array(
                      'category_name' => $this->input->post('name'),
                      'sort_num'      => 0,
                      'created'       => fn_get_date(),
                      'modified'       => fn_get_date()
                  );

              } else {
                  //DB更新
                  $db_data = array(
                      'category_name' => $this->input->post('name'),
                      'modified'       => fn_get_date()
                  );
              }

              $result = $this->categorylib->replace($db_data , $edit_id);

              if(!empty($result)) {
                  $this->session->set_flashdata('success', true);
                  redirect(site_url(INDEX_CONTROLLER_PATH) , 'location');
              }
          }
      }

      //登録されているカテゴリデータを取得
      $category_list_data = $this->categorylib->get_list();

      $category_data_cnt = 0;
      if($category_list_data) $category_data_cnt = count($category_list_data);

      //記事データがカテゴリごとに何件あるか
      $cnt_category_by = $this->categorylib->getnum_category_by();

      //Viewに渡す
      $this->view_data['result'] = $category_list_data;
      $this->view_data['cnt_category_by'] = $cnt_category_by;


      return $this->load->custom_view($this->dirname , 'category/index' , $this->view_data);
      //$this->load->view('info/category/index' , $this->_view_esc($this->view_data));
    }


    public function delete() {
      $del_id = $this->input->post('del_id');
      if(!empty($del_id)) {
        $this->categorylib->delete($del_id);
      }

      redirect(INDEX_CONTROLLER_PATH , 'location');
    }



    private function _validation() {

      $this->load->library('form_validation');
      $this->form_validation->set_rules('name', 'カテゴリ名', 'trim|required|max_length[30]');
    
      return $this->form_validation->run();

    }

}
