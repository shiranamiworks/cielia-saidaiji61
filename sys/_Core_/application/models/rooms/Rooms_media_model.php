<?php
require_once(APPPATH."/models/Media_model.php");

class Rooms_media_model extends Media_Model {

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
    $this->_table = "rooms_".TABLE_BASENAME_MEDIA;
	}

}
