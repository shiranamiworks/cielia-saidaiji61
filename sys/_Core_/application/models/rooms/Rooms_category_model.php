<?php

require_once(APPPATH."/models/Category_model.php");

class Rooms_category_model extends Category_Model {

	public function __construct(){
		parent::__construct();
		$this->_init();
	}

	/**
	 * 初期化関数
	 *
	 * @access	private
	 * @param
	 * @return
	 */
	private function _init() {
    $this->_table = "rooms_".TABLE_BASENAME_CATEGORY;
	}


}
