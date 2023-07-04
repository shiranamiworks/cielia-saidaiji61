<?php
defined('BASEPATH') OR exit('No direct script access allowed');
defined('ADMINTOOL_FLG') OR exit('No direct script access allowed');

class Login extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->_init();
	}

	private function _init() {
    
    //管理画面ヘッダタイトル
    define("ADMIN_TITLE" , $this->config->item('admin_title' , 'config_myapp'));

    $this->load->library('auth' , 'session');
	}


	public function index() {
    
    //セッションが切れる直後にいたURL
    if($this->session->flashdata('logout_ref')) {
      $this->session->set_userdata('referer_url' , $this->session->flashdata('logout_ref'));
    }else{
      $this->session->unset_userdata('referer_url');
    }

		$this->load->view('login/index' , $this->_view_esc($this->view_data));
		return;

	}


	public function auth() {

		$validation = $this->_input_validation();
    
		if($validation['error'] === true) {

  		$this->load->library(array('form_validation'));
			$this->view_data['error'] = $validation;

			$this->form_validation->run();
			$this->load->view('login/index' , $this->_view_esc($this->view_data));

		} else {
			//認証成功
      $redirect_url = site_url($this->config->item('admin_mainpage' , 'config_myapp'));
      if($this->session->userdata('referer_url')) {
        $redirect_url = site_url($this->session->userdata('referer_url'));
        $this->session->unset_userdata('referer_url');
      }
			redirect($redirect_url , 'location');
		}

		return;
	}


  private function _input_validation() {


  	if(!$this->input->post('loginID') || !$this->input->post('password')) {

  		return array('error'=>true , 'key'=>'loginID' , 'message'=>'すべての項目に入力を行って下さい');

  	} else {

  		$this->load->database('default');

			//クライアント用のDBに接続して ID・パスワードを照合
			$this->load->model('Admins_model');
			$rs_data = $this->Admins_model->fetchOneByLoginAccount($this->input->post('loginID'));
			if(!empty($rs_data["password"]) && !empty($rs_data['created']))
      {

          $a = $this->auth->makeEncryptedPassword($this->input->post('password') , $rs_data['created']);
          // ユーザ入力パスワードの判定
          if ($this->auth->makeEncryptedPassword($this->input->post('password') , $rs_data['created']) == $rs_data["password"]) {
              //CLIENT_ID取得
              $this->load->model('Clients_model');
              $CLIENT_ID = $this->Clients_model->getValue('account');

              // セッション登録
              $this->auth->setLoginSession($rs_data['login_id'] , $rs_data['user_name'],$this->config->item('CERT_STRING'),$rs_data["authority"] , $CLIENT_ID);
							//ログイン日時をDBに書き込み
							$this->Admins_model->saveLastLoginData($rs_data['login_id']);
              return array('error'=>false);
          }
      }

  	}
    
  	return array('error'=>true , 'key'=>'loginID' ,'message'=>'ユーザーIDまたはパスワードの入力に間違いがあります');
  }

}
