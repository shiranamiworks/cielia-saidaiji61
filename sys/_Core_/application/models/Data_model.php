<?php

class Data_model extends MY_Model {

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
   * カテゴリごとに何件のデータがあるかを取得
   * @param string $where
   * @param array $param
   * @return array
   */
  public function getnum_category_by($where = array()) {

    $result = array();

    if(!empty($where)) {
      $this->db->where($where);
    }
    $this->db->select('category , count(id) as cnt');
    $this->db->group_by("category");
    $this->db->order_by('category' , 'asc');

    $query = $this->db->get($this->_table); 

    if (!empty($query) && $query->num_rows() > 0) {
      foreach ($query->result_array() as $row)
      {
        if(!empty($row['category']))
        {
          $result[$row['category']] = $row['cnt'];
        }
      }
    }

    return $result;

  }

  /**
   * 指定したアカウントIDを持つ記事をすべて任意のアカウントIDに変更する
   * （アカウント管理画面でアカウント削除を行った場合にそのアカウントで記事が作成されていた場合の対処）
   * @param number $target_id
   * @param number $change_id
   * @return number 
   */
  function change_user_id($target_id , $change_id = 1) {
    $updata = array(
      'user_id' => $change_id
    );

    $this->db->where('user_id' , $target_id);
    return $this->db->update($this->_table , $updata);
  }
  
  /**
   * データのカテゴリを一括変更させる（カテゴリ削除処理の際、すべてcategory=1に変更する処理で使う）
   * @param number $target_category 変更対象カテゴリID
   * @param number $change_category 変更後のカテゴリID
   * @return boolean 
   */
  function data_category_change($target_category , $change_category) {
      $updata = array(
        'category' => $change_category
      );
      $this->db->where('category' , $target_category);
      return $this->db->update($this->_table , $updata);
  }

  public function getList($page , $limit , $where = array() , $like = array() , $order_key = 'distribution_start_date' , $order_type = 'DESC') {

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

    $this->db->order_by($order_key , $order_type);
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


  /**
   * 承認待ちのデータ件数を取得する
   * 
   * @return number
   */
  public function data_cnt_waiting()
  {
      return $this->getDataCnt(array('status' => 'wait'));
  }


  /**
   * 登録データのうち、最大のsort_num値を返す
   * 
   * @return number
   */
  public function getMaxSortNum() {
    $this->db->select_max('sort_num', 'max_sort_num');
    $query = $this->db->get($this->_table);

    if(!empty($query)) {
      $result = $query->row_array();
      if(!empty($result['max_sort_num'])) {
        return $result['max_sort_num'];
      }
    }

    return 0;
  }


  /**
   * 登録データのうち、最小のsort_num値を返す
   * 
   * @return number
   */
  public function getMinSortNum() {
    $this->db->select_min('sort_num', 'min_sort_num');
    $this->db->where('sort_num >=' , 0);
    $query = $this->db->get($this->_table);

    if(!empty($query)) {
      $result = $query->row_array();
      if(!empty($result['min_sort_num'])) {
        return $result['min_sort_num'];
      }
    }

    return 0;
  }

  /*
   * 条件を付けて一件だけデータ取得
   */
  public function getWhereOne($where) {
    $query = $this->db->where($where)->get($this->_table);
    if (!empty($query) && $query->num_rows() > 0) {
        return $query->row_array();
    }
    return array();
  }

  /*
   * ソートNoを入れ替える処理
   */
  public function sortNumChange($id , $type = 'up' , $where = array() , $sort_order_type = 'ASC') {

    $t_id = '';

    if(is_num($id)) {

      $data = $this->find($id);

      if(!empty($data)) {

        if($type == 'up') {
          if($sort_order_type == 'ASC') {
            //次の小さいソートNoを持つデータを探す
            $where['sort_num <'] = $data['sort_num'];
            $this->db->where($where);
            $this->db->order_by('sort_num' , 'DESC');
          } else {
            //$sort_order_type == 'DESC' の場合
            //次の大きいソートNoを持つデータを探す
            $where['sort_num >'] = $data['sort_num'];
            $this->db->where($where);
            $this->db->order_by('sort_num' , 'ASC');
          }
          
        
        } else {
          if($sort_order_type == 'ASC') {
            //次の大きいソートNoを持つデータを探す
            $where['sort_num >'] = $data['sort_num'];
            $this->db->where($where);
            $this->db->order_by('sort_num' , 'ASC');
          } else {
            //$sort_order_type == 'DESC' の場合
            //次の小さいソートNoを持つデータを探す
            $where['sort_num <'] = $data['sort_num'];
            $this->db->where($where);
            $this->db->order_by('sort_num' , 'DESC');
          }
        }

        $this->db->limit(1);
        $query = $this->db->get($this->_table);
        if(!empty($query) && $query->num_rows() > 0) {
          $change_data = $query->row_array();
        }

        //ソート順入れ替え対象となるデータがあれば
        if(!empty($change_data)) {

          //トランザクションスタート
          $this->db->trans_start();

          //sort_numの入れ替え
          $updata1 = array(
            'sort_num' => $change_data['sort_num'],
            'modified' => fn_get_date(),
          );
          $this->db->where('id' , $data['id']);
          $this->db->update($this->_table , $updata1);

          $updata2 = array(
            'sort_num' => $data['sort_num'],
            'modified' => fn_get_date(),
          );
          $this->db->where('id' , $change_data['id']);
          $this->db->update($this->_table , $updata2);

          $this->db->trans_complete();

          $t_id = $data['id'];
        }
      }
    }

    return $t_id;
  }


}
