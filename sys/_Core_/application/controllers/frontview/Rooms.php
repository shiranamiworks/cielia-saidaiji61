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
defined('BASEPATH') OR exit('No direct script access allowed');

require(dirname(__FILE__).'/../../core/controller/Front/Base_Controller.php');

class Rooms extends Base_Controller {

  protected $sort_order_key = 'distribution_start_date';
  protected $sort_order_type = 'DESC';

  protected $category_list = [];

  protected $page_title;

	public function __construct()
	{
		parent::__construct();
    $this->init();
	}

  private function init() {

      $this->load->library('datalib' , array(
        'data_model_name' => 'rooms/rooms_data_model',
        'metadata_model_name' => 'rooms/rooms_metadata_model',
        'category_model_name' => 'rooms/rooms_category_model'
      ));

      $this->load->library(array('torikago'));
      $this->config->load('config_torikago',true);

      $search_where = $this->search_where_base;

      $this->category_list = $this->datalib->get_category_list();
      $this->view_data['category_list'] = $this->category_list;

      $this->frontviewlib->set_view_dir('rooms' , VIEW_TMPL_PATH );
	}

  public function index() {

    $search_where = $this->search_where_base;
      $search_like = '';

    $params = $this->input->get();
    if (count($params) > 0) {
        foreach ($params as $key => $values) {
            if (is_array($values) && count($values) > 0) {
                $tagWhere = '';
                foreach ($values as $v) {
                    switch ($key) {
                        case 'free_tag':
                            $tagWhere .= ' OR ' . $key . ' LIKE \'%' . $v . '%\'';
                            break;
                        case 'breadth':
                            $tagWhere .= ' OR (' . $key . ' >= ' . $v . ' AND ' . $key . ' <= ' . (intval($v) + 9.99) . ')';
                            break;
                        case 'price':
                            $tagWhere .= ' OR (' . $key . ' >= ' . $v . ' AND ' . $key . ' <= ' . (intval($v) + 999) . ')';
                            break;
                        default:
                            $tagWhere .= ' OR ' . $key . ' = "' . $v . '"';
                            break;
                    }
                }
                    $tagWhere = '(' . substr($tagWhere, 4) . ')';
                    $search_where .= ' AND ' . $tagWhere;
            }
        }
    }

    //データリスト取得
    $rs = $this->datalib->get_list(
      1 , 
      999, 
      $search_where , 
      $search_like , 
      $this->sort_order_key , 
      $this->sort_order_type
    );

//      echo $this->db->last_query();

    $data_list = array();
    if($rs) {
      foreach($rs as $val) {
        $data_list[$val['edit_id']] = $val;
      }
    }

    $this->view_data = array_merge($this->view_data , array(
      'page' => 1,
      'simulation_link_url' => $this->config->item('simulation_link_url' , 'config_torikago'),
      'simulation_link_window' => $this->config->item('simulation_link_window' , 'config_torikago'),
      'simulation_window_size' => $this->config->item('simulation_window_size' , 'config_torikago'),
      'torikago_html' => $this->torikago->getTemplateHtmlFront($data_list)
    ));

    $this->frontviewlib->view('lists' , $this->view_data);
    return;
  }
}
