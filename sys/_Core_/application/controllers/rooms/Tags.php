<?php
/**
 * Created by PhpStorm.
 * User: ikumi
 * Date: 2019/02/06
 * Time: 22:17
 */
defined('BASEPATH') OR exit('No direct script access allowed');

require(dirname(__FILE__).'/../../core/controller/Front/Base_Controller.php');

class Tags extends Base_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->_init();
    }

    private function _init()
    {
        $this->load->model('Search_tag_model', 'SearchTagModel');
        $this->load->library(array('auth' , 'urlsetting' , 'message' , 'adminslib' , 'torikago'));
    }


    public function index() {

        $this->config->load('config_torikago',true);
        $Tags = $this->SearchTagModel->getTags();

        $this->view_data = [
            'Tags' => $Tags
        ];

        $this->load->view('rooms/tags' , $this->_view_esc($this->view_data));

        return;
    }
}