<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require(dirname(__FILE__).'/../../core/controller/Admin/Publish_Controller.php');

class Publish extends Publish_Controller {


	public function __construct()
	{
        //認証失敗時に表示するViewファイルを指定
        define('LOGOUT_REDIRECT_VIEWFILE' , 'common/publish/logout_error');
		parent::__construct();
	}

}
