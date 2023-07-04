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
class  Datalib {

	var $CI = NULL;
  
  /**  @var string  $authority  権限タイプ */
  var $authority;

  var $save_history = false; //履歴を保存するかどうか

  var $data_model_name = 'Data_model';
  var $metadata_model_name = 'Metadata_model';
  var $category_model_name = 'Category_model';

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
    if(!empty($params['data_model_name'])) {
      $this->data_model_name = $params['data_model_name'];
    }
    if(!empty($params['metadata_model_name'])) {
      $this->metadata_model_name = $params['metadata_model_name'];
    }
    if(!empty($params['category_model_name'])) {
      $this->category_model_name = $params['category_model_name'];
    }

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
    $this->CI->load->model(array('Admins_model'));
    $this->CI->load->model($this->data_model_name , 'Data_model');
    $this->CI->load->model($this->metadata_model_name , 'MetaData_model');
    $this->CI->load->model($this->category_model_name , 'Category_model');
    $this->CI->load->library(array('auth' , 'form_validation' , 'message'));
		$this->CI->load->helper(array('string','url' , 'common'));


	}

  /**
   * 指定された絞り込み条件をURLに付加させるパラメータに変換
   * @param object $search_param
   * @return string
   */
  function get_searchlink_param($search_param)
  {
      $searchlink = "";

      if(is_array($search_param) && count($search_param))
      {
          foreach($search_param as $key => $val)
          {
              if($searchlink) $searchlink .= "&";
              $searchlink .= $key."=".$val;
          }
      }

      return $searchlink;
  }

  /**
   * 記事の公開切り替え
   *
   * @param number $id
   * @param srting $user_id //公開、非公開実行者
   */
  function change_publish($id , $user_id)
  {

      $result = 0;

      //公開権限があるか
      if($this->CI->auth->chk_controll_limit("publish_post"))
      {
          if(is_num($id) && $this->CI->Data_model->getDataCnt(array('id' => $id)))
          {
              $rs = $this->CI->Data_model->find($id);
              if($rs && isset($rs["status"]) && !$rs["status"]) //statusにwaitもbackもない状態なら公開切り替えする
              {
                  $output_flag = $rs["output_flag"];

                  $updata = array();

                  $action = "";
                  if($output_flag == 1){
                      $updata["output_flag"] = 0;
                      $updata["last_edit_user_id"] = $user_id;
                      $action = "非公開化";
                  }else{
                      $updata["output_flag"] = 1;
                      $updata["last_edit_user_id"] = $user_id;
                      $updata["modified"] = fn_get_date();
                      $action = "公開化";
                  }

                  $updata["id"] = $id;
                  $update_where = array("id" => $id);
                  //historyをアップデート
                  $updata["history"] = $this->update_history($id, "", $user_id, $action , fn_get_date()); //hisotry

                  $result = $this->CI->Data_model->replaceData($updata , $update_where);

              }
          }
      }
      return $result;
  }


  /**
   * 承認待ちの記事を公開する
   *
   * @param number $data_id
   * @param srting $user_id //承認実行者
   */
  function app_publish($data_id , $user_id)
  {

      $result = 0;

      //公開権限があるか
      if($this->CI->auth->chk_controll_limit("publish_post"))
      {

          $rs = $this->CI->Data_model->find($data_id);

          if($rs && isset($rs["status"]) && $rs["status"])
          {

              //parent_data_idに値があれば（入れ替えパターン。例えば、公開中の記事を別の内容に差し替えたい
              //場合、新規作成したのち、公開と同時に入れ替え対象の記事と入れ替えをおこなう。とりあえず入れ替えする
              //処理だけ実装
              if(isset($rs["parent_data_id"]) && $rs["parent_data_id"])
              {

                  /* 利用しないのでとりあえずコメントアウト
                  $data = $rs;

                  //もとの記事データと入れ替え
                  $data["id"] = $rs["parent_data_id"];
                  $data["output_flag"] = 1;
                  $data["status"] = "";
                  $data["parent_data_id"] = 0;
                  $data["history"] = $this->update_history($data["id"], "", $user_id, "承認" , fn_get_date());
                  //$data["modified"] = fn_get_date();
                  $result = $this->LACNE->model["post"]->replace($data , "id");

                  //入れ替えしたあと、記事データを削除
                  $this->LACNE->model["post"]->delete($data_id);

                  //メタデータも入れ替え
                  $this->LACNE->model["postmeta"]->delete($rs["parent_data_id"]);

                  $meta_updata = array(
                      "data_id" => $rs["parent_data_id"]
                  );
                  $this->LACNE->model["postmeta"]->change_data_id($data_id , $rs["parent_data_id"]);
                  */

              }
              //通常の承認→公開パターン
              //output_flag値を1にし、statusを空にするだけ
              else
              {
                  $updata = array(
                      "output_flag" => 1,
                      "status" => "",
                      "last_edit_user_id" => $user_id,
                      "history" => $this->update_history($rs["id"], "", $user_id, "承認" , fn_get_date()),
                      "modified" => fn_get_date()
                  );

                  $result = $this->CI->Data_model->replaceData($updata , array("id" => $rs['id']));

                  //記事作成者あてに承認された旨をメール送信
                  //メール送信をおこなう設定になっているかチェック
                  $sendmail_check = $this->CI->auth->get_sendmail_target("wait_publish");
                  if($sendmail_check)
                  {
                      //メール送信先（記事を作成した投稿者宛て）
                      if(isset($rs["user_id"]) && $rs["user_id"])
                      {
                          $editor_info = $this->CI->Admins->fetchOneByLoginID($rs["user_id"]);

                          //承認を行った管理者名
                          $admin_user_info = $this->CI->Admins->fetchOneByLoginID($user_id);

                          if($editor_info)
                          {
                              //メール本文データ
                              $mail_data = array(
                                  "editor_name" => $editor_info["user_name"],
                                  "title" => $rs["title"],
                                  "modified" => $rs["modified"],
                                  "admin_user_name" => (isset($admin_user_info["user_name"]))?$admin_user_info["user_name"]:""
                              );

                              $this->app_action_sendmail(array($editor_info["email"]), "app_ok_message", "（".LACNE_APP_ADMIN_PAGENAME."）承認待ちとなっていた記事が承認されました", $mail_data , $admin_user_info["email"]);
                          }
                      }
                  }
                  //メール送信ここまで

              }
          }
      }

      return $result;
  }

  /**
   * 承認待ちの記事を差し戻す
   *
   * @param number $data_id
   * @param srting $user_id //差戻し実行者
   * @param string $message
   */
  function app_back($data_id , $user_id , $message)
  {

      $result = 0;

      //公開権限があるか
      if($this->CI->auth->chk_controll_limit("publish_post"))
      {

          $rs = $this->CI->Data_model->find($data_id);

          if($rs && isset($rs["status"]) && $rs["status"])
          {
              $updata = array(
                  "output_flag" => 0,
                  "status" => "back",
                  "history" => $this->update_history($data_id, "back", $user_id, "差戻し" , fn_get_date()),
                  //"modified" => fn_get_date()
              );

              $result = $this->CI->Data_model->replaceData($updata , array("id" => $data_id));

              //記事作成者あてに差戻しされた旨をメール送信
              //メール送信をおこなう設定になっているかチェック
              $sendmail_check = $this->CI->auth->get_sendmail_target("wait_back");
              if($sendmail_check)
              {
                  //メール送信先（記事を作成した投稿者宛て）
                  if(isset($rs["user_id"]) && $rs["user_id"])
                  {
                      $editor_info = $this->CI->Admins->fetchOneByLoginID($rs["user_id"]);

                      //差戻しを行った管理者名
                      $admin_user_info = $this->CI->Admins->fetchOneByLoginID($user_id);

                      if($editor_info)
                      {
                          //メール本文データ
                          $mail_data = array(
                              "editor_name" => $editor_info["user_name"],
                              "title" => $rs["title"],
                              "modified" => $rs["modified"],
                              "admin_user_name" => (isset($admin_user_info["user_name"]))?$admin_user_info["user_name"]:"",
                              "message" => $message
                          );

                          $this->app_action_sendmail(array($editor_info["email"]), "app_ng_message", "（".LACNE_APP_ADMIN_PAGENAME."）承認待ちとなっていた記事が差戻しされました", $mail_data , $admin_user_info["email"]);
                      }
                  }
              }
              //メール送信ここまで
          }
      }
      return $result;
  }


  /**
   * historyを抽出（配列形式にする）
   * @param number $data_id
   * @return object
   */
  function get_history($data_id)
  {
      if(!$this->save_history) //historyの記録が無効なら空を返す
      {
          return array();
      }

      $data = $this->CI->Data_model->find($data_id);

      if($data && isset($data["history"]))
      {

          $history = $data["history"];
          //historyはシリアライズされている
          //return unserialize($history);
          return unserialize_base64_decode($history);
      }

      return array();
  }

  /**
   *
   * historyデータを取得・追記して返す
   *
   * @param number $data_id
   * @param string $status
   * @param number $user_id
   * @param string $message(差戻し時)
   * @param datetime $modified
   * @return string
   */
  function update_history($data_id , $status , $user_id , $message , $modified)
  {

      if(!$this->save_history) //historyの記録が無効なら空を返す
      {
          return "";
      }

      $add_history = array(
          "status"  =>$status ,
          "user_id" =>$user_id ,
          "message" =>$message ,
          "modified" =>$modified
      );

      $history = $this->get_history($data_id);
      if(empty($history)) $history = array();
      array_push($history , $add_history);
      
      //return serialize($history);
      return serialize_base64_encode($history);

  }


  /**
   *
   * historyデータを新規作成
   *
   * @param string $status
   * @param number $user_id
   * @param string $message(差戻し時)
   * @param datetime $modified
   * @return string
   */
  function create_new_history($status , $user_id , $message , $modified)
  {
      if($this->save_history) //historyの記録が有効なら
      {
          //return serialize(array(array("status"=>$status , "user_id"=>$user_id, "message"=>$message , "modified"=>$modified))); //hisotry
          return serialize_base64_encode((array(array("status"=>$status , "user_id"=>$user_id, "message"=>$message , "modified"=>$modified)))); //hisotry
      }

      return "";
  }

  /**
   * 一覧中の「公開 / 非公開」または「承認・公開」メニュー表示
   * @param object $data
   */
  function publish_menu($data , $publish_link = "")
  {
      $return = "";

      $menu_str = "";
      $menu_html_str = "";
      $waiting = 0;
      //対象記事が承認待ちもしくは差戻しの状態かどうか
      if(isset($data["status"]) && ($data["status"] == "wait" || $data["status"] == "back"))
      {
          $publish_link = str_replace("[link_type]" , "app" , $publish_link);
          if($data["status"] == "wait")
          {
              $menu_str = "<div class='btn primary'><i class='icon-user'></i> 承認待ち</div>";
          }
          else
          {
              $menu_str = "<div class='btn info'><i class='icon-retweet'></i> 差戻し</div>";
          }
          $menu_html_str = '<a href="javascript:void(0)" style="cursor:pointer" data-link="'.$publish_link.'"  class="link_waiting" data-id="'.$data["id"].'" id="waiting_'.$data["id"].'">';
          $menu_html_str_close = '</a>';
          $waiting = 1;
      }
      else if(isset($data["output_flag"]))
      {
          $publish_link = str_replace("[link_type]" , "index" , $publish_link);
          if($data["output_flag"] == 1)
          {
              $menu_str = '<div class="slider-frame primary"><span data-on-text="ON" data-off-text="OFF" class="slider-button on">ON</span></div>';
          }
          else
          {
              $menu_str = '<div class="slider-frame"><span data-on-text="ON" data-off-text="OFF" class="slider-button">OFF</span></div>';
          }
          $menu_html_str = '<span style="cursor:pointer" data-link="'.$publish_link.'" class="link_publish" data-id="'.$data["id"].'" id="publish_'.$data["id"].'">';
          $menu_html_str_close = '</span>';

      }


      //公開権限があるか
      if($this->CI->auth->chk_controll_limit("publish_post"))
      {
          $return = $menu_html_str.$menu_str.$menu_html_str_close;
      }
      else
      {
          $return = $menu_str;
      }


      return $return;

  }


  function app_action_sendmail($send_address_arr , $template_filename , $mail_title , $mail_body , $from = MAIL_SETTING_NOTICE_FROM)
  {
      
      $mail_body = $this->CI->load->view($template_filename , $mail_body , true);

      //メール送信
      $this->CI->load->library(array('mailer'));
      $this->CI->mailer->subject($mail_title);
      $this->CI->mailer->text($mail_body);
      $this->CI->mailer->from($from);

      //送信先アドレスを1件取得し、2件目以降はCCにする
      $send_to = array_shift($send_address_arr);
      $this->CI->mailer->to($send_to);
      $send_to_cc = array();
      if(!empty($send_address_arr))
      {
          foreach($send_address_arr as $send_address)
          {
              $send_to_cc[] = array($send_address);
          }
          $this->CI->mailer->cc($send_to_cc);
      }

      //メール送信
      return $this->CI->mailer->send();
      
  }


  function get_data_cnt($where = array() , $like = array()) {
    return $this->CI->Data_model->getDataCnt($where , $like);
  }

  function get_data_cnt_waiting() {
    return $this->CI->Data_model->data_cnt_waiting();
  }

  function get_list($page , $limit = 10 , $where = array() , $like = array() , $order_key = 'distribution_start_date' , $order_type = 'DESC') {
    return $this->CI->Data_model->getList($page , $limit , $where , $like , $order_key , $order_type);
  }

  /**
   * 指定したDataIDのデータを取得（metaデータも含めて）
   * @param number $data_id
   * @param bool $frontview フロント表示するデータかどうか
   */
  function getData($data_id , $frontview=false)
  {

      //プレビューモードの場合
      if(!empty($_GET['mode']) && $_GET['mode'] == 'preview' && $this->CI->session->flashdata('preview_mode')) {
        $this->CI->load->library('session');
        $preview_data = $this->CI->session->flashdata('preview_data');
        $this->CI->session->unset_userdata('preview_data');
        if(!empty($preview_data)) {
          return $preview_data;
        }
      }

      $data = array();
      if(!is_num($data_id)) {
        return array();
      }

      $where = "id = ".(int)$data_id;

      //フロント表示するデータのみ抽出する条件を追加
      if($frontview === true) {
        $this->CI->load->library('frontviewlib');

        $time = fn_get_date();
        $where .= " AND output_flag = 1 AND distribution_start_date <= '".$time."' AND (distribution_end_date = '0000-00-00 00:00:00' OR distribution_end_date >= '".$time."')";
      }

      //$data = $this->CI->Data_model->find($data_id);
      $data = $this->CI->Data_model->getWhereOne($where);
      if(!empty($data['id'])) {
        $meta_data = $this->CI->MetaData_model->fetchAllbyDataID($data['id']);
      
        if(!empty($meta_data))
        {
            foreach($meta_data as $meta)
            {
                if($frontview === true) {
                  //フロント表示する場合は form_groupタイプのデータのシリアライズされた値を復元する処理を入れる
                  $meta_value = $this->CI->frontviewlib->metadataToDisplay($meta);
                } else {
                  $meta_value = $meta["meta_value"];
                }
                $data["_meta_"][$meta["meta_key"]] = $meta_value;
            }
        }
      }

      return $data;
  }

  /**
   * カテゴリ選択プルダウン用のカテゴリ登録リストを取得
   *
   * @return object
   */
  function get_category_list()
  {
      $result = $this->CI->Category_model->find_all();

      $category_data = array();
      if($result && count($result))
      {
          foreach($result as $value)
          {
              $category_data[$value["category_id"]] = $value["category_name"];
          }
      }
      return $category_data;
  }

  function getnum_category_by($where = array()) {
    return $this->CI->Data_model->getnum_category_by($where);
  }


  function dataSave($data , $metadata , $login_id) {

    //トランザクションスタート
    $this->CI->db->trans_start();

    $save_data_id = $this->replaceData($data , $login_id);
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
   * @param string $login_id
   */
  function replaceData($data , $login_id="")
  {
      $tid = "";

      //公開権限があるか
      if($this->CI->auth->chk_controll_limit("publish_post" , $login_id))
      {
          //権限ある→そのままインサートもしくはアップデート
          if(!empty($data["id"]))
          {
              //id指定がある場合、そのデータが存在しているか（更新モードか、新規モードか）
              $rs = $this->CI->Data_model->find($data["id"]);
          }
          $save_where = array();
          if(!empty($rs))
          {
              $save_where = array('id' => $data['id']);
              //更新モードならhistoryをアップデート
              $data["last_edit_user_id"] = $login_id; //記事最終更新者
              $data["history"] = $this->update_history($data["id"], "-", $login_id, "更新処理" , fn_get_date()); //hisotry
              $data['modified'] = fn_get_date();
          }
          else
          {
              //新規記事ならhistoryを作成
              //ソートNoを付ける場合は、登録データの最大値を取得
              $data["sort_num"] = (($this->set_sort_num === true) ? $this->get_max_sort_num() + 1 : 0); 
              $data["user_id"] = $login_id; //記事作成者
              $data["history"] = $this->create_new_history("" , $login_id, "新規作成" , fn_get_date()); //history作成
              $data['created'] = fn_get_date();
              $data['modified'] = fn_get_date();
          }

          $tid = $this->CI->Data_model->replaceData($data , $save_where);
      }

      //公開権限がない（新規作成ならOK 編集モードなら非公開かつ自分自身が作成した記事かどうかチェックして処理）
      else
      {

          if(!empty($data["id"])){
              //id指定がある場合、そのデータが存在しているか（更新モードか、新規モードか）
              //または更新モードの場合、この記事が公開状態かどうか確認
              $rs = $this->CI->Data_model->find($data["id"]);
          }

          if(empty($data["id"]) || empty($rs) || (!empty($rs) && !$rs["output_flag"] && ($data["user_id"] == $login_id)))
          {

              $save_where = array();
              if(!empty($rs))
              {
                  if(!empty($rs["last_edit_user_id"])) {
                      $data["last_edit_user_id"] = $login_id; //記事最終更新者
                  }
                  //更新モードならhistoryをアップデート
                  $data["history"] = $this->update_history($data["id"], "wait", $login_id, "承認待ち・編集" , fn_get_date()); //hisotry
                  $data['modified'] = fn_get_date();
                  $save_where = array('id' => $data["id"]);
              }
              else
              {
                  //新規記事ならhistoryを作成
                  //ソートNoを付ける場合は、登録データの最大値を取得
                  $data["sort_num"] = (($this->set_sort_num === true) ? $this->CI->Data_model->getMaxSortNum() + 1 : 0); 
                  $data["user_id"] = $login_id; //記事作成者
                  $data["history"] = $this->create_new_history("wait" , $login_id, "承認待ち" , fn_get_date());
                  $data['created'] = fn_get_date();
                  $data['modified'] = fn_get_date();
              }

              //DB登録：parent_data_id , status,historyを追記して新規挿入
              $data["status"] = "wait";
              $data["output_flag"] = 0;
              $tid = $this->CI->Data_model->replaceData($data , $save_where);


              //管理者あてに承認待ちの記事が作成された旨をメール送信
              //メール送信をおこなう管理者アカウントのデータ取得
              $target_admin_arr = $this->CI->auth->get_sendmail_target("add_wait");
              if($target_admin_arr && count($target_admin_arr))
              {
                  //メール送信先（管理者宛て）
                  $send_to_arr = array();
                  foreach($target_admin_arr as $target_admin)
                  {
                      if(isset($target_admin["email"]))
                      {
                          $send_to_arr[] = $target_admin["email"];
                      }
                  }

                  if(count($send_to_arr))
                  {
                      //記事作成者
                      $editor_info = $this->CI->Admins_model->fetchOneByLoginID($data["user_id"]);

                      if($editor_info)
                      {
                          //メール本文データ
                          $mail_data = array(
                              "editor_name" => $editor_info["user_name"],
                              "title" => $data["title"],
                              "modified" => $data["modified"]
                          );

                          $this->app_action_sendmail($send_to_arr, "app_request_message", "（".ADMINPAGE_NAME."）承認待ちとなっている登録があります", $mail_data , $editor_info["email"]);
                      }
                  }
              }
              //メール送信ここまで
          }
      }

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
      $registered_data = $this->CI->MetaData_model->fetchAllbyDataID($data_id);
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

            $this->CI->MetaData_model->replaceData($db_data , $where);
          }
      }

      //メタデータの過去分で余りがあれば削除対象となる
      if(!empty($registered_data)) {
        foreach($registered_data as $data) {
          $this->CI->MetaData_model->delete($data['meta_id'] , 'meta_id');
        }
      }

      return;
  }

  function data_delete($data_id) {
    return $this->CI->Data_model->delete($data_id);
  }

  function metadata_delete($data_id) {
    return $this->CI->MetaData_model->deleteWhere(array("data_id"=>$data_id));
  }


  function get_max_sort_num() {
    return $this->CI->Data_model->getMaxSortNum();
  }
  function get_min_sort_num() {
    return $this->CI->Data_model->getMinSortNum();
  }
  
  //ソート番号を次のデータと入れ替え
  function sort_num_up($id , $where , $sort_order_type) {
    return $this->CI->Data_model->sortNumChange($id , 'up' , $where , $sort_order_type);
  }
  //ソート番号を前のデータと入れ替え
  function sort_num_down($id , $where , $sort_order_type) {
    return $this->CI->Data_model->sortNumChange($id , 'down' , $where , $sort_order_type);
  }

  /**
   * 表示日付を一覧表示用に整形して返す
   * @param string $output_date
   * @return string
   */
  function view_output_date($output_date)
  {
      //表示日付が未来の日付であれば、日付表示を時間まで表示させ
      //公開予約状態にあることをわかるようにする
      if(strtotime($output_date) > strtotime('now'))
      {
          return '<strong>'.fn_dateFormat($output_date, "Y年m月d日").'<br />'.fn_dateFormat($output_date, "H時i分").' に掲載開始</strong>';
      }

      return fn_dateFormat($output_date);
  }

  /**
   * 記事一覧で表示させるデータ件数表示のhtml
   * @param number $cnt
   * @param number $page
   * @param number $page_num
   * @param number $output_num
   * @return string
   */
  function html_post_list_pager_head($cnt , $page , $page_num , $output_num)
  {
      return $this->CI->message->data_cnt_info($cnt , $page , $page_num , $output_num);
  }
  function html_admin_post_list_pager_head($cnt , $page , $page_num , $output_num)
  {
      return $this->CI->message->data_cnt_info_admin($cnt , $page , $page_num , $output_num);
  }

  /**
   * 送信されてきた削除対象とするidを取得
   * @param objet $data
   * @param string $key_id //単一で削除指定されたid値のKEY
   * @param string $key_id_arr　//複数で削除指定されたid値のKEY
   * @return array
   */
  function get_delete_id($data , $key_id , $key_id_arr)
  {
      $delete_id_arr = array();
      if(isset($data[$key_id]) && is_num($data[$key_id]))
      {
          $delete_id_arr[] = $data[$key_id];
      }
      else if(isset($data[$key_id_arr]) && count($data[$key_id_arr]) > 0){

          foreach($data[$key_id_arr] as $data_id)
          {
              if(is_num($data_id))
              {
                  $delete_id_arr[] = $data_id;
              }
          }
      }

      return $delete_id_arr;
  }


  /**
   * 記事編集画面にて、登録完了後に表示させるメッセージを取得
   * @param void
   * @return string
   */
  function get_edit_complete_message()
  {
      if(isset($_GET["complete"]))
      {
          //return "登録が完了しました。<br />新規作成された場合、".DATA_NAME."一覧画面において公開切り替えの操作を行う必要があります。";
          return "登録が完了しました。";
      }
      else if(isset($_GET["completewait"]))
      {
          return "登録が完了しました。<br />この".DATA_NAME."は「承認待ち」の状態として保存されました";
      }
      return "";
  }

  /**
   * 編集画面にて権限がない場合に出力するエラーメッセージを取得
   * @param void
   * @return string
   */
  function errMessage_authority()
  {
      return "権限がないため、この".DATA_NAME."を編集することができません。";
  }

  /**
   * 編集画面にてデータ編集の際に、編集前の最終更新日時が書き換わっていた場合のエラー（別アカウントで同じ記事を編集されてしまっていた可能性がある）メッセージを取得
   * @param void
   * @return string
   */
  function errMessage_conflict()
  {
      //return "別のユーザーによってこの".DATA_NAME."の編集が行われた可能性があるため登録できませんでした。<br />一覧画面に戻り、もう一度編集作業をお試しください。";
      return "データのコンフリクトが発生する可能性があったため、登録を行えませんでした。<br />お手数ですが、もう一度登録作業をお試しください。";
  }


  public function get_dataname($type) {
    $data_name = $this->CI->config->item('data_name' , 'config_myapp');
    if(!empty($data_name[$type])) {
      return $data_name[$type];
    }

    return "データ";
  } 

}
