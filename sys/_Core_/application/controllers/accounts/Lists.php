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

  protected $NUM_LIST = 20;

	public function __construct()
	{
		parent::__construct();
		$this->_init();
	}

	private function _init()
	{

      define('ADMINPAGE_NAME' , 'アカウント一覧 | '.(defined('ADMIN_BASE_TITLE') ? ADMIN_BASE_TITLE : $this->config->item('admin_title' , 'config_myapp')));
      
      $this->load->model(array('Admins_model' , 'Admins_deleted_model'));
      $this->load->library(array('auth' , 'urlsetting' , 'message' , 'adminslib'));

      //権限チェック
      $this->login_id = $this->auth->IsSuccess( true , "manage_account" );
	}


	public function index() {

		//現在のページ
    if(!isset($_GET['page'])) $_GET['page'] = 1;
    $page = fn_valid_page($_GET['page']);
		//データ件数取得
		$cnt = $this->Admins_model->getDataCnt();
		//総ページ数取得
		$page_num = fn_getPages($cnt , $this->NUM_LIST);
		if($page > $page_num){ $page = $page_num; }

		//権限タイプリストを取得
    $authority_list = $this->auth->get_AuthorityData();
		$this->view_data['authority_list'] = $authority_list;

		//登録されている管理者アカウントリストを取得
		$data_list = $this->auth->getAccountList($page , $this->NUM_LIST);

		if(!empty($data_list)) {
			foreach($data_list as $data) {

        $data['delete'] = ( $data['id'] == 1 ? false : true ); //id=1のデータは削除不可
        $data['edit_link'] = site_url("accounts/edit/".$data["id"]."/".fn_urlprm($_GET));

				$this->view_data['result'][$data['id']] = $data;
			}
		}
		
		$this->view_data = array_merge($this->view_data , array(
			'page' => $page,
			'cnt' => $cnt,
			'page_num' => $page_num,
      //'html_pager_head' => $this->message->data_cnt_info($cnt , $page , $page_num , $this->NUM_LIST),
      'search_base_url' => site_url(INDEX_CONTROLLER_PATH),
			'delete_url'	=> site_url(INDEX_CONTROLLER_PATH.'/delete/'.fn_urlprm($_GET)),
		));

		//ページャ用
    $page_info = calc_pageinfo($cnt , $page , $this->NUM_LIST);

    //ページャリンク生成
    $this->view_data['pager_html'] = $this->_paging(site_url(INDEX_CONTROLLER_PATH), $page_info['page'] , $cnt , $this->NUM_LIST);
    $this->view_data['html_pager_head'] = $this->message->data_cnt_info($cnt , $page_info['page'] , $page_num ,$this->NUM_LIST); //データ件数まわりの表示

		$this->load->view('accounts/lists' , $this->_view_esc($this->view_data));

		return;
	}

	public function delete() {
    
    if($this->input->post("del_id") && is_num( $this->input->post("del_id") ) && $this->input->post("del_id") != 1) {

      $this->adminslib->accountDelete($this->input->post('del_id'));
    }

    redirect(site_url(INDEX_CONTROLLER_PATH."/".fn_urlprm($_GET)) , 'location');
	}

}
