<?php

class Admins_model extends MY_Model {

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
	}


  public function getList($page , $limit = 10 , $where = array() , $like = array()) {

    if($page < 1) {
      $page = 1;
    }
    if($limit < 1) {
      $limit = 1;
    }

    $result = array();

    $this->db->from($this->_table);

    if(!empty($where)) {
      $this->db->where($where);
    }

    if(!empty($like)) {
      $this->db->like($like);
    }

    $this->db->order_by('id' , 'DESC');

    if($limit) {
      $this->db->limit($limit , ($page-1)*$limit);
    }

    $query = $this->db->get();

    if (!empty($query) && $query->num_rows() > 0) {
      foreach ($query->result_array() as $row)
      {
        if(!empty($row['id']))
        {
          $result[$row['id']] = $row;
        }
      }
    }

    return $result;
  }


  /**
   * データ件数を返す
   *
   * @access  public
   */
  public function getDataCnt($where = array() , $like = array()) {

    $result = 0;

    $this->db->from($this->_table);
    
    if(!empty($where)) {
      $this->db->where($where);
    }

    if(!empty($like)) {
      $this->db->like($like);
    }

    $query = $this->db->get();
    if (!empty($query) && $query->num_rows() > 0) {
      return $query->num_rows();
    }

    return 0;
  }


  public function fetchAllbyAuthority($authority)
  {
      $sql = "SELECT * FROM ".$this->_table." WHERE authority = ?";
      $query = $this->db->query($sql, array($authority));
      if (!empty($query) && $query->num_rows() > 0) {
      	return $query->result_array();
      }
      return array();
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

  /**
   * ログインアカウントデータ1件取得（login_account値から）
   * @param string $login_account
   */
  public function fetchOneByLoginAccount($login_account)
  {
      $sql = "SELECT * FROM ".$this->_table." WHERE login_account = ?";
      $query = $this->db->query($sql, array($login_account));
      if (!empty($query) && $query->num_rows() > 0) {
      	return $query->row_array();
      }
      return false;
  }

  /**
   * ログインアカウントデータ1件取得（email値から）
   * @param string $email
   */
  public function fetchOneByEmail($email)
  {
      $sql = "SELECT * FROM ".$this->_table." WHERE email = ?";
      $query = $this->db->query($sql, array($email));
      if (!empty($query) && $query->num_rows() > 0) {
        return $query->row_array();
      }
      return false;
  }

  /**
   * パスワードリマインダ用
   * login_accountとemailで照合し、存在すればデータIDを返す
   *
   * @param string $login_account
   * @param string $email
   * @return mixed
   */
  public function checkAccount($login_account , $email)
  {

		$sql = "SELECT * FROM ".$this->_table." WHERE login_account = ? AND email = ?";
		$query = $this->db->query($sql , array($login_account , $email));
    if (!empty($query) && $query->num_rows() > 0) {
			$rs = $query->row_array();

	    if(!empty($rs["id"]))
	    {
	        return $rs["id"];
	    }
	  }

    return false;
  }

  /**
   * login_id値からをアカウント名を得る
   * @param number $login_id
   */
  public function getAccountName($login_id)
  {
      $result = $this->fetchOneByLoginID($login_id);
      if(!empty($result["login_account"]))
      {
          return $result["login_account"];
      }

      return "";
  }


  /**
  *  ログイン日時を書き込む
  */
  public function saveLastLoginData($login_id) {
  	$updata = array(
			'last_login' => fn_get_date()
		);
		$this->db->where('login_id', $login_id);
  	return $this->db->update($this->_table , $updata);
  }

}
