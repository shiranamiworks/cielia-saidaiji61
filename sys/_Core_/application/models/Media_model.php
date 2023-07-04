<?php

class Media_model extends \MY_Model {

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
    //テーブル名は機能別で変わってくるため（お知らせ用メディアテーブル（info_media）、イベント用メディアテーブル(event_media)など）
    //メディアテーブル名の定数がセットされていればそれを利用する
    //if(defined('MEDIA_TABLE_NAME')) {
    //  $this->_table = MEDIA_TABLE_NAME;
    //}
	}

  public function saveMediaData($upload_data) {

    $type = 'image';
    if(strtolower($upload_data['file_ext']) == '.pdf') {
      $type = 'pdf';
    } else if(strtolower($upload_data['file_ext']) == '.doc' || strtolower($upload_data['file_ext']) == '.docx') {
      $type = 'word';
    } else if(strtolower($upload_data['file_ext']) == '.xls' || strtolower($upload_data['file_ext']) == '.xlsx') {
      $type = 'excel';
    }

    $insert_data = array(
      'filename'    => $upload_data['file_name'],
      'upload_filename' => $upload_data['upload_filename'],
      'type'        => $type,
      'description' => $upload_data['description'],
      'created'     => fn_get_date()
    );

    return $this->db->insert($this->_table, $insert_data); 
  }

  public function getList($page , $limit , $where = array() , $keyword = "") {

    if($page < 1) {
      $page = 1;
    }
    if($limit < 1) {
      $limit = 1;
    }

    $result = array();

    $this->db->from($this->_table);

    if(!empty($where)) {
      foreach($where as $key => $val) {
        $this->db->where($key , $val);
      }
    }

    if(!empty($keyword)) {
      $this->db->like('description' , $keyword);
    }
    
    $this->db->order_by('created' , 'DESC');
    
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
  public function getDataCnt($where = array() , $keyword = "")
  {

    $result = 0;

    $this->db->from($this->_table);
    
    if(!empty($where)) {
      foreach($where as $key => $val) {
        $this->db->where($key , $val);
      }
    }

    if(!empty($keyword)) {
      $this->db->like('description' , $keyword);
    }
    $query = $this->db->get();

    return $query->num_rows();

  }
}
