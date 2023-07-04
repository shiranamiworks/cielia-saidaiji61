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

class Edit_Controller extends Base_Controller
{

    protected $editSetting = [];
    protected $category_list = [];

    public function __construct($init = true) {
        parent::__construct();

        if($init) {
          $this->_init();
        }
    }

    private function _init() {

      define('ADMINPAGE_NAME' , DATA_NAME.'登録 | '.(defined('ADMIN_BASE_TITLE') ? ADMIN_BASE_TITLE : $this->config->item('admin_title' , 'config_myapp')));
    
      $this->load->model($this->dirname.'/'.$this->dirname.'_data_model', 'Data_model');
      $this->load->model($this->dirname.'/'.$this->dirname.'_metadata_model', 'MetaData_model');
      $this->load->model($this->dirname.'/'.$this->dirname.'_category_model', 'Category_model');
      $this->load->model('Search_tag_model', 'SearchTagModel');

      $this->load->library('datalib' , array(
        'data_model_name' => $this->dirname.'/'.$this->dirname.'_data_model',
        'metadata_model_name' => $this->dirname.'/'.$this->dirname.'_metadata_model',
        'category_model_name' => $this->dirname.'/'.$this->dirname.'_category_model',
        //ソートNoを登録するか
        'set_sort_num' => (defined('SET_SORT_NUM') ? SET_SORT_NUM : false)
      ));
      $this->load->library(array('auth' , 'urlsetting'));

      //権限チェック
      $this->login_id = $this->auth->IsSuccess();

      $this->view_data['filelist_url'] = site_url($this->dirname.'/media/lists');
      $this->view_data['list_page_url'] = site_url($this->dirname.'/lists');
    }



    public function index($id = '') {

      $this->view_data['submit_url'] = site_url(INDEX_CONTROLLER_PATH.'/'.$id);
      $this->view_data['cancel_url'] = site_url($this->dirname.'/lists');
      $this->view_data['preview_url'] = site_url($this->dirname.'/preview');
      $this->view_data['preview_site_url'] = $this->_clientsInfo('site_url').$this->dirname.'/d/'.(!empty($id) ? $id : 0);
      
      $this->view_data['edit_authority'] = true;
      $this->view_data['error'] = array();
      //編集モードの場合、そのデータをユーザーが編集可能かどうかをチェック
      $editData = array();
      if(!empty($id)) {
        $editData = $this->datalib->getData($id);

        if(empty($editData)) {
          $this->_error_notfound();
          return;
        }

        $this->view_data['edit_authority'] = $this->_authority_check($id , $editData);

      }

      //##################################################
      if (method_exists($this, '_init_index')) { $this->_init_index($id , $editData); }
      //##################################################

      //データ送信があれば
      if($this->input->post('submit_data')) {

        $validation = $this->_validation();

        if(!$validation) {
          //バリデーションエラー
          $this->view_data['error'] = validation_errors();
          $editData = $this->input->post();

        } else {  

          $db_dataset = $this->_db_dataset();
          
          //##################################################
          if (method_exists($this, '_before_data_save')) { $db_dataset = $this->_before_data_save($db_dataset); }
          //##################################################

          if(!empty($id)) {
            //DB更新
            $db_dataset['data']['id'] = $id;
          }

          try{
            //DB登録処理
            $data_id = $this->datalib->dataSave($db_dataset['data'] , $db_dataset['meta'] , $this->_userInfo('login_id'));
            $this->SearchTagModel->save();

            if($data_id) {
              //登録成功
              $this->session->set_flashdata('save_success', true);
              $this->session->set_flashdata('save_data_id', $data_id);
              //記事の登録状態をチェック
              $dataChk = $this->datalib->getData($data_id);
              if(isset($dataChk['status'])) {
                if($dataChk['status'] == 'wait') {
                  //wait :承認待ちで登録された場合
                  $this->session->set_flashdata('save_status' , 'wait');
                }
              }

              redirect(site_url(INDEX_CONTROLLER_PATH.'/complete') , 'location');

            } else {
              //エラー
              throw new Exception('Data Insert Error');
            }

          } catch(Exception $e) {
            $this->_error('登録時にエラーが発生しました。恐れ入りますが再度登録をお試し下さい'.'（'.$e->getMessage().'）');
            return;
          }
        }
      }

      $tab_setting = $this->_get_tabs_setting();
      $this->view_data['tab_html'] = $this->load->view('edit_form/parts/tab' , array('data' => $tab_setting) , true);
      if(count($tab_setting) > 1) {
        $this->view_data['tab_btm_html'] = $this->load->view('edit_form/parts/tab' , array('data' => $this->_get_tabs_setting() , 'tab_position' => 'bottom') , true);
      }

      if(empty($editData)) {
        $editData = array();
      }
      $this->view_data['content_html'] = $this->_convertDisplayHtml($editData , $this->view_data['error']);


      //読み込むjsファイル追加あれば
      if(!empty($this->editSetting['add_view']['js'])) {
        $this->view_data['add_js'] = $this->editSetting['add_view']['js'];
      }
      if(!empty($this->editSetting['add_view']['css'])) {
        $this->view_data['add_css'] = $this->editSetting['add_view']['css'];
      }

      //##################################################
      if (method_exists($this, '_before_render_edit_view')) { $this->_before_render_edit_view($id , $editData); }
      //##################################################

      //view表示
      $this->load->custom_view($this->dirname , 'edit/index' , $this->view_data);

      return;

    }

