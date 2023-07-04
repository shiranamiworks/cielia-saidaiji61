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

class Media_Controller extends Base_Controller
{

    private $NUM_LIST = 15;

    public function __construct() {
        parent::__construct();

        $this->_init();
    }

    private function _init() {

      define('ADMINPAGE_NAME' , 'ファイル管理 | '.(defined('ADMIN_BASE_TITLE') ? ADMIN_BASE_TITLE : $this->config->item('admin_title' , 'config_myapp')));
      define('LISTPAGE_CONTROLLER_PATH' , INDEX_CONTROLLER_PATH.'/lists');

      $this->load->model(array($this->dirname.'/'.$this->dirname.'_media_model'));
      $this->load->library('mediafile' , array(
        'media_model_name' => $this->dirname.'/'.$this->dirname.'_media_model' 
      ));

      //index page用
      $this->view_data['index_url'] = site_url(INDEX_CONTROLLER_PATH);
      $this->view_data['upload_url'] = site_url(INDEX_CONTROLLER_PATH.'/upload');
      $this->view_data['delete_url'] = site_url(INDEX_CONTROLLER_PATH.'/delete');

      //list page用
      $this->view_data['listpage_url'] = site_url(LISTPAGE_CONTROLLER_PATH);
      $this->view_data['listpage_upload_url'] = site_url(INDEX_CONTROLLER_PATH.'/list_upload');

      $this->view_data['upload_maxsize'] = $this->config->item('media_upload_maxsize' , 'config_myapp');
    }


    // get param : page / keyword
    public function index() {

      if(!isset($_GET['page'])) $_GET['page'] = 1;
      $page = fn_valid_page($_GET['page']);

      $keyword = '';
      if(!empty($_GET['keyword'])) $keyword = urldecode($_GET['keyword']);

      //データリスト取得
      $this->view_data['result'] = $this->mediafile->getFileList($page , $this->NUM_LIST , "" , $keyword);

      $page_info = calc_pageinfo($this->view_data['result']['num'] , $page , $this->NUM_LIST);

      //ページャリンク生成
      $this->view_data['pager_html'] = $this->_paging(site_url(INDEX_CONTROLLER_PATH).'/'.fn_urlprm($_GET, array('type','called','target','keyword')) , $page_info['page'] , $this->view_data['result']['num'] , $this->NUM_LIST);

      $this->view_data['html_pager_head'] = $this->mediafile->html_media_list_pager_head($this->view_data['result']['num'] , $page_info['page'], $page_info['page_num'] , $this->NUM_LIST);

      $this->view_data['keyword'] = $keyword;
      $this->view_data['delete_url'] .= '?page='.$page_info['page'].'&keyword='.$keyword;
      $this->load->custom_view($this->dirname , 'media/index' , $this->view_data);

      return;

    }

    public function upload() {

      $result = $this->_do_upload();

      if(!empty($result['success'])) {
          $this->session->set_flashdata('success', $result['success']);
      }
      if(!empty($result['errors'])) {
          $this->session->set_flashdata('errors', $result['errors']);
      }

      redirect(site_url(INDEX_CONTROLLER_PATH) , 'location');

      return;

    }

    // get param :  page / keyword / target / called / type
    public function lists() {

      if(!isset($_GET['page'])) $_GET['page'] = 1;
      $page = fn_valid_page($_GET['page']);

      $keyword = '';
      if(!empty($_GET['keyword'])) $keyword = urldecode($_GET['keyword']);

      $filter = (!empty($_GET['type']) ? $_GET['type'] : '');

      //データリスト取得
      $this->view_data['result'] = $this->mediafile->getFileList($page , $this->NUM_LIST , $filter , $keyword);

      $page_info = calc_pageinfo($this->view_data['result']['num'] , $page , $this->NUM_LIST);

      //ページャリンク生成
      $this->view_data['pager_html'] = $this->_paging(site_url(LISTPAGE_CONTROLLER_PATH).fn_urlprm($_GET, array('type','called','target','keyword')) , $page_info['page'] , $this->view_data['result']['num'] , $this->NUM_LIST);

      $this->view_data['html_pager_head'] = $this->mediafile->html_media_list_pager_head($this->view_data['result']['num'] , $page_info['page'], $page_info['page_num'] , $this->NUM_LIST);

      $this->view_data['keyword'] = $keyword;

      //呼び出しもと
      $this->view_data['btnSelect'] = 0;
      $this->view_data['target'] = '';
      if(!empty($_GET['called']) && $_GET['called'] == 'btnSelect') {
          $this->view_data['btnSelect'] = 1;
          $this->view_data['target'] = (!empty($_GET['target']) ? $_GET['target'] : '1');
      }
      $this->view_data['type'] = $filter;
      $this->view_data['called'] = (!empty($_GET['called']) ? $_GET['called'] : '');

      $this->view_data['listpage_url'] .= '/'.fn_urlprm($_GET, array('type','called','target'));
      $this->view_data['listpage_upload_url'] .= '/'.fn_urlprm($_GET, array('type','called','target'));

      $this->load->custom_view($this->dirname , 'media/list' , $this->view_data);

      return;
    }

