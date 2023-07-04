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

class  Urlsetting {

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
    $this->CI->config->load('config_urlsetting' , true);

		$this->CI->load->helper(array('string','url','common'));

	}


  /**
   * フロントエンド側のURLを取得する
   * @param string $type (info_detail ,event_detail ...)
   * @param number $id
   * @param number $category
   * @param string $device(smph , mobi)
   * @return string 
   */
  function get_pageurl($type  , $id , $category , $device = "")
  {

    $url_setting = $this->CI->config->item('url_setting' , 'config_urlsetting');

    if(!empty($url_setting[$type]))
    {
      $replace_pattern = array('/\[id\]/','/\[category\]/','/\[device\]/');
      return preg_replace($replace_pattern,array($id , $category , $device),$url_setting[$type]);
    }

    return "";
  }
}
