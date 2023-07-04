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

class Lists extends Base_Controller {

  public function __construct()
  {
    parent::__construct();
    $this->_init();
  }

  private function _init()
  {

      define('ADMINPAGE_NAME' , '物件一覧 | '.(defined('ADMIN_BASE_TITLE') ? ADMIN_BASE_TITLE : $this->config->item('admin_title' , 'config_myapp')));

      $this->load->model($this->dirname.'/'.$this->dirname.'_data_model', 'Data_model');
      $this->load->model($this->dirname.'/'.$this->dirname.'_metadata_model', 'MetaData_model');
      $this->load->model($this->dirname.'/'.$this->dirname.'_category_model', 'Category_model');

      $this->load->library('datalib' , array(
        'data_model_name' => $this->dirname.'/'.$this->dirname.'_data_model',
        'metadata_model_name' => $this->dirname.'/'.$this->dirname.'_metadata_model',
        'category_model_name' => $this->dirname.'/'.$this->dirname.'_category_model'
      ));

      $this->load->library(array('auth' , 'urlsetting' , 'message' , 'adminslib' , 'torikago'));

      //権限チェック
      $this->login_id = $this->auth->IsSuccess( true , "manage_rooms" );
  }


  public function index()
  {
    $this->config->load('config_torikago',true);
    /*
    $css = $this->config->item('torikago_table_css_classname' , 'config_torikago');
    pr($css);
    exit;
    */

    //データリスト取得
    $rs = $this->datalib->get_list(
      1 ,
      999
    );

    $data_list = array();
    if($rs) {
      foreach($rs as $val) {
        $data_list[$val['edit_id']] = $val;
      }
    }

    $this->view_data = array_merge($this->view_data , array(
      'page' => 1,
      'torikago_html' => $this->torikago->getTemplateHtmlAdmin($data_list)
    ));

    $this->load->view('rooms/lists' , $this->_view_esc($this->view_data));

    return;
  }

    public function tags ()
    {
        $this->load->model('Search_tag_model', 'SearchTagModel');
        $this->config->load('config_torikago',true);
        $Tags = $this->SearchTagModel->getTags();

        $data_list = array();

        $this->view_data = array_merge($this->view_data , array(
            'page' => 1,
            'torikago_html' => $this->torikago->getTemplateHtmlAdmin($data_list)
        ));

        $this->load->view('rooms/lists' , $this->_view_esc($this->view_data));

        return;
    }

}