    public function list_upload() {

      if(!empty($this->input->post('upload_flg'))) {
          $result = $this->_do_upload();
          if(!empty($result['success'])) {
              $this->view_data['success'] = $result['success'];
          }
          if(!empty($result['errors'])) {
              $this->view_data['errors'] = $result['errors'];
          }
      }

      $this->view_data['btnSelect'] = 0;
      $this->view_data['target'] = '';
      $this->view_data['listpage_url'] .= '/'.fn_urlprm($_GET, array('type','called','target'));
      $this->view_data['listpage_upload_url'] .= '/'.fn_urlprm($_GET, array('type','called','target'));

      return $this->load->custom_view($this->dirname , 'media/list_upload' , $this->view_data);

    }


  public function delete() {
    $delete_id = array();
    if($this->input->post("del_id") && is_num($this->input->post("del_id") )) {
        //権限チェック(削除権限があるか  OKなら削除実行）
        if($this->auth->chk_controll_limit("delete_files")) {
            $delete_ids[] = $this->input->post("del_id");
        }
    }
    if(!empty($delete_ids))
    {
        foreach($delete_ids as $tid)
        {
            //データ削除
            $this->mediafile->delete($tid);
        }
    }

    $page = fn_valid_page((!empty($_GET['page'])?$_GET['page']:1));
    $keyword = (!empty($_GET['keyword'])?$_GET['keyword'].'/':'');
    $type = (!empty($_GET['type'])?$_GET['type']:'');

    redirect(site_url(INDEX_CONTROLLER_PATH."/".fn_urlprm($_GET)) , 'location');

  }


  private function _do_upload() {

    if(empty($this->input->post('upload_flg'))) {
        return false;
    }

    $config['upload_path']         = $this->mediafile->getUploadDir();
    //$config['allowed_types']       = 'gif|jpg|png|pdf|doc|docx|xls|xlsx';
    $config['allowed_types']       = 'gif|jpg|png|pdf';
    $config['max_size']            = $this->config->item('media_upload_maxsize' , 'config_myapp');
    //$config['encrypt_name']              = TRUE;
    $config['file_ext_tolower'] = true;
    $config['file_name_tolower'] = false;
    $this->load->library('upload', $config);
    $files = $_FILES;
    $posts = $this->input->post();
    $cpt = count($_FILES['upfile']['name']); //アップロードされた件数を確認

    $errors = array();
    $success = array();
    $this->load->library('image_lib');
    
    // ループしながら$_FILESを上書き
    for ($i = 0; $i < $cpt; $i++) {
        if(!empty($files['upfile']['name'][$i]) && isset($files['upfile']['tmp_name'][$i])) {
          $_FILES['file']['name']= $this->dirname.'_'.$files['upfile']['name'][$i];
          $upload_filename = $_FILES['file']['name'];
          $_FILES['file']['type']= $files['upfile']['type'][$i];
          $_FILES['file']['tmp_name']= $files['upfile']['tmp_name'][$i];
          $_FILES['file']['error']= $files['upfile']['error'][$i];
          $_FILES['file']['size']= $files['upfile']['size'][$i];

          if(!preg_match('/^([a-zA-Z0-9_]|\-|\.|\+)+$/' , $upload_filename )) {

            $errors[$i+1] = 'ファイル名が不正です（半角英数字とハイフン、アンダーバーのみ利用可）';

          }else if ( ! $this->upload->do_upload('file')) {
            
            $errors[$i+1] = $this->upload->display_errors("","");

          } else {

              $upload_data = $this->upload->data();
              $success[$i+1] = $upload_data;
              //$upload_data['upload_filename'] = $upload_data['orig_name'];
              $upload_data['upload_filename'] = $upload_data['file_name'];
              $upload_data['description'] = $posts['upfile_desc'][$i];
              //サムネイルリサイズ
              if($upload_data['is_image']) {
                  $resize_config = array(
                      'source_image'  => $upload_data['full_path'],
                      'create_thumb'  => true,
                      'width'         => 150
                  );
                  //$this->load->library('image_lib', $resize_config); 
                  $this->image_lib->initialize($resize_config);
                  $this->image_lib->resize();
                  $this->image_lib->clear();

                  //スマフォ用リサイズ
                  $smp_width = 600;
                  if($upload_data['image_width'] < 600) {
                    $smp_width = $upload_data['image_width'];
                  }
                  $resize_config = array(
                      'source_image'  => $upload_data['full_path'],
                      'new_image'     => $this->mediafile->getUploadDir().'/'.$upload_data['raw_name'].'_smp'.$upload_data['file_ext'],
                      'width'         => $smp_width
                  );

                  $this->image_lib->initialize($resize_config);
                  $this->image_lib->resize();
                  $this->image_lib->clear();

              }

              //アップロード成功
              //DBに保存
              if(!empty($upload_data)) {
                $this->Media_model->saveMediaData($upload_data);
              }
          }

       }
       
       $this->upload->initialize($config);
    }

    return array(
        'success'   => $success,
        'errors'    => $errors
    );

  }

}
