<?php
defined('BASEPATH') OR exit('No direct script access allowed');
defined('ADMINTOOL_FLG') OR exit('No direct script access allowed');

class Setup extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->_init();
	}

	private function _init() {
    $this->load->database('default');
	}


	public function index() {

    $this->view_data['database_name'] = $this->db->database;
    $this->load->view('setup/index' , $this->_view_esc($this->view_data));

	}


	public function start() {
    
    $complete = false;
    try{

      if(!$this->input->post('submit')) {
        throw new Exception("Input Error");
      }

      //データベースにテーブルが存在していないかチェック
      //入っていたらインストール開始しない
      $tables = $this->db->list_tables();

      if(!empty($tables)) {
        throw new Exception("このデータベースにはすでにテーブルが作成されています。");
      }

      $sql = $this->load->view('setup/sql/data' , array() , true);
      $sqls = explode(';', $sql);
      array_pop($sqls);

      if(!empty($sqls)) {

        $this->db->trans_start();
        
        foreach($sqls as $statement){
          $statment = $statement . ";";
          $this->db->query($statement); 
        }
        
        $this->db->trans_complete();


        if ($this->db->trans_status() === FALSE)
        {
          throw new Exception("エラーが発生しました。セットアップに失敗しました（DB ERROR 002）");
        }

          $uploadDir = $this->config->item('media_upload_upper_dir' , 'config_myapp');
          if (file_exists($uploadDir) == false) {
              mkdir($uploadDir, 0777);

              $uploadDir = $this->config->item('media_upload_dir' , 'config_myapp') . 'torikago';
              if (file_exists($uploadDir) == false) {
                  mkdir($uploadDir, 0777);
              }
          }

        $complete = true;

      }
      
    } catch(Exception $e) {
      $error = $e->getMessage();
      $this->view_data['error'] = $error;
    }

    $this->view_data['complete'] = $complete;
    $this->load->view('setup/complete' , $this->_view_esc($this->view_data));

	}


}
