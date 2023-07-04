<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Logout extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->_init();
	}

	private function _init() {
		$this->load->library(array('auth'));
		
	}


	public function index() {

		$this->auth->logout();
		redirect(site_url('login') , 'location');

	}


}
