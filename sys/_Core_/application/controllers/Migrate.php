<?php
defined('BASEPATH') OR exit('No direct script access allowed');
defined('ADMINTOOL_FLG') OR exit('No direct script access allowed');

class Migrate extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->_init();
	}

	private function _init() {
    $this->load->database('default');
	}


	public function update181005_db_addcolum_to_rooms_data() {

    $this->load->dbforge();

    $tables = $this->db->list_tables();
    if(empty($tables)) {
      die('テーブルが作成されていません（ERR101）');
      return;
    }

    //20181005
    if (!$this->db->table_exists('rooms_data')) {
      die('rooms_dataテーブルが作成されていません（ERR102）');
      return;
    }

    if ($this->db->field_exists('pricetype', 'rooms_data')) {
      die('すでにアップデート済みです（181005DBアップデート）');
      return;
    }

    // add colum pricetype to rooms_data table
    $fields = array(
      'pricetype' => array('type' => 'TINYINT' , 'constraint' => 2 , 'default' => 1 , 'null' => false , 'after' => 'price')
    );
    $res = $this->dbforge->add_column('rooms_data', $fields);

    if(!$res) {
      die('「rooms_data」テーブルへのカラム追加が失敗しました（ERR201）');
      return;
    }

    if(!empty($res)) {
      die('DBのカラム追加処理が完了しました');
      return;
    }

    die('ERR001');

	}


}