    /*
    登録完了画面
    */
    public function complete() {

      if(empty($this->session->flashdata('save_success'))) {
        redirect($this->view_data['list_page_url'] , 'location');
      }

      //##################################################
      if (method_exists($this, '_before_render_complete_view')) { $this->_before_render_complete_view(); }
      //##################################################

      $this->load->custom_view($this->dirname , 'edit/complete' , $this->view_data);

      return;
    }


    protected function _validation() {

        $this->load->library('form_validation');


        if(!empty($this->editSetting['items'])) :
          foreach($this->editSetting['items'] as $items) :
            if(!empty($items['item'])) :
              foreach($items['item'] as $item_key => $item_setting) :

                //validation rule set
                //------------------------------------------------------------
                $item_name = '';
                if(!empty($item_setting['meta'])) {
                  $item_name = '_meta_[';
                }
                $item_name .= $item_key;
                if(!empty($item_setting['meta'])) {
                  $item_name .= ']';
                }

                //------------------------------------------------------
                if($item_setting['type'] != 'form_group') :
                  //if($item_setting['type'] == 'checkbox') {
                  //type値にcheckboxという文字が含まれていれば
                  if(preg_match('/checkbox/',$item_setting['type'])) {
                    $item_name .= '[]';
                  }
                  
                  $this->form_validation->set_rules(
                    $item_name , 
                    $item_setting['label'] , 
                    (!empty($item_setting['validation'])?$item_setting['validation'] : '')
                  );

                else:

                  if($this->input->post($item_name) !== false) :
                    
                    $post_data = $this->input->post($item_name);

                    if(is_array($post_data['block_number']) && !empty($post_data['block_number'])) :
                      //$post_data[key][num][itemname]
                      //------------------------------------------------------
                      $cnt = 1;
                      foreach($post_data['block_number'] as $block_num) :
                        if(is_num($block_num)) :
                          foreach($item_setting['group'] as $group_item_key => $group_item_setting) :
                            $group_item_name = $item_name.'['.$block_num.']['.$group_item_key.']';
                            if(preg_match('/checkbox/',$group_item_setting['type'])) {
                              $group_item_name .= '[]';
                            }
                            if(isset($group_item_setting['validation'])) :
                              $this->form_validation->set_rules(
                                $group_item_name , 
                                //$item_setting['label'].' '.$cnt.'番目の'.$group_item_setting['label'] , 
                                $group_item_setting['label'] , 
                                (!empty($group_item_setting['validation'])?$group_item_setting['validation']:'')
                              );
                            endif;
                          endforeach;

                          $cnt++;
                        endif;
                      endforeach;
                      //------------------------------------------------------
                    endif;
                  endif;

                endif;
                //------------------------------------------------------

              endforeach;

            endif;
          endforeach;
        endif;


        return $this->form_validation->run();

    }

