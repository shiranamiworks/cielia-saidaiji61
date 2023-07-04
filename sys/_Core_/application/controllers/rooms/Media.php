<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require(dirname(__FILE__).'/../../core/controller/Admin/Media_Controller.php');

class Media extends Media_Controller {

	public function __construct() {
    $this->adminpage_title = '物件 ファイル管理';

		parent::__construct();
	}

}
