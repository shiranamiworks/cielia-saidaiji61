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
class  Adminslib {

  var $CI = NULL;

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
    $this->CI->load->model(array('Admins_model' , 'Admins_deleted_model'));
    $this->CI->load->helper(array('string','url' , 'common'));

  }

  public function accountDelete($delele_id) {

    //削除対象アカウントが作成した記事があれば、すべて1に変更
    //まず削除対象アカウントのlogin_id値を得る
    $t_login_id = '';
    $delete_user = $this->CI->Admins_model->find($delele_id);

    if(!empty($delete_user["login_id"])) {
      $t_login_id = $delete_user["login_id"];
      //次にアカウントID = 1のユーザーのlogin_idを得る
      $user_info1 = $this->CI->Admins_model->find(1);
      $login_id1 = '';
    
      if(isset($user_info1["login_id"])) {
        $login_id1 = $user_info1["login_id"];
      }

      if($login_id1) {

        //トランザクションスタート
        $this->CI->db->trans_start();

        //削除対象アカウントが作成した各モデルのデータのユーザーIDをすべて1に変更
        //各種データのモデルを読み込んでそれぞれで処理する
        $data_table_list = $this->CI->config->item('data_table_list' , 'config_myapp');
        if(!empty($data_table_list)) {
          foreach($data_table_list as $data_table_key) {
            $t_model_name = ucwords($data_table_key).'_data_model';
            $this->CI->load->model($data_table_key.'/'.$t_model_name , 't_data_model');
            $this->CI->t_data_model->change_user_id($t_login_id , $login_id1);
          }
        }
        //アカウント削除
        $this->CI->Admins_model->delete($delele_id);
        //削除されたアカウントはadmins_deletedテーブルに
        $db_dataset = [
          'login_id' => $delete_user["login_id"],
          'user_name' => $delete_user["user_name"],
          //'login_account' => $delete_user["login_account"],
          'authority' => $delete_user["authority"],
          'created' => fn_get_date()
        ];
        $this->CI->Admins_deleted_model->insert($db_dataset);

        $this->CI->db->trans_complete();
        
      }

    }

    return;
  }

  /**
   * アカウント一覧（削除済みも含めて）取得
   */
  public function getAccountList() {

    $account_list = array();
    $result = $this->CI->Admins_model->find_all();
    if(!empty($result)) {
      foreach($result as $value) {
        $value['deleted_flag'] = 0;
        $account_list[$value['login_id']] = $value;
      }
    }

    $result2 = $this->CI->Admins_deleted_model->find_all();
    if(!empty($result2)) {
      foreach($result2 as $value) {
        $value['deleted_flag'] = 1;
        $account_list[$value['login_id']] = $value;
      }
    }

    return $account_list;

  }

}
