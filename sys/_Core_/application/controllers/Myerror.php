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

class Myerror extends MY_Controller {


  public function __construct()
  {
    parent::__construct();
    $this->init();
  }

  private function init() {
  }

  public function frontview404() {
    
    $this->output->set_status_header('404');
    $this->load->library(array('frontviewlib'));
    $this->frontviewlib->set_view_dir('error' , VIEW_TMPL_PATH);
    return $this->load->view('index' , $this->view_data);

  }
}
