<?php

class Clients_model extends MY_Model {

	public function __construct() {
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
	}


	/**
	 * 指定したKEYの値を返す
	 *
	 * @access	public
	 * @param	string
	 * @return	array
	 */
	public function getValue($key) {

		$result = array();

		$this->db->where('setting_key' , $key);
		$this->db->limit(1);
		$query = $this->db->get($this->_table);

		if(!empty($query) && $query->num_rows() > 0)
		{
			$result = $query->row_array();
			if(isset($result['setting_value'])) {
				return $result['setting_value'];
			}
		}

		return "";

	}

}
