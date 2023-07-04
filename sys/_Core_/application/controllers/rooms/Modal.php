<?php
/**
 * Created by PhpStorm.
 * User: ikumi
 * Date: 2019/02/06
 * Time: 22:17
 */
defined('BASEPATH') OR exit('No direct script access allowed');

require(dirname(__FILE__).'/../../core/controller/Front/Base_Controller.php');

class Modal extends Base_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->_init();
    }

    private function _init()
    {
        $this->load->model('rooms/Rooms_data_model', 'Data_model');
        $this->load->model('rooms/Rooms_metadata_model', 'MetaData_model');
        $this->load->model('rooms/Rooms_category_model', 'Category_model');
        $this->load->model('Search_tag_model', 'SearchTagModel');

        /*$this->load->library('datalib' , array(
            'data_model_name' => $this->dirname.'/'.$this->dirname.'_data_model',
            'metadata_model_name' => $this->dirname.'/'.$this->dirname.'_metadata_model',
            'category_model_name' => $this->dirname.'/'.$this->dirname.'_category_model',
        ));*/

        $this->load->library(array('auth' , 'urlsetting' , 'message' , 'adminslib' , 'torikago'));
        $this->config->load('config_torikago',true);
    }


    public function index()
    {
        $result = $this->Data_model->find($this->input->get('id'));
        $this->view_data['data'] = $result;
        $this->load->view('rooms/modal', $this->_view_esc($this->view_data));
    }
}