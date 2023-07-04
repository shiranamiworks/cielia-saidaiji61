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
require_once APPPATH.'third_party/phpQuery-onefile.php';

class  Torikago {

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
    $this->CI->config->load('config_torikago' , true);
    $this->CI->load->model('rooms/Rooms_data_model' , 'Rooms_model');
    $this->CI->load->model('rooms/Rooms_metadata_model' , 'Rooms_metadata_model');
    $this->CI->load->model('rooms/Rooms_category_model' , 'Rooms_category_model');
    $this->CI->load->library(array('auth' , 'form_validation' , 'message' , 'parser'));
		$this->CI->load->helper(array('string','url' , 'common'));


	}

  /**
   * 指定したDataIDのデータを取得（metaデータも含めて）
   * @param number $data_id
   */
  function getData($data_id)
  {

      $data = $this->CI->Rooms_model->find($data_id);
      $meta_data = $this->CI->Rooms_metadata_model->fetchAllbyDataID($data_id);
      
      if(!empty($meta_data))
      {
          foreach($meta_data as $meta)
          {
              $data["_meta_"][$meta["meta_key"]] = $meta["meta_value"];
          }
      }
      
      return $data;
  }


  public function getTemplateDir() {
    return $this->CI->config->item('torikago_template_path','config_myapp');
  } 
  public function getTemplateFile() {
    $templateDir = $this->getTemplateDir();
    return $templateDir.'torikago.html';
  } 
  private function _getTemplateHtmlDoc() {
    $filepath = $this->getTemplateFile();
    $html = file_get_contents($filepath);
    if(!$html) {
      return false;
    }

    return phpQuery::newDocument($html);
  }
  private function _chkTemplate($doc) {

    $idArr = array();
    $errors = array();
    foreach ($doc["#torikago"]->find("td.edit") as $edit_td){
      $id = pq($edit_td)->attr('id');
      if(!empty($idArr[$id])) {
        $errors[] = $id;
      }else{
        $idArr[$id] = 1;
      }
    }

    return array('result' => !empty($errors) ? false : true ,'errors' => $errors);
  }


  public function getTemplateHtmlAdmin($dbData) {

    $doc = $this->_getTemplateHtmlDoc();

    if(!$doc) {
      return array('error' => 'テンプレートを取得できません');
    }

    $chkTemplate = $this->_chkTemplate($doc);

    if($chkTemplate['result'] === false) {
      return array('error' => 'ID重複エラー：'.implode($chkTemplate['errors'], ','));
    }

    $categoryList = $this->getCategoryList();
    $classNameList = $this->CI->config->item('torikago_table_css_classname' , 'config_torikago');
    $outputTagList = $this->CI->config->item('torikago_table_output_tag' , 'config_torikago');

    foreach ($doc["#torikago"]->find("td.edit") as $editTD){
      $tid = pq($editTD)->attr('id');
      $setParam = array();
      $partsTemplate = '<p>　</p><p class="price">　</p>';
      if(!empty($tid) && !empty($dbData[$tid])){
        $dbData[$tid] = fn_esc( $dbData[$tid] );

        $setParam = array(
          'id'      => $dbData[$tid]['id'],
          'room_number'   => $dbData[$tid]['title'] !== '' ? $dbData[$tid]['title'] : '　',
          'category' => $dbData[$tid]['category'],
          'type' => nl2br( $dbData[$tid]['type_str'] ),
          'status' => $dbData[$tid]['status_str'] !== '' ? nl2br( $dbData[$tid]['status_str']) : '　',
          'price'   => $dbData[$tid]['price'] !== '' ? $dbData[$tid]['price']. $this->_get_price_unit( $dbData[$tid] )  : '　'
        );

        if(isset($outputTagList[$setParam['category']])) {
          $partsTemplate = $outputTagList[$setParam['category']];
        }
      }
      $parts = $this->CI->parser->parse_string( $partsTemplate , $setParam ,TRUE);
      //pq($editTD)->append($parts);
      $href = 'edit/'.(!empty($setParam['id']) ? $setParam['id'].'/' : '?edit_id='.fn_esc($tid));
      
      $editLink = pq('<a>');
      $editLink->attr('href' , $href);

      $class = '';
      $tooltipContent = array();
      //すでに登録済みデータの場合、ツールチップ設定など
      if(!empty($setParam['id'])) {
        
        $tooltipContent[] = '部屋番号：'.$setParam['room_number'];
        $tooltipContent[] = '価格：'.$setParam['price'];

        if(isset($setParam['category']) && isset($categoryList[ $setParam['category'] ])) {
          $editLink->attr('data-category' , $setParam['category']);
          $tooltipContent[] = '表示タイプ：'.fn_esc($categoryList[ $setParam['category'] ]);
          if(isset($classNameList[ $setParam['category'] ])) {
            $class = $classNameList[ $setParam['category'] ];
          }
        }

        if(!empty($dbData[$tid]['modified']) && $dbData[$tid]['modified'] != '0000-00-00 00:00:00') {
          $tooltipContent[] = '最終更新：'.date('Y-m-d',strtotime($dbData[$tid]['modified']));
        }

        if(!empty($tooltipContent)) {
          $editLink->attr('title' , implode($tooltipContent , '<br>'));
          $editLink->attr('data-html' , 'true');
          $editLink->attr('data-toggle' , 'tooltip');
        }
      }

      $editLink->html($parts);
      pq($editTD)->append($editLink);
      if($class) {
        pq($editTD)->addClass($class);
      }elseif(!empty($setParam['id'])){
        pq($editTD)->removeClass('edit');
      }
    }

    return $doc;
  }


  public function getTemplateHtmlFront($dbData) {

    $doc = $this->_getTemplateHtmlDoc();

    if(!$doc) {
      return '<p class="error">テンプレートを取得できません</p>';
    }

    $chkTemplate = $this->_chkTemplate($doc);

    if($chkTemplate['result'] === false) {
      return '<p class="error">テンプレートにエラーがあります</p>';
    }

    $categoryList = $this->getCategoryList();
    $classNameList = $this->CI->config->item('torikago_table_css_classname' , 'config_torikago');
    $outputTagList = $this->CI->config->item('torikago_table_output_tag' , 'config_torikago');
    
    $simuEnableTypeArr = $this->CI->config->item('simulation_enable_type' , 'config_torikago');
    $simuBtnTag = $this->CI->config->item('simulation_btn_tag' , 'config_torikago');
    $furnitureBtnTag = $this->CI->config->item('furniture_btn_tag' , 'config_torikago');

    $linkDisableTypeArr = $this->CI->config->item('link_disable_type' , 'config_torikago');

    foreach ($doc["#torikago"]->find("td.edit") as $editTD){
      $tid = pq($editTD)->attr('id');
      $setParam = array();
      $partsTemplate = '<p>　</p><p class="price">　</p>';
      if(!empty($tid) && !empty($dbData[$tid])){

        $dbData[$tid] = fn_esc( $dbData[$tid] );

        $setParam = array(
          'id'      => $dbData[$tid]['id'],
          'room_number'   => $dbData[$tid]['title'] !== '' ? $dbData[$tid]['title'] : '　',
          'category' => $dbData[$tid]['category'],
          'type' => $dbData[$tid]['type_str'] !== '' ? nl2br($dbData[$tid]['type_str']) : '　',
          'status' => $dbData[$tid]['status_str'] !== '' ? nl2br($dbData[$tid]['status_str']) : '　',
          'price'   => $dbData[$tid]['price'] !== '' ? number_format( $dbData[$tid]['price'] ) . $this->_get_price_unit( $dbData[$tid] ) : '　',
          'link'    => $dbData[$tid]['link'],
            'modalUrl' => '/m/saidaiji61/sys/output/rooms/modal/?id=' . $dbData[$tid]['id'] . '&room=' . $dbData[$tid]['title']
        );

        if(isset($outputTagList[$setParam['category']])) {
          $partsTemplate = $outputTagList[$setParam['category']];
        }
      }

      $roomOutputTag = $this->CI->parser->parse_string( $partsTemplate , $setParam ,TRUE);

      $class = !empty($classNameList['99']) ? $classNameList['99'] : '';
      $simulatorLinkParam = array();
      $linkDisable = false;

      if(!empty($tid) && !empty($dbData[$tid])) {

        if(isset($dbData[$tid]['title']) && $dbData[$tid]['title'] !== '') {
          $simulatorLinkParam['heya'] = $dbData[$tid]['title'];
        }

        if(isset($dbData[$tid]['category']) && isset($categoryList[ $dbData[$tid]['category'] ])) {
          pq($editTD)->attr('data-category' , $dbData[$tid]['category']);
          if(isset($classNameList[ $dbData[$tid]['category'] ])) {
            $class = $classNameList[ $dbData[$tid]['category'] ];
          }
          if(in_array($dbData[$tid]['category'] , $linkDisableTypeArr)) {
            $linkDisable = true;
          }
        }

        if(isset($dbData[$tid]['price']) && $dbData[$tid]['price'] !== '') {
          $simulatorLinkParam['kakaku'] = $dbData[$tid]['price'];
          $simulatorLinkParam['lk'] = '1';
        }

      }

      $tagContainer = pq('<div>');
      $tagContainer->addClass('td-con');
      $tagInner = '';

      if(!empty($setParam['modalUrl']) && $linkDisable === false) {

        $tagInner = pq('<a>');
        $tagInner->attr('href' , $setParam['modalUrl']);
        $tagInner->attr('class', 'btn-openWindow');
//        $tagInner->attr('target' , '_blank');

        pq($editTD)->addClass('haslink');

      }else{

        $tagInner = pq('<div>');
        if(!empty($class)) {
          $tagInner->addClass( str_replace(' ' , '' , $class) .'-inner' );
        }
        $tagInner->addClass( 'td-inner' );

      }

      $tagInner->html($roomOutputTag);
      $tagContainer->html($tagInner);
      
      //計算ボタン追加
      if(!empty($setParam['category']) && in_array( $setParam['category'] , $simuEnableTypeArr ) ) {
          $_btnTag = $this->CI->parser->parse_string( $simuBtnTag , array() ,TRUE);
          $_btnTag = pq($_btnTag);
          $_btnTag->find('button')->attr('data-param' , http_build_query( $simulatorLinkParam ));
          $tagContainer->append($_btnTag);
          pq($editTD)->addClass('has-simu-btn');
      }

        if(!empty($setParam['category']) && in_array( $setParam['category'] , $simuEnableTypeArr ) ) {
            if (!empty($setParam['link'])) {
                $_btnTag = $this->CI->parser->parse_string( $furnitureBtnTag , array() ,TRUE);
                $_btnTag = pq($_btnTag);
                $_btnTag->find('a')->attr('href' , $setParam['link']);
                $tagContainer->append($_btnTag);
//          pq($editTD)->addClass('has-simu-btn');
            }
        }

      pq($editTD)->append($tagContainer);

      pq($editTD)->removeClass('edit');
      if($class) {
        pq($editTD)->addClass($class);
      }
    }

    return $doc;
  }


  /**
   * カテゴリリストを取得
   *
   * @return object
   */
  function getCategoryList()
  {
      $result = $this->CI->Rooms_category_model->find_all();

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

  private function _get_price_unit($dbData) {
    if(!empty($dbData['pricetype']) && $dbData['pricetype'] == '2') {
      return '万円台';
    }

    return '万円';
  }
}
