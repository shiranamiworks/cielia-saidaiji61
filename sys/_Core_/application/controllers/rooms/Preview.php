<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require(dirname(__FILE__).'/../../core/controller/Admin/Preview_Controller.php');

class Preview extends Preview_Controller {

	public function __construct(){
		parent::__construct();

    $this->session->unset_userdata('preview_setting_data');
    $this->_set_session_preview_data($_POST);

	}

  private function _orig_setting() {
  }

}
