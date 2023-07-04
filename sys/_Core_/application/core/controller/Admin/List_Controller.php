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
require(dirname(__FILE__).'/Base_Controller.php');

class List_Controller extends Base_Controller
{

  protected $NUM_LIST = 10;

  protected $listSetting = [];

  protected $sort_order_key = 'distribution_start_date';
  protected $sort_order_type = 'DESC';

  protected $category_list = [];


    public function __construct($init = true) {
        parent::__construct();
        
        if($init) {
          $this->_init();
        }
    }

    private function _init() {

      define('ADMINPAGE_NAME' , (!empty($this->adminpage_title) ? $this->adminpage_title : DATA_NAME.'一覧') . ' | '.(defined('ADMIN_BASE_TITLE') ? ADMIN_BASE_TITLE : $this->config->item('admin_title' , 'config_myapp')));
      
      $this->load->model($this->dirname.'/'.$this->dirname.'_data_model', 'Data_model');
      $this->load->model($this->dirname.'/'.$this->dirname.'_metadata_model', 'MetaData_model');
      $this->load->model($this->dirname.'/'.$this->dirname.'_category_model', 'Category_model');

      $this->load->library('datalib' , array(
        'data_model_name' => $this->dirname.'/'.$this->dirname.'_data_model',
        'metadata_model_name' => $this->dirname.'/'.$this->dirname.'_metadata_model',
        'category_model_name' => $this->dirname.'/'.$this->dirname.'_category_model'
      ));

      $this->load->library(array('auth' , 'urlsetting' , 'adminslib'));

      //権限チェック
      $this->login_id = $this->auth->IsSuccess( true , "manage_".$this->dirname );

      $this->category_list = array('0' => '選択なし') + $this->datalib->get_category_list();
    }