    protected function _db_dataset() {

      $dataset = ['data' => [] , 'meta'=>[]];

      if(!empty($this->editSetting['items'])) :
        foreach($this->editSetting['items'] as $items) :
          if(!empty($items['item'])) :
            foreach($items['item'] as $item_key => $item_setting) :
            
              if(isset($item_setting['meta']) && $item_setting['meta']) :
                //$dataset['meta'][$item_key] = $this->_toDBInsertData($this->input->post('_meta_['.$item_key.']') , $item_setting['type']);
                $dataset['meta'][$item_key] = array(
                  'value' => $this->_toDBInsertData($this->input->post('_meta_['.$item_key.']') , $item_setting['type']),
                  'type'  => $item_setting['type']
                );
              else:
                $dataset['data'][$item_key] = $this->_toDBInsertData($this->input->post($item_key) , $item_setting['type']);
              endif;

            endforeach;
          endif;

        endforeach;
      endif;

      return $dataset;
    }

    //DBに挿入するデータを項目タイプに合わせて変換する
    protected function _toDBInsertData( $data , $type ) {

      //if($type == 'checkbox') {
      if(preg_match('/checkbox/',$type)) {
        if(is_array($data)) {
          return implode(CATEGORY_DATA_DELIMITER , $data).CATEGORY_DATA_DELIMITER;
        } else {
          return $data.CATEGORY_DATA_DELIMITER;
        }

      //グループタイプのデータは、データ全体をシリアライズして保存
      } elseif($type == 'form_group') {

        $dbInsertData = array();
        if(!empty($data['block_number']) && is_array($data['block_number'])) {
          sort($data['block_number']); //numberの小さい順にソート
          $cnt = 1;
          foreach($data['block_number'] as $block_number) {
            if(is_num($block_number) && isset($data[$block_number])) {
              $dbInsertData[$cnt] = $data[$block_number];
              $cnt++;
            }
          }
        }
        return serialize_base64_encode($dbInsertData);
      }


      return $data;
    }

    //DBから取得して画面表示するデータを項目タイプに合わせて変換する
    protected function _toDisplayData( $data , $type ) {

      if(!empty($data)) {

        //if($type == 'checkbox') {
        if(preg_match('/checkbox/',$type)) {
          
          return fn_convertChkboxValue($data);

        //グループタイプのデータはシリアライズされている
        } elseif($type == 'form_group' && empty($data['block_number'])) {

          $displayData = array();
          $data = unserialize_base64_decode($data);
          if(!empty($data) && is_array($data)) {
            foreach($data as $key => $value) {
              $displayData['block_number'][] = $key;
              $displayData[$key] = $value;
            }
          }
          return $displayData;
        }
      }
      
      return $data;
    }

