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

class Datapage_Controller extends MY_Controller
{

    public function __construct() {

        parent::__construct();
        $this->init();
    }
    
  private function init() {

      $this->load->library('datalib' , array(
        'data_model_name' => 'info/info_data_model',
        'metadata_model_name' => 'info/info_metadata_model',
        'category_model_name' => 'info/info_category_model'
      ));

      $this->category_list = $this->datalib->get_category_list();
    }

  public function index() {

    $time = fn_get_date();
    $search_where = "output_flag = 1 AND distribution_start_date <= '".$time."' AND (distribution_end_date = '0000-00-00 00:00:00' OR distribution_end_date >= '".$time."')";
    if(!empty($_GET['category']) && is_num($_GET['category'])) {
      $search_where .= " AND category = '".$_GET['category']."'";
    }

    $search_like = array();


    //現在のページ
    if(!isset($_GET['page'])) $_GET['page'] = 1;
    $page = fn_valid_page($_GET['page']);
    //データ件数取得
    $cnt = $this->datalib->get_data_cnt($search_where , $search_like);
    //総ページ数取得
    $page_num = fn_getPages($cnt , $this->NUM_LIST);
    if($page > $page_num){ $page = $page_num; }

    //データリスト取得
    $data_list = $this->datalib->get_list(
      $page , 
      $this->NUM_LIST , 
      $search_where , 
      $search_like , 
      $this->sort_order_key , 
      $this->sort_order_type
    );

    $content_data = array();
    if(!empty($data_list)) {
      $i = 0;
      foreach($data_list as $data) {

        $content_data[$i] = $data;
        $content_data[$i]['detail_link'] = site_url('info/d/'.$data['id']);
        $i++;
      }
    }

    //パンくず
    $breadcrumb = [
      ['url' => site_url() , 'label' => 'Top'],
      ['url' => '' , 'label' => 'ニュース']
    ];

    $this->view_data = array_merge($this->view_data , array(
      'content_data'  => $content_data,
      'content_title' => 'ニュース一覧',
      'breadcrumb'    => $breadcrumb,
      'category_list' => $this->category_list,
      'page'          => $page,
      'cnt'           => $cnt,
      'page_num'      => $page_num,
      'search_base_url' => site_url('info'),
    ));

    //ページャ用
    $page_info = calc_pageinfo($cnt , $page , $this->NUM_LIST);
        
    //ページャリンク生成
    $pager_param = $_GET;
    if(isset($pager_param['page'])) unset($pager_param['page']);
    $this->view_data['pager_html'] = $this->_paging(site_url('info').fn_urlprm($pager_param) , $page_info['page'] , $cnt , $this->NUM_LIST);
    $this->view_data['html_pager_head'] = $this->datalib->html_post_list_pager_head($cnt , $page_info['page'] , $page_num ,$this->NUM_LIST); //データ件数まわりの表示

    $this->frontviewlib->view('info_list' , $this->view_data);

    return;
  }



  public function detail($id) {

    //第２引数にtrueを指定してフロント表示対象データのみを抽出するようにする
    $content_data = $this->datalib->getData($id , true);

    if(empty($content_data)) {
      $this->_error_notfound();
      return;
    }

    //パンくず
    $breadcrumb = [
      ['url' => site_url() , 'label' => 'Top'],
      ['url' => site_url('info') , 'label' => 'ニュース'],
      ['url' => '' , 'label' => fn_cutstr($content_data['title'] , 40)]
    ];

    $this->view_data = array_merge($this->view_data , array(
      'content_data' => $content_data,
      'content_title' => 'ニュース',
      'category_name' => (!empty($this->category_list[$content_data['category']]) ? $this->category_list[$content_data['category']] : ''),
      'listpage_url' => site_url('info'),
      'breadcrumb'    => $breadcrumb
    ));

    $this->frontviewlib->view('info_detail' , $this->view_data);

    return;
  }

}