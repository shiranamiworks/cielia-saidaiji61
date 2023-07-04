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
 * MY_Controller
 *
 */

class MY_Controller extends CI_Controller
{
    public $view_data;

    public function __construct()
    {
        parent::__construct();

        //設定ファイル
        $this->load->config('config_myapp' , TRUE);

        //共通ヘルパー
        $this->load->helper(array('string','url','form', 'common'));

    }

    /**
     * エラー画面表示
     */
    protected function _custom_show_error($message  , $error_code = '' , $template = 'custom_error', $status_code = 500 , $heading= 'エラー')
    {
        $_error =& load_class('Exceptions', 'core');
        echo $_error->show_error($heading, $message, $template, $status_code);
        exit;
    }


    /**
     * Viewに渡すデータをエスケープする
     */
    protected function _view_esc($data) {
        $return = (array)fn_esc($data);
        $return['_no_escape'] = $data; //エスケープしていない元データも入れておく

        return $return;
    }
}

