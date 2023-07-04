<?php
/*
======================================================================
Project Name    : Mishima T.CMS  
 
Copyright © <2016> Teruhiko Mishima All rights reserved.
 
This source code or any portion thereof must not be  
reproduced or used in any manner whatsoever.
本ソースコードを無断で転用・転載することを禁じます。
======================================================================
*/
/**
 * MY_Model
 */
class MY_Model extends CI_Model {

    /**
     * table name
     *
     * @var string
     */
    protected $_table;

    /**
     * constructor
     */
    public function __construct() {
        parent::__construct();
        
		$this->config->load('config_myapp', TRUE);

        $clazz = get_class($this);
        $this->_table = strtolower(substr($clazz, 0, strpos($clazz, '_')));
    }


    /**
     * insert
     *
     * @return integer
     */
    public function insert($data) {

        $ret = $this->db->insert($this->_table, $data);
        if ($ret === FALSE) {
            return FALSE;
        }
        return $this->db->insert_id();
    }

    /**
     * update
     *
     * @param integer|string $id
     */
    public function update($data = null , $where = array() , $id_keyname = 'id') {
        if ($data === null) {
            $data = $this;
        }
        $this->db->update($this->_table, $data, $where);
        if($this->db->affected_rows() > 0) {
            if(isset($where[$id_keyname])) {
                return $where[$id_keyname];
            }

            return true;
        }

        return false;
    }
    
    public function replaceData($data , $where = array()) {
        if(!empty($where)) {
            $this->db->where($where);
            $query = $this->db->get($this->_table);
        
            if (!empty($query) && $query->num_rows() > 0) {
                //データが存在していればupdateを実行
                $this->db->where($where);
                return $this->update($data , $where);
            }
        }

        //そうでなければinsert
        return $this->insert($data);
        
    }

    /**
     * delete
     *
     * @param integer|strng $id
     */
    public function delete($id , $where_key = "id") {
        $this->db->delete($this->_table, array($where_key => $id));
    }

    /**
     * delete
     *
     * @param object $where
     */
    public function deleteWhere($where = array()) {
        $this->db->where($where);
        $this->db->delete($this->_table);
    }


    /**
     * find_all
     *
     * @return array
     */
    public function find_all() {
        $query = $this->db->get($this->_table);
        if (!empty($query) && $query->num_rows() > 0) {
            return $query->result_array();
        }
        return array();
    }

    /**
     * find_list
     *
     * @param  integer|string $limit
     * @return array
     */
    public function find_list($limit = 10) {
        $query = $this->db->limit($limit)->order_by('id')->get($this->_table);
        if (!empty($query) && $query->num_rows() > 0) {
            return $query->result_array();
        }
        return array();
    }

    /**
     * find
     *
     * @param  integer|string $id
     * @return stdClass
     */
    public function find($id , $id_keyname = 'id') {
        $query = $this->db->where(array($id_keyname => $id))->get($this->_table);
        if (!empty($query) && $query->num_rows() > 0) {
            return $query->row_array();
        }
        return array();
    }


	/*
	 *  渡されたパラメータをセットする
	 *  @access	private
	 *  @param	object $param
	 *  @return	boolean
	 */
	protected function _set_query_param($param)
	{

		//WHERE , LIKE
		if($param && !empty($param['where']))
		{
			$this->db->where($param['where']);
		}
		if($param && !empty($param['like']))
		{
			$this->db->like($param['like']);
		}
        if($param && !empty($param['where_in']) && is_array($param['where_in']))
        {
            foreach($param['where_in'] as $key => $val)
            {
                $this->db->where_in($key , $val);
            }
        }

		//ORDER BY
		if(!empty($param['order_by']))
		{
			$this->db->order_by($param['order_by']);
		}

		//LIMIT , OFFSET
		if($param && !empty($param['limit']) && !empty($param['offset']))
		{
			$this->db->limit($param['limit']  , $param['offset']);
		}
		else if($param && !empty($param['limit']))
		{
			$this->db->limit($param['limit']);
		}

		return;

	}

    /**
     * now
     *
     * @return string
     */
    public function now() {
        return date('Y-m-d H:i:s');
    }


    public function _dbg_query()
    {
        echo $this->db->last_query();exit;
    }

    public function output_error( $message  , $errcode = '')
    {
        if(!empty($_GET["format"]))
        {
            $format = $_GET["format"];
        }

        if(!empty($this->_auth_data["name"])) //認証済みなら xml か jsonでエラー出力
        {
            if($format == 'json')
            {
                fn_show_error_json( $message  , $errcode , 'HTTP/1.0 503 Service unavailable.' );
            }
            else
            {
                fn_show_error_xml( $message  , $errcode , 'HTTP/1.0 503 Service unavailable.' );
            }
        }
        else
        {
            show_error( $message." / ".$errcode , 503);
        }

        exit;
    }
}