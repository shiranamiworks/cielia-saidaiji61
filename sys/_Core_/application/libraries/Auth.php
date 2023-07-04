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
class  Auth {

	var $CI = NULL;

  /** @var object  $authority  権限設定 */
  private $authority = array(

      "MASTER" => array(
          "name" => "オーナー",
          "level" => 1
      ),
      "ADMIN" => array(
          "name" => "管理者",
          "level" => 2
      ),
      "EDITOR" => array(
          "name" => "投稿者",
          "level" => 3
      )
  );

  /**
   * @var object $controll_setting 権限別に許可される操作設定
   * ・manage_account アカウント管理画面での操作,
   * ・manage_setting 設定画面での操作
   * ・manage_category カテゴリ管理画面での操作
   * ・edit_post 記事作成・編集
   * ・publish_post 記事公開
   * ・upload_files ファイルのアップロード
   * ・manage_user 会員管理画面での操作
   * ・manage_shop 店舗管理画面での操作(操作権限はあっても、publish_postが有効でなければ公開はできない)
   * ・manage_shop_csv 店舗管理画面のCSVインポート・エクスポート操作
   * ・manage_campaign キャンペーン管理画面での操作
   * ・manage_campaign_app キャンペーン応募者管理画面での操作
   * ・manage_banner バナー管理画面での操作(操作権限はあっても、publish_postが有効でなければ公開はできない)
   * ・manage_push プッシュ配信管理画面での操作(操作権限はあっても、publish_postが有効でなければ公開はできない)
   *
   */
  private $controll_setting = array(
      "1" => array(
          "manage_account"    => 1,
          "manage_setting"    => 1,
          "manage_category"   => 1,
          "publish_post"      => 1,
          "delete_post"       => 1,
          "sort_change_post"  => 1,
          "upload_files"      => 1,
          "delete_files"      => 1,
          "manage_user"       => 1,
          "manage_info"       => 1,
          "manage_rooms"      => 1
      ),

      "2" => array(
          "manage_account"    => 0,
          "manage_setting"    => 0,
          "manage_category"   => 1,
          "publish_post"      => 1,
          "delete_post"       => 1,
          "sort_change_post"  => 1,
          "upload_files"      => 1,
          "delete_files"      => 1,
          "manage_user"       => 1,
          "manage_info"       => 1,
          "manage_rooms"      => 1
      ),

      "3" => array(
          "manage_account"    => 0,
          "manage_setting"    => 0,
          "manage_category"   => 0,
          "publish_post"      => 0,
          "delete_post"       => 0,
          "sort_change_post"  => 0,
          "upload_files"      => 1,
          "delete_files"      => 0,
          "manage_user"       => 1,
          "manage_info"       => 1,
          "manage_rooms"      => 1
      ),

  );

  private $app_sendmail_flag = true; //承認待ち、差戻し、承認・公開のタイミングでメール送信するか

  //承認待ち、差戻し、承認・公開時のメール送信設定
  //それぞれarrayを空にすると送信されなくなる
  private $app_sendmail_setting = array(
      "add_wait" => array("MASTER" , "ADMIN"),
      "wait_publish" => array("EDITOR"),//実際はEDITORの中の記事作成担当者のみに送信
      "wait_back" => array("EDITOR") //実際はEDITORの中の記事作成担当者のみに送信
  );

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

