<?php

require_once(APPPATH."/models/Data_model.php");

class Rooms_data_model extends Data_Model {

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
    $this->_table = "rooms_".TABLE_BASENAME_DATA;
    $this->_table_category = "rooms_".TABLE_BASENAME_CATEGORY;
    $this->_table_metadata = "rooms_".TABLE_BASENAME_METADATA;
	}

}