    //編集モードでDBから取得したデータを編集画面に表示させるための処理
    protected function _convertDisplayHtml( $editData  = array() , $error = '' ) {
      
      $html = '';

      if(!empty($this->editSetting['items'])) :
          $cnt = 1;

          foreach($this->editSetting['items'] as $tabKey => $items) :
            $data = array(
              'tab_active'  => (($cnt == 1) ? true : false),
              'tab_key'     => $tabKey
            );

            $html .= $this->load->view('edit_form/parts/container_top' , $data , true);
            
            if(!empty($items['item'])) :
              foreach($items['item'] as $item_key => $item_setting) :

                $item_name = '';
                if(!empty($item_setting['meta'])) {
                  $item_name = '_meta_[';
                }
                $item_name .= $item_key;
                if(!empty($item_setting['meta'])) {
                  $item_name .= ']';
                }


                if(isset($item_setting['data']) && !is_array($item_setting['data'])) {
                  //data(セレクトボックスやチェックボックス形式の選択肢データの指定が 文字列型の場合は その文字列を変数名として参照する)
                  if(!empty($this->{$item_setting['data']})) {
                    $item_setting['data'] = $this->{$item_setting['data']};
                  }
                }

                $targetEditData = array();
                //編集データが存在している場合
                if(!empty($item_setting['meta'])){
                  if(isset($editData['_meta_'][$item_key])) {
                    $targetEditData = $editData['_meta_'][$item_key];
                  }
                }else{
                  if(isset($editData[$item_key])) {
                    $targetEditData = $editData[$item_key];
                  }
                } 


                //----------------------------------------
                //form_group タイプ以外の項目の場合
                //----------------------------------------
                if($item_setting['type'] != 'form_group') :

                  $data = $item_setting;
                  $data['key'] = $item_name;
                  $data['value'] = '';
                  //編集データありの場合（URLにid指定がある、または入力->Errorのパターン）、value値をセット
                  if(!empty($targetEditData)) {
                    if(!$this->input->post('submit_data')) {
                     //id指定でDBからデータ取得して表示するパターンの場合
                      $data['value'] = $this->_toDisplayData($targetEditData , $item_setting['type']);
                    } else {
                      //入力->Errorのパターンの場合
                      $data['value'] = $targetEditData;
                    }
                  }

                  //エラーがあれば、そのエラー内容を取得
                  if(!empty($error)) {
                    $_item_name = $item_name;
                    //if($item_setting['type'] == 'checkbox') {
                    if(preg_match('/checkbox/',$item_setting['type'])) {
                      $_item_name .= '[]';
                    }
                    $data['error'] = form_error($_item_name , '<div><span class="error-message"><i class="icon-remove-sign"></i> ','</span></div>');
                  }

                  if($item_setting['type'] == 'link') {
                    $data['window_value'] = '';
                  } elseif ($item_setting['type'] == 'image') {
                    $data['image_id'] = (!empty($item_setting['meta']) ? 'meta_':'') . $item_key;
                  }

                  $html .= $this->load->view('edit_form/parts/form_'.$item_setting['type'] , array('data' => $data) , true);

                //----------------------------------------
                //form_group タイプの項目の場合
                //----------------------------------------
                else:

                  $group_blocks_html = '';

                  //編集データがある場合（入力->エラーありのパターン、もしくは編集モードでDBからデータ取得のパターン）
                  //または 最初のひとつは必須入力の場青（min_block_num = 1）の初期表示html生成

                  if($item_setting['min_block_num'] > 0 && empty($targetEditData)) {
                    //編集データがないが、min_block_num = 1だった場合、block_number 1 を入れて初期表示分のhtmlがひとつ生成されるようにする
                    $targetEditData['block_number'] = array(1);
                  }
                  if(!empty($targetEditData)) :
                    if(!$this->input->post('submit_data')) {
                      $targetEditData = $this->_toDisplayData($targetEditData , 'form_group');
                    }

                    if( !empty($targetEditData['block_number']) && is_array($targetEditData['block_number']) ) :

                      $block_cnt = 1;
                      foreach($targetEditData['block_number'] as $block_num) :
                        if(is_num($block_num)) :

                          $container_data = array(
                            'blockname'     => $item_key,
                            'blocklabel'     => (!empty($item_setting['blocklabel']) ? $item_setting['blocklabel'] : ''),
                            'block_number'  => $block_num,
                            'btn_delete'     => true
                          );
                          if($block_cnt == 1 && $item_setting['min_block_num'] != 0) {
                            $container_data['btn_delete'] = false;
                          }

                          $group_blocks_html .= $this->load->view('edit_form/parts/group_block_container_top' , $container_data , true);

                          foreach($item_setting['group'] as $group_item_key => $group_item_setting) :
                            $group_item_name = $item_name.'['.$block_num.']['.$group_item_key.']';

                            $data = $group_item_setting;
                            $data['key'] = $group_item_name;
                            $data['value'] = '';
                            if(isset($targetEditData[$block_num][$group_item_key])) {
                              $data['value'] = $targetEditData[$block_num][$group_item_key];
                            }

                            if($group_item_setting['type'] == 'link') {
                              $data['window_value'] = '';
                            } elseif ($group_item_setting['type'] == 'image') {
                              $data['image_id'] = $item_key.'_grp_'.$group_item_key.'_'.$block_num;
                            } elseif ($group_item_setting['type'] == 'editor') {
                              $data['editor_id'] = 'editor_grp_'.$item_key.'_'.$block_num;
                            }

                            //エラーがあれば、そのエラー内容を取得
                            if(!empty($error)) {
                              $_group_item_name = $group_item_name;
                              //if($group_item_setting['type'] == 'checkbox') {
                              if(preg_match('/checkbox/',$group_item_setting['type'])) {
                                $_group_item_name .= '[]';
                              }
                              $data['error'] = form_error($_group_item_name , '<div><span class="error-message"><i class="icon-remove-sign"></i> ','</span></div>');
                            }

                            $data['group_block'] = true;
                            
                            $group_blocks_html .= $this->load->view('edit_form/parts/form_'.$group_item_setting['type'] , array('data' => $data) , true);

                          endforeach;

                          $group_blocks_html .= $this->load->view('edit_form/parts/group_block_container_btm' , array() , true);


                          $block_cnt++;

                        endif;
                      endforeach;
                    endif;

                  endif;


                  //以下は追加ボタンで追加するテンプレートhtmlを生成する処理
                  //------------------------------------------------------
                  $group_blocks_tmpl_html = '';

                  $container_data = array(
                    'blockname'     => $item_key,
                    'blocklabel'     => (!empty($item_setting['blocklabel']) ? $item_setting['blocklabel'] : ''),
                    'block_number'  => '${block_num}',
                    'btn_delete'    => true
                  );

                  $group_blocks_tmpl_html .= $this->load->view('edit_form/parts/group_block_container_top' , $container_data , true);

                  foreach($item_setting['group'] as $group_item_key => $group_item_setting) :
                    $group_item_name = $item_name.'[${block_num}]['.$group_item_key.']';

                    $data = $group_item_setting;
                    $data['key'] = $group_item_name;
                    $data['value'] = '';

                    if($group_item_setting['type'] == 'link') {
                      $data['window_value'] = '';
                    } elseif ($group_item_setting['type'] == 'image') {
                      $data['image_id'] = $item_key.'_grp_'.$group_item_key.'_${block_num}';
                    } elseif ($group_item_setting['type'] == 'editor') {
                      $data['editor_id'] = 'editor_grp_'.$item_key.'_${block_num}';
                    }


                    $data['group_block'] = true;
                    $data['group_tmpl'] = true; // テンプレートモードの場合
                    
                    $group_blocks_tmpl_html .= $this->load->view('edit_form/parts/form_'.$group_item_setting['type'] , array('data' => $data) , true);


                  endforeach;

                  $group_blocks_tmpl_html .= $this->load->view('edit_form/parts/group_block_container_btm' , array() , true);

                  $data = $item_setting;
                  $data = array_merge( $data , array(
                    'key' => $item_key,
                    'group_blocks_html' => $group_blocks_html,
                    'group_blocks_tmpl_html' => $group_blocks_tmpl_html,
                    'add_button' => $item_setting['add_button'],
                    'max_block_num' => $item_setting['max_block_num'],
                    'min_block_num' => $item_setting['min_block_num']
                  ));

                  $html .= $this->load->view('edit_form/parts/form_group' , array('data' => $data) , true);


                endif;
                //------------------------------------------------------

              endforeach;

            endif;


            $data = array();
            $html .= $this->load->view('edit_form/parts/container_btm' , $data , true);


            $cnt++;
          endforeach;
        endif;


        return $html;
    }


