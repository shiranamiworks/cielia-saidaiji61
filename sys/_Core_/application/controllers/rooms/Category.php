<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require(dirname(__FILE__).'/../../core/controller/Admin/Category_Controller.php');

class Category extends Category_Controller {

	public function __construct() {
    $this->adminpage_title = '物件管理 カテゴリ';

		parent::__construct();
	}

}