    $this->CI->load->model(array('Admins_model'));
    $this->CI->load->library(array('session' , 'form_validation'));
		$this->CI->load->helper(array('string','url' , 'common'));


	}


    /**
    *  認証セッションへセット
    *
    *  @param string  $login_id
    *  @param string  $cert_string
    *  @param string  $authority
    *  @return boolean
    */
  public function setLoginSession($login_id ,  $user_name ,$str,$authority="MASTER" , $client_id) {

    $this->CI->session->set_userdata(array(
      'login_id'  => $login_id,
      'user_name' => $user_name,
      'ip'        => $this->CI->input->ip_address(),
      'cert'      => $str,
      'authority' => $authority,
      'client_id' => $client_id
    ));

    return;

  }


  /**
  *  認証成功の判定（ユーザーの権限をチェックし、操作不可ならリダイレクト）
  *  @param boolean $redirect (認証エラーの場合そのままリダイレクトするか)
  *  @param string $controller_name
  *  @return boolean
  */
  public function isSuccess($redirect = true , $controller_name = "" , $logout_redirect_viewfile = "") {

    if(!empty($this->CI->session->userdata('cert')) && $this->CI->session->userdata('cert') == $this->CI->config->item('CERT_STRING') && !empty($this->CI->session->userdata('login_id'))) {

          //権限チェック
          if($controller_name && !$this->chk_controll_limit($controller_name)) {
              if($redirect) {
                  $this->err_authority(); //権限なし main pageにリダイレクト
              }

              return false;
          }

          return $this->CI->session->userdata('login_id');

    }

    if($redirect) {
        $this->CI->session->set_flashdata('logout_ref' , $this->CI->uri->uri_string());
        redirect(site_url('login') , 'location');
    }

    if(!empty($logout_redirect_viewfile)) {
      $this->CI->load->view($logout_redirect_viewfile);
      return;
    }

    return false;
  }



  /** 関連セッションのみ破棄する。 */
  public function logout() {
    $this->CI->session->sess_destroy();
    return;
  }

  /**
   *  アカウントリストを取得
   *
   *  @param  number $num //取得件数
   *  @return object
   */
  public function getAccountList($page , $num=10) {

      return $this->CI->Admins_model->getList($page , $num);

  }

  /**
  *  login_accountとemailからアカウント存在チェックし
  *  存在すればパスワードを再発行する
  *  @param string $login_account
  *  @param string $email
  *
  *  @return boolean
  */
  public function passRemind($login_account , $email) {

      //check
      $tid = $this->CI->Admins_model->checkAccount($login_account , $email);

      if($tid)
      {

          $t_admin_data = $this->CI->Admins_model->find($tid);
          if(!empty($t_admin_data)) {
            //仮パスワードを発行
            $pass0 = substr(uniq() , 0 , 8);
            $pass = $this->makeEncryptedPassword($pass0 , $t_admin_data['created']);

            $updata = array(
                "password" => $pass,
                "modified" => fn_get_date()
            );

            $result = $this->CI->Admins_model->updateOne($updata , array('id' => $tid));

            if($result)
            {
                //メール本文作成
                $mail_message = array(
                    "login_account" => $login_account,
                    "password" => $pass0
                );
                $mailSendMessage = $this->load->view('email/remind/forget_message' , $mail_message , true);

                //メール送信
                $this->load->library(array('mailer'));
                $ret = $this->mailer->to($this->config->item('EMAIL_TO_ADMIN','config_myapp'))
                      ->subject('パスワード再発行のお知らせ')
                      ->message($mailSendMessage)
                      ->from($this->config->item('EMAIL_FROM','config_myapp'), $this->config->item('EMAIL_FROM_NAME','config_myapp'))
                      ->send();

                return true;
            }
          }

      }

      return false;
  }


  /**
  * 権限設定データを返す（プルダウン選択用にデータを加工して）
  *
  * @return object
  */
  public function get_AuthorityData() {
    $return_obj = array();
    foreach($this->authority as $key => $val) {
        $return_obj[$key] = $val["name"];
    }

    return $return_obj;
  }

  public function getAccountName($login_id) {
      $data = $this->CI->Admins_model->fetchOneByLoginID($login_id);

      if(!empty($data) && isset($data["user_name"])){
          return $data["user_name"];
      }

      return " － ";
  }

  /**
   * アカウントKEY名を渡してそのアカウントの権限レベルを取得する
   * @return number
   */
  public function get_AccountLevel(){

      return $this->get_authority("level");

  }

  /**
   * 操作権限があるかをチェック
   * @param mixed $action (配列で複数指定もできる manage_XXX, edit_XXX と複数指定した場合、どちらも1なら1となる)
   * @param string $login_id //指定しなければSESSIONから取得
   * @return boolean
   */
  public function chk_controll_limit($action , $login_id = "") {

      $authority_str = "";
      if($login_id) {
          $rs = $this->CI->Admins_model->fetchOneByLoginID($login_id);
          if(!empty($rs) && isset($rs["authority"])) {
              $authority_str = $rs["authority"];
          }
      }

      $level = $this->get_authority("level" , $authority_str);

      if($level) {

          if(is_array($action)) {
              $permit = 1;
              foreach($action as $action_name) {
                  if(isset($this->controll_setting[$level][$action_name])) {
                      $permit = $permit*$this->controll_setting[$level][$action_name];
                  } else {
                      //action_nameが$controll_setting上に存在しない場合は 1とする
                  }
              }
              return $permit;
          }

          if(isset($this->controll_setting[$level][$action])) {
              return $this->controll_setting[$level][$action];
          }
          return 1;

      }

      return 0;
  }

  /**
   * 対象の記事データは自身が作成した記事かどうか
   * @param string $model_name
   * @param number $data_id
   * @param string $login_id //指定しなければSESSIONから取得
   */
  public function chk_my_edit_post($model_name , $data_id , $login_id = "")
  {
      if(!$login_id && !empty($this->CI->session->userdata("login_id"))) {
        $login_id = $this->CI->session->userdata("login_id");
      }

      $this->CI->load->model($model_name , 'targetDataModel');
      $data = $this->CI->targetDataModel->find($data_id);

      if($data && isset($data["user_id"]) && $data["user_id"] == $login_id) {
          return true;
      }

      return false;

  }

  /**
   * 権限エラーでリダイレクト処理
   */
  public function err_authority() {
      redirect($this->CI->config->item('admin_mainpage' , 'config_myapp') , 'location');
  }


  /**
   * 権限名、もしくは権限レベル値を取得
   * @return string $key (name or level)
   * @param string $$authority_str
   */
  public function get_authority($key = "" , $authority_str = "") {

      if(!$authority_str && !empty($this->CI->session->userdata("authority"))) {
          $authority_str = $this->CI->session->userdata("authority");
      }

      if($authority_str && isset($this->authority[$authority_str])) {
          if(isset($this->authority[$authority_str][$key])) {
              return $this->authority[$authority_str][$key];
          } else {
             return $this->CI->session->userdata("authority");
          }
      }

      return "";
  }

  /**
   * 承認待ち、差戻し、承認・公開のそれぞれのタイミングでメール送信する対象者を取得
   * @param string $action
   * @return array
   */
  public function get_sendmail_target($action)
  {

      $target = array();
      if($this->app_sendmail_flag) //メール送信を許可しているかどうかをまずチェック
      {

          if(is_array($this->app_sendmail_setting[$action]) && count($this->app_sendmail_setting[$action]))
          {
              foreach($this->app_sendmail_setting[$action] as $authority)
              {
                  $result = $this->CI->Admins_model->fetchAllbyAuthority($authority);
                  if($result) $target = array_merge($target , $result);
              }
          }
      }
      return $target;

  }

  /**
   * セッションIDを返す
   * @return string
   */
    public function get_session_id() {
        return session_id();
    }


    public function makeEncryptedPassword($password , $registered_date) {
      return sha1(sha1($this->CI->config->item('CERT_LOGIN_KEY').$password).$registered_date);
    }


}
