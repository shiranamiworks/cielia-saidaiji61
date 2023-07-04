<?php

class Metadata_model extends MY_Model {

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



  public function fetchAllbyDataID($data_id)
  {
      $sql = "SELECT * FROM ".$this->_table." WHERE data_id = ?";
      $query = $this->db->query($sql, array($data_id));
      if (!empty($query) && $query->num_rows() > 0) {
        $result = $query->result_array();
        $return_data = array();
        foreach($result as $value) {
          if(isset($value['meta_key'])) {
            $return_data[$value['meta_key']] = $value;
          }
        }

        return $return_data;
      }
      return array();
  }

  public function fetchOnebyDataIDAndMetakey($data_id , $meta_key)
  {
      $sql = "SELECT * FROM ".$this->_table." WHERE data_id = ? AND meta_key = ? LIMIT 1";
      $query = $this->db->query($sql, array($data_id , $meta_key));
      if (!empty($query) && $query->num_rows() > 0) {
        return $query->row_array();
      }
      return array();
  }

}
