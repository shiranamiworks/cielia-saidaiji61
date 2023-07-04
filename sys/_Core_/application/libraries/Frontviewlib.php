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
class  Frontviewlib {

	var $CI = NULL;
  var $setting;
  var $tmpl_setting;

	/**
	* __construct
	*
	* @access public
	* @return void
	*/
	public function __construct($params = array())
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
    $this->CI->load->library(array('form_validation' , 'message' , 'user_agent'));
		$this->CI->load->helper(array('string' , 'url' , 'common'));

	}

  public function set_view_dir($view_dir , $base_dir = '') {

    if(empty($base_dir)) {
      $base_dir = FCPATH;
    }
    $view_path = realpath($base_dir.'/'.$view_dir.'/');
    if(substr($view_path , -1) != '/') {
      $view_path .= '/';
    }
    $this->CI->load->view_path_override($view_path);
  }

  public function view($tmpl = 'index' , $param = array()) {

    $param['page_url'] = current_url();
    $param['ogp_image'] = '';

    return $this->CI->load->custom_view('' , $tmpl , $param);

  }

  private function _changeFilePath($data) {

    //プレビューモードの場合はパス変換しない
    if(defined('ADMINTOOL_FLG')) {
      return $data;
    }

    $str1 = '/uploads/cl_'.CLIENT_ID.'/';
    $str2 = '/uploads/';

    if (is_array($data)) {
      return array_map(array($this , '_changeFilePath'), $data);
    }

    if(!is_string($data)) {
      return $data;
    }

    return str_replace($str1 , $str2 , $data);

  }

  //画像ファイルを サムネイル用画像、またはスマホ用画像のパスに書き換える処理
  public function changeImagePath($filepath , $type = '') {
    $file_info = pathinfo($filepath);
    if(isset($file_info["extension"])){
      $file_ext = $file_info["extension"];
    }
    if(!empty($file_ext)) {
      if($type == 'thumb') {
        return str_replace(".".$file_ext , "_thumb".".".$file_ext , $filepath);
      }elseif($type == 'smp') {
        return str_replace(".".$file_ext , "_smp".".".$file_ext , $filepath);
      }else{
        //typeが空の場合、端末のユーザーエージェントをチェックしてスマホからのアクセスなら
        //スマホ最適化した画像パスを返す
        if($this->agent->is_mobile()) {
          return str_replace(".".$file_ext , "_smp".".".$file_ext , $filepath);
        }
      }
    }

    return $filepath;
  }

  public function stripJsCode($data) {

    if (is_array($data)) {
      return array_map(array($this , 'stripJsCode'), $data);
    }

    if(!is_string($data)) {
      return $data;
    }

    return fn_strip_jscode($data);
  }

  //form_groupタイプのメタデータがあればアンシリアライズして返す
  public function metadataToDisplay($metadata) {
    $meta_value = $metadata["meta_value"];
    if(!empty($metadata['meta_type']) && $metadata['meta_type'] == 'form_group') {
      $meta_value= unserialize_base64_decode($meta_value);
    }
    return $meta_value;
  }

  //form_groupタイプのデータでblock_numberを取得し、そのnumberのKEYを持つデータだけをまとめて返す
  public function fetchFromGroupValues($data) {
    $return = array();
    if(!empty($data['block_number'])) {
      foreach($data['block_number'] as $num) {
        if(isset($data[$num])) {
          $return[$num] = $data[$num];
        }
      }
      return $return;
    }

    return $data;
  }

}