    protected function _get_tabs_setting() {
      if(!empty($this->editSetting['tabs'])) {
        return $this->editSetting['tabs'];
      }

      return array();
    }

    //リンク先（サイト内）選択肢となるページのデータを取得
    protected function _get_linkpage_lists() {

      $link_pages = $this->config->item('data_name' , 'config_myapp');
      unset($link_pages['freepage']);
      unset($link_pages['accounts']);
      unset($link_pages['setting']);
      unset($link_pages['toppage']);
      foreach($link_pages as $key => $pages) {
        $link_pages[$key] = $pages.'一覧';
      }
      $link_pages = array_merge(array('0'=>'選択なし（外部URLを入力する）') , $link_pages);

      //freepageの登録があれば、それも選択肢に追加
      $this->load->model(array('freepage/freepage_data_model'));
      $time = fn_get_date();
      $search_where = "output_flag = 1";
      $data_cnt = $this->freepage_data_model->getDataCnt($search_where);
      if($data_cnt > 0) {
        $freepage_data = $this->freepage_data_model->getList(1 , $data_cnt , $search_where , array() , 'sort_num');
        foreach($freepage_data as $data) {
          $link_pages['fr/'.$data['id']] = 'フリーページ / '.$data['title'];
        }
      }

      return $link_pages;
    }

    //記事の編集権限をチェック
    protected function _authority_check($id , $editData) {

      if(!($this->auth->chk_controll_limit("publish_post") || (!$editData["output_flag"] && isset($editData["user_id"]) && $this->login_id == $editData["user_id"]))) {
         return false; //編集不可
      }

      return true; //編集可能

    }


    //配信開始日時が配信終了日時を超えていないか
    protected function _check_dateover() {
      if(!$this->form_Validation->error('distribution_start_date')) {
        if(strtotime($this->input->post("distribution_start_date")) >= strtotime($this->input->post("distribution_end_date"))) {
          $this->CI->form_validation->set_message('_check_dateover', '配信開始日時の指定が正しくありません');
          return FALSE;
        }
      }
      return TRUE;
    }
}
