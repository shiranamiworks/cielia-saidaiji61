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

class Preview_Controller extends Base_Controller
{

  public function __construct() {
      parent::__construct();
      $this->_init();
  }

  private function _init() {
  }

  public function index() {

    $this->session->unset_userdata('client_account');

    if(!empty($_GET['url'])) {
      $client_site_url = $this->_clientsInfo('site_url');
      $client_site_url_parts = parse_url($client_site_url);
      $client_account = $this->_clientsInfo('account');
      $this->session->set_userdata('client_account' , $client_account);

      $access_url_parts = parse_url($_GET['url']);
      if(!empty($client_site_url_parts['host']) && !empty($access_url_parts['host']) && 
         ($access_url_parts['host'] == $client_site_url_parts['host'] || $access_url_parts['host'] == $_SERVER['HTTP_HOST'])) {
        $path = (!empty($access_url_parts['path']) ? $access_url_parts['path'] : '/top');
        $path .= '?mode=preview';
        $path .= (!empty($access_url_parts['query']) ? '&'.$access_url_parts['query'] : '');
        $path = 'frontview'.$path;
      } else {
        $path = $_GET['url'];
      }
      $this->view_data['iframe_url'] = site_url($path);

      $this->session->set_flashdata('preview_mode' , true);

      $this->view_data['client_site_url'] = $client_site_url;

      $this->load->custom_view('' , 'preview/index' , $this->view_data);
    }
    
    return;
  }

  protected function _set_session_preview_data($post_data) {
    if(!empty($post_data)) {
      $this->session->set_flashdata('preview_data' , $post_data);
    }
    
    return;
  }

}
