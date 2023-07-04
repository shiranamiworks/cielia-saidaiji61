<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
======================================================================
Project Name    : Mishima T.CMS  
 
Copyright © <2016> Teruhiko Mishima All rights reserved.
 
This source code or any portion thereof must not be  
reproduced or used in any manner whatsoever.
本ソースコードを無断で転用・転載することを禁じます。
======================================================================
*/
class  Categorylib {

	var $CI = NULL;

  var $data_model_name = 'Data_model';
  var $category_model_name = 'Category_model';

	/**
	* __construct
	*
	* @access public
	* @return void
	*/
	public function __construct($params = array())
	{
		$this->CI =& get_instance();
    if(!empty($params['data_model_name'])) {
      $this->data_model_name = $params['data_model_name'];
    }
    if(!empty($params['category_model_name'])) {
      $this->category_model_name = $params['category_model_name'];
    }

    $this->_set_config();
	}

	/**
	* _set_config
	*
	* @access protected
	* @return void
	*/
	protected function _set_config()
	{

		$this->CI->config->load('config_myapp' , true);
    $this->CI->load->model($this->data_model_name , 'Data_model');
    $this->CI->load->model($this->category_model_name , 'Category_model');
		$this->CI->load->helper(array('string','url' , 'common'));

	}
  public function getData($category_id) {
    return $this->CI->Category_model->find($category_id , 'category_id');
  }

  public function get_list() {
    return $this->CI->Category_model->find_all();
  }

  //記事データがカテゴリごとに何件あるか
  public function getnum_category_by() {
    return $this->CI->Data_model->getnum_category_by();
  }

  /**
   *
   * データのインサートもしくはアップデート処理
   * 公開権限があるユーザーならば通常どおりデータを挿入もしくはアップデート
   * 権限がなければ、仮保存処理
   *
   * @param object $data
   * @param string $login_id
   */
  public function replace($data , $category_id="") {
    
    $save_where = array();
    if(!empty($category_id)) {
      if(!is_num($category_id)) return false;

      $save_where = array('category_id' => $category_id);
    }
    return $this->CI->Category_model->replaceData($data , $save_where);
  }

  public function delete($category_id) {
    $this->CI->Category_model->delete($category_id , 'category_id');
    //カテゴリIDを変更
    $this->CI->Data_model->data_category_change($category_id , 1);
  }



}
