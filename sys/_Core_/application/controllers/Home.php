<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require(dirname(__FILE__).'/../core/controller/Admin/Base_Controller.php');

class Home extends Base_Controller {


	public function __construct() {
		parent::__construct();
		$this->_init();
	}

	private function _init() {
		define('ADMINPAGE_NAME' , '管理画面ホーム');
	}

	public function index() {

		redirect('rooms/lists' , 'location');
		return;

	}

}
