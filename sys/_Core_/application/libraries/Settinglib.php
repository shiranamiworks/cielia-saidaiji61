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

class  Settinglib {

	var $CI = NULL;
  
  /**  @var string  $authority  権限タイプ */
  var $authority;

  var $save_history = false; //履歴を保存するかどうか

  var $set_sort_num = false; //ソートNoを登録するか

	/**
	* __construct
	*
	* @access public
	* @return void
	*/
	public function __construct($params = array())
	{
		$this->CI =& get_instance();
    //ソートNoを登録する設定
    if(!empty($params['set_sort_num'])) {
      $this->set_sort_num = $params['set_sort_num'];
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
    $this->CI->load->model('setting/setting_data_model' , 'Setting_model');
    $this->CI->load->model('setting/setting_metadata_model' , 'Setting_metadata_model');
    $this->CI->load->library(array('auth' , 'form_validation' , 'message'));
		$this->CI->load->helper(array('string','url' , 'common'));


	}

  /**
   * 指定したDataIDのデータを取得（metaデータも含めて）
   * @param number $data_id
   */
  function getData($data_id)
  {

      $data = $this->CI->Setting_model->find($data_id);
      $meta_data = $this->CI->Setting_metadata_model->fetchAllbyDataID($data_id);
      
      if(!empty($meta_data))
      {
          foreach($meta_data as $meta)
          {
              $data["_meta_"][$meta["meta_key"]] = $meta["meta_value"];
          }
      }
      
      return $data;
  }


  function dataSave($data , $metadata) {

    //トランザクションスタート
    $this->CI->db->trans_start();

    $save_data_id = $this->replaceData($data);
    if(empty($save_data_id)) {
      return FALSE;
    }

    //メタデータの登録
    if(!empty($save_data_id) && !empty($metadata)) {
      $this->replaceMeta($save_data_id , $metadata);
    }

    $this->CI->db->trans_complete();

    if ($this->CI->db->trans_status() === FALSE)
    {
      return FALSE;
    }

    return $save_data_id;

  }

  /**
   *
   * データのインサートもしくはアップデート処理
   * 公開権限があるユーザーならば通常どおりデータを挿入もしくはアップデート
   * 権限がなければ、仮保存処理
   *
   * @param object $data
   */
  function replaceData($data , $id = 1)
  {
      
      $data['id'] = 1;
      $save_where = array('id' => $id);
      $data['modified'] = fn_get_date();

      $tid = $this->CI->Setting_model->replaceData($data , $save_where);

      return $tid; //挿入IDを返す 
  }


  /**
   * 指定したPostIDとメタフィールド名をKEYとしてメタデータを登録する
   * @param number $data_id
   * @param object $data_list 
   */
  function replaceMeta($data_id , $meta_data = array())
  {
      $db_data = array();
      //現在登録されているメタデータを取得
      $registered_data = $this->CI->Setting_metadata_model->fetchAllbyDataID($data_id);
      if(!empty($meta_data) && is_array($meta_data))
      {
          foreach($meta_data as $key => $meta)
          {
            $where = array();
            $db_data = array(
                "data_id" => $data_id,
                "meta_key" => $key,
                "meta_value" => $meta['value'],
                "meta_type" => (!empty($meta['type']) ? $meta['type'] : ''),
                "modified" => fn_get_date()
            );
            //もし同一のメタキーで登録があれば、そのメタIDで上書き更新
            if(isset($registered_data[$key])) {
              $where = array('meta_id' => $registered_data[$key]['meta_id']);
              unset($registered_data[$key]);
            } else {
              $db_data['created'] = fn_get_date();
            }

            $this->CI->Setting_metadata_model->replaceData($db_data , $where);
          }
      }

      //メタデータの過去分で余りがあれば削除対象となる
      if(!empty($registered_data)) {
        foreach($registered_data as $data) {
          $this->CI->Setting_metadata_model->delete($data['meta_id'] , 'meta_id');
        }
      }

      return;
  }

  function data_delete($data_id) {
    return $this->CI->Setting_model->delete($data_id);
  }

  function metadata_delete($data_id) {
    return $this->CI->Setting_metadata_model->delete(array("data_id"=>$data_id));
  }


  public function get_dataname($type) {
    return "設定";
  } 

}
