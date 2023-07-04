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

class Publish_Controller extends Base_Controller
{

    protected $publish_str = '公開';

    public function __construct() {
        parent::__construct();
        $this->_init();
    }

    private function _init() {

      define('PUBLISH_STR' , $this->publish_str);
      define('ADMINPAGE_NAME' , PUBLISH_STR.'切り替え | '.(defined('ADMIN_BASE_TITLE') ? ADMIN_BASE_TITLE : $this->config->item('admin_title' , 'config_myapp')));
      

      $this->load->model($this->dirname.'/'.$this->dirname.'_data_model', 'Data_model');
      $this->load->model($this->dirname.'/'.$this->dirname.'_metadata_model', 'MetaData_model');
      $this->load->model($this->dirname.'/'.$this->dirname.'_category_model', 'Category_model');

      $this->load->library('datalib' , array(
        'data_model_name' => $this->dirname.'/'.$this->dirname.'_data_model',
        'metadata_model_name' => $this->dirname.'/'.$this->dirname.'_metadata_model',
        'category_model_name' => $this->dirname.'/'.$this->dirname.'_category_model'
      ));
      $this->load->library(array('auth' , 'urlsetting'));

      $this->view_data['cancel_page'] = site_url($this->dirname.'/lists'.fn_urlprm($_GET));

      //権限チェック
      $this->login_id = $this->auth->IsSuccess(true , "publish_post");

    }


    public function blank() {
      $this->load->custom_view($this->dirname , 'publish/blank' , $this->view_data);
      return;
    }

    public function index($data_id) {

      $data = $this->datalib->getData($data_id);
      if(empty($data)) { die("Error(Not Found)"); }
      $this->view_data['data'] = $data;

      //公開、非公開の処理実行用URL
      $this->view_data["publish_link"] = site_url($this->dirname."/publish/do_publish/".$data["id"]."/".fn_urlprm($_GET));
      $this->load->custom_view($this->dirname , 'publish/confirm' , $this->view_data);

      return;
    }

    public function do_publish($data_id) {

      //直接URLアクセスを防ぐ対策
      $this->_checkPost();

      $data = $this->datalib->getData($data_id);
      if(empty($data)) { die("Error(Not Found)"); }
      $this->view_data['data'] = $data;

      //権限チェック(公開権限があるか）
      if($this->auth->chk_controll_limit("publish_post")) {
          //さらにこの記事がstatus情報を持たない（承認待ち状態でない）状態か
          if(!$data["status"]) {
              $this->datalib->change_publish($data["id"] , $this->login_id);
              //コンプリート画面へ
              redirect(site_url($this->dirname."/publish/complete/".$data["id"]."/".fn_urlprm($_GET)));
              
          } else {
              $err = "この".$this->config->item()."は承認待ち状態の可能性があります。";
          }

      } else {
          //権限なし エラー表示
          $err = "この操作をおこなう権限がありません。";
      }
      
      if(!empty($err)) {
          $this->view_data['error'] = $err;
          $this->load->custom_view($this->dirname , 'publish/error' , $this->view_data);

          return;
      }
    }

    public function app($data_id) {

      //直接URLアクセスを防ぐ対策
      $this->_checkPost();
      
      $data = $this->datalib->getData($data_id);
      $this->view_data['data'] = $data;
      if(empty($data)) { die("Error(Not Found)"); }

      //権限チェック(公開権限があるか）
      if($this->auth->chk_controll_limit("publish_post")) {
          
          //この記事がstatus情報を持つ（承認待ち状態か）かどうか
          if(!$data["status"]) {
              $err = "この".DATA_NAME."は承認待ちの状態ではないか、権限がない可能性があります。";
          } else {
              if(isset($_POST["message"]) && (isset($_POST["app"]) || isset($_POST["back"]))) {
                  
                  // -------------------
                  // 承認実行
                  // -------------------
                  if($_POST["app"]) {
                      $this->datalib->app_publish($data["id"] , $this->login_id);
                      //コンプリート画面へ
                      redirect(site_url($this->dirname."/publish/complete/".$data["id"]."/".fn_urlprm($_GET)));
                          
                  }
                  // -------------------
                  // 差戻し実行
                  // -------------------
                  else if($_POST["back"]) {
                      //この記事がstatus情報を持つ（承認待ち状態か）かどうか
                      if($data["status"]) {
                        $message = "";
                        if(!empty($_POST["message"])) {
                            $message = $_POST["message"];
                        }
                        $this->datalib->app_back($data["id"] , $this->login_id , $message);

                        //コンプリート画面へ
                        redirect(site_url($this->dirname."/publish/complete/".$data["id"]."/".fn_urlprm($_GET)));
                      }
                  }
              }
          }
          
          if(empty($err)) {
              $this->view_data["publish_link"] = site_url($this->dirname."/publish/app/".$data["id"]."/".fn_urlprm($_GET));
              $this->load->custom_view($this->dirname , "publish/approval" , $this->view_data);
          }
      } else {
          //権限なし エラー表示
          $err = "この操作をおこなう権限がありません。";
      }
      
      if(!empty($err)) {
          $this->view_data['error'] = $err;
          $this->load->custom_view($this->dirname , 'publish/error' , $this->view_data);

          return;
      }
    }

    public function complete($data_id) {
      $data = $this->datalib->getData($data_id);
      if(empty($data)) { die("Error(Not Found)"); }
      $this->view_data['data'] = $data;
      $this->load->custom_view($this->dirname , "publish/complete" , $this->view_data);
      return;
    }
}
