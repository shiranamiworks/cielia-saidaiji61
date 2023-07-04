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
defined('ADMINTOOL_FLG') OR exit('No direct script access allowed');

class Base_Controller extends MY_Controller
{

    private $login_id;
    protected $dirname = '';
    protected $classname = '';

    protected $adminpage_title = '';
    protected $adminpage_infotext = '';
    protected $adminpage_dataname = '';

    public function __construct()
    {
        parent::__construct();

        $this->dirname = $this->router->fetch_directory();
        if(!empty($this->dirname)) {
            $this->dirname = str_replace('/' , '' , $this->dirname);
        }
        $this->classname = $this->router->fetch_class();

        //ログインチェック
        $this->load->library(array('auth','pagination' , 'urlsetting'));
        $redirect = (defined('LOGOUT_REDIRECT_VIEWFILE') && LOGOUT_REDIRECT_VIEWFILE) ? false : true;
        $logout_redirect_viewfile = (defined('LOGOUT_REDIRECT_VIEWFILE') && LOGOUT_REDIRECT_VIEWFILE) ? LOGOUT_REDIRECT_VIEWFILE : '';
        
        $this->auth->isSuccess($redirect , "" , $logout_redirect_viewfile);

        //DB接続 セッションに持っている client_idの値をもとに接続するDB名を得て、DB接続する
        if(empty($this->session->userdata('client_id'))) {
            $this->_custom_show_error('データベースエラーが発生しました');
        }
        define('CLIENT_ID' , $this->session->userdata('client_id'));
        
        $this->load->database('default');

        $this->view_data['dirname'] = $this->dirname;
        $this->view_data['classname'] = $this->classname;

        //データ名称
        $data_name_list = $this->config->item('data_name' , 'config_myapp');
        if(!empty($data_name_list[$this->dirname])) {
            define('DATA_NAME' , $data_name_list[$this->dirname]);
        }
        if(!defined('DATA_NAME')) define('DATA_NAME' , 'データ');

        //グローバルナビ
        $this->load->config('config_adminmenu' , TRUE);
        $this->view_data['admin_menu_list'] = $this->config->item('menu_list' , 'config_adminmenu');

        //ヘッダ情報
        $this->view_data['header_info'] = array();
        $this->view_data['header_info']['site_url'] = $this->_clientsInfo('site_url');
        $this->view_data['header_info']['account_name'] = 'アカウント：'.$this->_userInfo('user_name')." さん";
        $this->view_data['blank_page_url'] = site_url('blank');

        //管理画面タイトルまわり
        $this->view_data['adminpage_title'] = $this->adminpage_title;
        $this->view_data['adminpage_infotext'] = $this->adminpage_infotext;
        $this->view_data['adminpage_dataname'] = $this->adminpage_dataname;

        $admin_dirname_title = '';
        if(!empty($this->dirname)) {
            $config_menu_list = $this->config->item('menu_list' , 'config_adminmenu');
            if(isset($config_menu_list[$this->dirname]['label'])) {
                $admin_dirname_title = $config_menu_list[$this->dirname]['label'].' | ';
            }
        }

        //管理画面ヘッダタイトル
        define("ADMIN_BASE_TITLE" , $admin_dirname_title . $this->config->item('admin_title' , 'config_myapp'));

        //インデックスコントローラへのパス
        define('INDEX_CONTROLLER_PATH' , (!empty($this->dirname) ? $this->dirname.'/' : '').$this->classname);

    }

    /**
     *
     * @param string $path          パス (コントローラ/メソッド)
     * @param number $cur_page      現在のページ数
     * @param number $total_rows    項目総数
     * @param number $per_page      1ページあたりの項目数
     * @param number $num_links     前後に表示するリンク数
     * @param number $uri_segment   ページ番号として利用するセグメントの順番
     */
    protected function _paging($base_url = '', $cur_page = 0, $total_rows = 0, $per_page = 20, $num_links = 5, $uri_segment = 3) {
        $config['base_url'] = $base_url;
        $config['cur_page'] = $cur_page;
        $config['total_rows'] = $total_rows;
        $config['per_page'] = $per_page;
        $config['num_links'] = $num_links;
        $config['use_page_numbers'] = true;
        $config['page_query_string'] = true;
        $config['query_string_segment'] = 'page';
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['prev_link'] = '&lt;';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['next_link'] = '&gt;';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['first_link'] = FALSE;
        $config['last_link'] = FALSE;

        $this->pagination->initialize($config);
        return  $this->pagination->create_links();
    }

    protected function _userInfo($key) {
        if(!empty($this->session->userdata($key))) {
            return $this->session->userdata($key);
        }

        return "";
    }

    protected function _clientsInfo($key) {

        $this->load->model('Clients_model');
        return $this->Clients_model->getValue($key);

    }

    protected function _error_notfound($message = 'このURLはアクセスすることができません。') {
        $this->output->set_status_header('404');
        $this->view_data['error_message'] = $message;
        echo $this->load->view('error/index' , $this->view_data ,true);
        exit;
    }

    protected function _error($message = 'エラーが発生し、処理が中段されました。恐れ入りますが、再度操作をお試し下さい。') {
        $this->output->set_status_header('500');
        $this->view_data['error_message'] = $message;
        echo $this->load->view('error/index' , $this->view_data ,true);
        exit;
    }

    //POST送信されてきているかをチェック
    protected function _checkPost() {
        if($_SERVER["REQUEST_METHOD"] != "POST"){
            $this->security->csrf_show_error();
        }

        return;
    }
}