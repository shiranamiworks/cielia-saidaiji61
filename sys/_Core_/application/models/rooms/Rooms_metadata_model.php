<?php

require_once(APPPATH."/models/Metadata_model.php");

class Rooms_metadata_model extends Metadata_Model {

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
    $this->_table = "rooms_".TABLE_BASENAME_METADATA;
    $this->_table_category = "rooms_".TABLE_BASENAME_CATEGORY;
    $this->_table_data = "rooms_".TABLE_BASENAME_DATA;
	}

}