    public function index() {

      $search_param = $this->_get_search_param();
      $search_where = (!empty($search_param['where']) ? $search_param['where'] : []);
      $search_like = (!empty($search_param['like']) ? $search_param['like'] : []);
      //承認待ちのデータのみ抽出
      if(isset($_GET["wait"]) && is_num($_GET["wait"])) {
          $search_where["status"] = "wait";
      }


      //現在のページ
      if(!isset($_GET['page'])) $_GET['page'] = 1;
      $page = fn_valid_page($_GET['page']);
      //データ件数取得
      $cnt = $this->datalib->get_data_cnt($search_where , $search_like);
      //総ページ数取得
      $page_num = fn_getPages($cnt , $this->NUM_LIST);
      if($page > $page_num){ $page = $page_num; }

      //承認待ちデータ数をカウント
      $cnt_wait = $this->datalib->get_data_cnt_waiting();


      //データリスト取得
      $data_list = $this->datalib->get_list(
        $page , 
        $this->NUM_LIST , 
        $search_where , 
        $search_like , 
        $this->sort_order_key , 
        $this->sort_order_type
      );


      //最大、最小のソートNo取得
      $max_sort_num = $this->datalib->get_max_sort_num();
      $min_sort_num = $this->datalib->get_min_sort_num();

      //ユーザーアカウントリスト取得
      $account_list = $this->adminslib->getAccountList();

      $tmpl_param = [];
      $tmpl_param['listSetting'] = $this->listSetting;
      $tmpl_param['sort_order_key'] = $this->sort_order_key;
      $tmpl_param['sort_order_type'] = $this->sort_order_type;
      $tmpl_param['search_result_text'] = $search_param['result_text'];
      $tmpl_param['max_sort_num'] = $max_sort_num;
      $tmpl_param['min_sort_num'] = $min_sort_num;

      $tmpl_param['result'] = $data_list;

      $html_list_header = $this->load->custom_view('' , 'common/lists/parts/list_item_header' , array('data' => $tmpl_param) , true);
      $html_list_footer = $this->load->custom_view('' , 'common/lists/parts/list_item_footer' , array('data' => $tmpl_param) , true);

      $html_list_body = '';

      if(!empty($data_list)) {
        $i = 0;
        foreach($data_list as $data) {

          //preview URLを生成（GETでpreview=1を追記する）
          $page_url = $this->urlsetting->get_pageurl($this->dirname."_detail" , $data["id"] , $data["category"]);
          $parse_page_url = parse_url(site_url($page_url));
          if(isset($parse_page_url["query"]) && $parse_page_url["query"]){
              $preview_url = $page_url."&preview=1";
          } else {
              $preview_url = $page_url."?preview=1";
          }

          $tmpl_param['value'] = $data;
          
          $tmpl_param = array_merge($tmpl_param , array(
            'delete'        => ($this->auth->chk_controll_limit("delete_post") || (!$data["output_flag"] && isset($data["user_id"]) && $data["user_id"] == $this->login_id))?true:false,
            'edit_link'     => site_url($this->dirname."/edit/".$data["id"]."/".fn_urlprm($_GET)),
            'category_name' => (isset($category_list[$data['category']]) ? $category_list[$data['category']] : ''),
            'publish_menu'  => $this->datalib->publish_menu($data , site_url($this->dirname."/publish/[link_type]/".$data["id"]."/".fn_urlprm($_GET))),
            //'account_name'  => $this->auth->getAccountName($data["user_id"]),
            'account_name'    => $this->_get_account_name($data["user_id"] , $account_list),
            //'created'       => $data["created"],
            //'modified'      => $data["modified"],
            //'last_edit_username'  => $this->auth->getAccountName($data["last_edit_user_id"]),
            'last_edit_username'  => $this->_get_account_name($data["last_edit_user_id"] , $account_list),
            'preview_url'   => $preview_url,
            'page_url'      => $page_url,
            //'sort_down_url' => site_url(INDEX_CONTROLLER_PATH.'/sort/?down='.$data['id'].'&'.fn_urlprm($_GET , array() , false)),
            //'sort_up_url' => site_url(INDEX_CONTROLLER_PATH.'/sort/?up='.$data['id'].'&'.fn_urlprm($_GET , array() , false))
            'tr_class'      => ($i === 0 ? ' class="first"' : '')
          ));
          
          //******************************
          //特別にデータ削除ができない場合（トップページ管理画面など）
          if($this->dirname == 'toppage') {
            $tmpl_param['delete'] = false;
          }
          //******************************

          $html_list_body .= $this->load->custom_view('' , 'common/lists/parts/list_item_body' , array('data' => $tmpl_param) , true);

          $i++;
        }
      }
      
      //記事データがカテゴリごとに何件あるかを取得
      $cat_cnt_where = array();
      if(isset($_GET["wait"]) && is_num($_GET["wait"])) {
          $cat_cnt_where = array("status"=>"wait");
      }
      $cnt_category_by =  $this->datalib->getnum_category_by($cat_cnt_where);

      $this->view_data = array_merge($this->view_data , array(
        'category_id' => (!empty($category_id)) ? $category_id : 0,
        'search_html' => $search_param['html'], //検索項目部分のhtml
        'search_result_text' => $search_param['result_text'],
        'cnt_category_by' => $cnt_category_by,
        'page' => $page,
        'cnt' => $cnt,
        'cnt_wait' => $cnt_wait,
        'page_num' => $page_num,
        'sort_order_key' => $this->sort_order_key, //ソートNo順で表示する場合は 表示順入れ替え矢印を表示させるため
        'sort_order_type' => $this->sort_order_type,
        'max_sort_num' => $max_sort_num,
        'min_sort_num' => $min_sort_num,
        //'html_pager_head' => $this->datalib->html_post_list_pager_head($cnt , $page , $page_num , $this->NUM_LIST),
        'html_list_header' => $html_list_header,
        'html_list_body' => $html_list_body,
        'html_list_footer' => $html_list_footer,
        'search_base_url' => site_url(INDEX_CONTROLLER_PATH),
        'delete_url'  => site_url(INDEX_CONTROLLER_PATH.'/delete/'.fn_urlprm($_GET)),
        'waitdata_url'  => site_url(INDEX_CONTROLLER_PATH).'?wait=1',
        'blank_page_url' => site_url($this->dirname . '/publish/blank'),
        'sort_change_url' => site_url(INDEX_CONTROLLER_PATH.'/sort/'),
        'edit_page_url' => site_url($this->dirname."/edit/")
      ));

      //ページャ用
      $page_info = calc_pageinfo($cnt , $page , $this->NUM_LIST);
          
      //ページャリンク生成
      $pager_param = $_GET;
      if(isset($pager_param['page'])) unset($pager_param['page']);
      $this->view_data['pager_html'] = $this->_paging(site_url(INDEX_CONTROLLER_PATH).fn_urlprm($pager_param) , $page_info['page'] , $cnt , $this->NUM_LIST);
      $this->view_data['html_pager_head'] = $this->datalib->html_admin_post_list_pager_head($cnt , $page_info['page'] , $page_num ,$this->NUM_LIST); //データ件数まわりの表示


      $this->load->custom_view($this->dirname , 'lists/index' , $this->view_data);

      return;
    }



