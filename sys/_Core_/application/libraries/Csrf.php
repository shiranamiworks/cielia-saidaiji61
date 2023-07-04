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
class  Csrf {

	var $CI = NULL;
	var $token_seed = '';
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
		$this->token_seed = $this->CI->config->item('encryption_key')."524";
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
		
		$this->CI->load->helper(array('string','url' , 'common'));

	}

	public function csrf_token_generate() {
        if(session_id())
        {
            return sha1($this->token_seed.session_id());
        }
        
        return "1";
    }

	public function csrf_check($token) {
        
        $token0 = $this->csrf_token_generate();
        if(preg_match("/^([a-zA-Z0-9])+$/i", $token) && $token0 && $token0 === $token)
        {
            return true;
        }
        
        return false;
    }
    

}
