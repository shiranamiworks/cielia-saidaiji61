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

class  Message {

	var $CI = NULL;

	/**
	* __construct
	*
	* @access public
	* @return void
	*/
	public function __construct()
	{
		$this->CI =& get_instance();
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

		$this->CI->load->helper(array('string','url','common'));

	}

  public function data_cnt_info($cnt , $page , $page_num , $output_num) {
    $str_num1 = (!$cnt)?0:($page-1)*$output_num+1;
    $str_num2 = ($page == $page_num) ? $cnt : $page*$output_num;
    if($cnt > 0){
      return $str_num1.' - '.$str_num2;
    } else {
      return '';
    }
  }
  public function data_cnt_info_admin($cnt , $page , $page_num , $output_num) {
    $str_num1 = (!$cnt)?0:($page-1)*$output_num+1;
    $str_num2 = ($page == $page_num) ? $cnt : $page*$output_num;
    if($cnt > 0){
      return "全". $cnt."件中 <strong>".$str_num1."〜".$str_num2."件</strong>を表示しています";
    } else {
      return "全 0件";
    }
  }

  public function error_authority() {
    return "この操作を行う権限がありません";
  }

  public function error_conflict($DATA_NAME = 'データ')
  {
      return "この".$DATA_NAME."の編集途中に別のアカウントユーザーが編集を行った可能性があるため、登録処理を中断しました。<br>お手数ですが、データを再読み込みした後、再度登録操作をお試しください。";
  }
}