    public function delete() {
      
      $delete_ids = array();
      if($this->input->post("del_id") && is_num($this->input->post("del_id") )) {
          //権限チェック(削除権限があるか、もしくは自分自身が作成した記事で公開されていないかどうかチェックする OKなら削除実行）
          $delete_data = $this->datalib->getData($this->input->post('del_id'));
          if(!empty($delete_data) && $this->auth->chk_controll_limit("delete_post") || (!$delete_data["output_flag"] && isset($delete_data["user_id"]) && $this->login_id == $delete_data["user_id"]))
          {
              $delete_ids[] = $this->input->post("del_id");
          }
      //---------------------------------
      //複数選択で削除が要求された場合
      //---------------------------------
      } else if($this->input->post('del_posts') && is_array($this->input->post('del_posts'))){

          foreach($this->input->post('del_posts') as $data_id)
          {
              if(is_num($data_id))
              {
                  //権限チェック(削除権限があるか、もしくは自分自身が作成した記事で公開されていないかどうかチェックする OKなら削除実行）
                  $delete_data = $this->datalib->getData($data_id);
                  if(!empty($delete_data) && $this->auth->chk_controll_limit("delete_post") || (!$delete_data["output_flag"] && isset($delete_data["user_id"]) && $this->login_id == $delete_data["user_id"]))
                  {
                      $delete_ids[] = $data_id;
                  }
              }
          }
      }
      if(!empty($delete_ids))
      {
          foreach($delete_ids as $tid)
          {
              //データ削除
              $this->datalib->data_delete($tid);
              $this->datalib->metadata_delete($tid);
          }
      }

      if(!empty($_GET['page'])) {
        $page = fn_valid_page($_GET['page']);
        $_GET['page'] = $page;
      }

      redirect(site_url(INDEX_CONTROLLER_PATH."/".fn_urlprm($_GET)) , 'location');

    }


    /**
     * ソート処理（Ajax）
     */
    public function sort() {
      /*
      $t_data_id = '';

      if(!empty($_GET['up'])) {
        if(is_num($_GET['up'])) {
          $t_data_id = $this->datalib->sort_num_up($_GET['up']);
        }
      }elseif(!empty($_GET['down'])) {
        if(is_num($_GET['down'])) {
          $t_data_id = $this->datalib->sort_num_down($_GET['down']);
        }
      }

      if(!empty($_GET['page'])) {
        $page = fn_valid_page($_GET['page']);
        $_GET['page'] = $page;
      }

      redirect(site_url(INDEX_CONTROLLER_PATH."/".fn_urlprm($_GET))."#dataPos".$t_data_id , 'location');
      */

      $t_data_id = '';

      if(!empty($_POST['sort_change_type']) && !empty($_POST['sort_change_id'])) {
        if(is_num($_POST['sort_change_id'])) {
          $t_data_id = $_POST['sort_change_id'];

          if($_POST['sort_change_type'] == 'up') {
            $this->datalib->sort_num_up($t_data_id , array() , $this->sort_order_type);
          }elseif($_POST['sort_change_type'] == 'down') {
            $this->datalib->sort_num_down($t_data_id , array() , $this->sort_order_type);
          }
        }
      }

      $return = array('result' => 'success');
      $this->output
             ->set_content_type('application/json')
             ->set_output(json_encode($return));

      return;
    }

    /**
     * 検索項目まわりのhtml生成や検索パラーメタを整理する処理
     */
    private function _get_search_param() {

      $search_html = '';
      $search_params = [];
      $search_where = [];
      $search_like = [];
      $search_result_text = '';

      if(!empty($this->listSetting['search_item'])) {
        foreach($this->listSetting['search_item'] as $key => $item) {
          $search_html .= '<li>';
          if(!empty($_GET[$key])) {
            if($item['type'] == 'input') {
              $search_like[$key] = $_GET[$key];
              $search_result_text .= $item['label'].'「'.$_GET[$key].'」';
            }elseif($item['type'] == 'select'){
              $search_where[$key] = $_GET[$key];
              $search_result_text .= $item['label'].'「'.$item['data'][$_GET[$key]].'」';
            }
            $search_params[$key] = $_GET[$key];
            $item['value'] = $_GET[$key];
          }

          $item['key'] = $key;
          
          $search_html .= $this->load->view('common/lists/parts/search_item_'.$item['type'] , array('data' => $item) , true);
          $search_html .= '</li>';
        }
      }

      return array(
        'html'  => $search_html,
        'params' => $search_params,
        'where' => $search_where , 
        'like' => $search_like , 
        'result_text' => $search_result_text
      );
    }

    private function _get_account_name($user_id , $account_list) {
      $account_name = (!empty($account_list[$user_id]) ? $account_list[$user_id]['user_name'] : '');
      if(!empty($account_name) && $account_list[$user_id]['deleted_flag']) {
        $account_name .= '【削除済みアカウント】';
      }

      if(empty($account_name)) {
        $account_name = ' － ';
      }

      return $account_name;
    }
}
