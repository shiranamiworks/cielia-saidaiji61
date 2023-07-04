<?php

class Admins_deleted_model extends MY_Model {

	public function __construct()
	{
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
	private function _init()
	{
    $this->_table = "admins_deleted";
	}

  /**
   * ログインアカウントデータ1件取得
   * @param string $login_id
   * @return object
   */
  public function fetchOneByLoginID($login_id)
  {
      $sql = "SELECT * FROM ".$this->_table." WHERE login_id = ?";
      $query = $this->db->query($sql, array($login_id));
      if (!empty($query) && $query->num_rows() > 0) {
      	return $query->row_array();
      }
      return false;
  }
}
