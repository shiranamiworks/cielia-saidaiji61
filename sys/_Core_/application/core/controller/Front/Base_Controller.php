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
class Base_Controller extends MY_Controller
{

    protected $search_where_base = '';

    public function __construct()
    {

        parent::__construct();

        if(!defined('CLIENT_ID')){
            //previewモードの場合、CLIENT ACCOUNTをここで定義
            if(!defined('CLIENT_ID') && !empty($_GET['mode']) && $_GET['mode'] == 'preview') {
                $this->load->library('session');
                $client_id = $this->session->userdata('client_id');
                if(!empty($client_id)) {
                    define('CLIENT_ID' , $client_id);
                }
            }
        }
        //CLIENT IDを定義
        /*
        if(!defined('CLIENT_ID')){
            //previewモードの場合、CLIENT ACCOUNTをここで定義
            if(!defined('CLIENT_ACCOUNT_ID') && !empty($_GET['mode']) && $_GET['mode'] == 'preview') {
                $this->load->library('session');
                $client_account = $this->session->userdata('client_account');
                if(!empty($client_account)) {
                    define('CLIENT_ACCOUNT_ID' , $client_account);
                }
            }

            if(defined('CLIENT_ACCOUNT_ID')) {
                if(is_file(CLIENT_APP_PATH.'/'.CLIENT_ACCOUNT_ID.'/settings.php')) {
                    require(CLIENT_APP_PATH.'/'.CLIENT_ACCOUNT_ID.'/settings.php');
                    if(!empty($_ClientSettings['CLIENT_ID'])) {
                        define('CLIENT_ID' , $_ClientSettings['CLIENT_ID']);
                    }
                }
            }
        }
        */

        if(!defined('CLIENT_ID')){
            $this->_error();
            return;
        }

        $this->load->database('default');
        $this->load->library(array('urlsetting' , 'frontviewlib' , 'pagination'));

        $this->view_data['client_id'] = CLIENT_ID;

        $time = fn_get_date();
        $this->search_where_base = "output_flag = 1 AND distribution_start_date <= '".$time."' AND (distribution_end_date = '0000-00-00 00:00:00' OR distribution_end_date >= '".$time."')";

    }
    
    /**
     *
     * @param string $path          パス (コントローラ/メソッド)
     * @param number $page      現在のページ数
     * @param number $total_rows    項目総数
     * @param number $per_page      1ページあたりの項目数
     * @param number $num_links     前後に表示するリンク数
     * @param number $uri_segment   ページ番号として利用するセグメントの順番（newでは使用しない）
     * @param number $per_num       全ページ数
     */
    protected function _paging_new($base_url, $page, $total_rows, $per_page = 20 , $num_links = 5, $uri_segment = 3, $page_num = 1) {

        $prev_page_txt = "＜";
        $next_page_txt = "＞";

        $disp=$num_links;

        //ページ番号リンク用
        $start =  ($page-floor($disp/2) > 0) ? ($page-floor($disp/2)) : 1;//始点
        $end =  ($start > 1) ? ($page+floor($disp/2)) : $disp;//終点
        $start = ($page_num < $end)? $start-($end-$page_num):$start;//始点再計算

        $output_html = '';

        if($page_num > 1){

            $base_url .= '?';
            $get = $this->input->get();
            if(isset($get['page'])) unset($get['page']);
            if(!empty($get)) {
                $base_url .= http_build_query($get).'&';
            }
            $base_url .= 'page=';

            $page_num = fn_getPages($total_rows , $per_page);
            $str_num1 = (!$total_rows) ? 0:($page-1)*$per_page+1;
            $str_num2 = ($page == $page_num) ? $total_rows : $page*$per_page;
            if($total_rows > 0){
                $output_html = '<li>'.$total_rows.'件中 '.$str_num1."〜".$str_num2.'件表示</li>';
            }else{
                $output_html = '<li>全 0件中</li>';
            }

            if($page != 1) {
                $output_html .= '<li class="pager_prev"><a href="'.$base_url.($page-1).'">'.$prev_page_txt.'</a></li>';
            }

            //最初のページへのリンク
            if($start >= floor($disp/2)){
                $output_html .= '<li><span>…</span></li>'; //ドットの表示
            }

            for($i=$start; $i <= $end ; $i++){//ページリンク表示ループ
                 
                if($i <= $page_num && $i > 0 ) {//1以上最大ページ数以下の場合
                    if($i == $page){
                        $output_html .= '<li><a href="javascript:void(0);" class="current" onClick="return false;">'.$i.'</a></li>';
                    }else{
                        $output_html .= '<li><a href="'.$base_url.$i.'">'.$i.'</a></li>';
                    }
                }
            }
             
            //最後のページへのリンク
            if($page_num > $end){
                $output_html .= '<li><span>…</span></li>';    //ドットの表示
            }

            if($page != $page_num) {
                $output_html .= '<li class="pager_next"><a href="'.$base_url.($page+1).'">'.$next_page_txt.'</a></li>';
            }

            $output_html .= '';
        }

        return $output_html;
    }

    protected function _paging_new_english($base_url, $page, $total_rows, $per_page = 20 , $num_links = 5, $uri_segment = 3, $page_num = 1) {

        $prev_page_txt = "＜";
        $next_page_txt = "＞";

        $disp=$num_links;

        //ページ番号リンク用
        $start =  ($page-floor($disp/2) > 0) ? ($page-floor($disp/2)) : 1;//始点
        $end =  ($start > 1) ? ($page+floor($disp/2)) : $disp;//終点
        $start = ($page_num < $end)? $start-($end-$page_num):$start;//始点再計算

        $output_html = '';

        if($page_num > 1){

            $base_url .= '?';
            $get = $this->input->get();
            if(isset($get['page'])) unset($get['page']);
            if(!empty($get)) {
                $base_url .= http_build_query($get).'&';
            }
            $base_url .= 'page=';

            $page_num = fn_getPages($total_rows , $per_page);
            $str_num1 = (!$total_rows) ? 0:($page-1)*$per_page+1;
            $str_num2 = ($page == $page_num) ? $total_rows : $page*$per_page;
            if($total_rows > 0){
                if($page != $page_num) {
                    $output_html = '<li>'.$per_page.' out of '.$total_rows.'</li>';
                }else{
                    $output_html = '<li>'.($total_rows - ($page-1)*$per_page).' out of '.$total_rows.'</li>';
                }
            }else{
                $output_html = '<li>0</li>';
            }

            if($page != 1) {
                $output_html .= '<li class="pager_prev"><a href="'.$base_url.($page-1).'">'.$prev_page_txt.'</a></li>';
            }

            //最初のページへのリンク
            if($start >= floor($disp/2)){
                $output_html .= '<li><span>…</span></li>'; //ドットの表示
            }

            for($i=$start; $i <= $end ; $i++){//ページリンク表示ループ
                 
                if($i <= $page_num && $i > 0 ) {//1以上最大ページ数以下の場合
                    if($i == $page){
                        $output_html .= '<li><a href="javascript:void(0);" class="current" onClick="return false;">'.$i.'</a></li>';
                    }else{
                        $output_html .= '<li><a href="'.$base_url.$i.'">'.$i.'</a></li>';
                    }
                }
            }
             
            //最後のページへのリンク
            if($page_num > $end){
                $output_html .= '<li><span>…</span></li>';    //ドットの表示
            }

            if($page != $page_num) {
                $output_html .= '<li class="pager_next"><a href="'.$base_url.($page+1).'">'.$next_page_txt.'</a></li>';
            }

            $output_html .= '';
        }

        return $output_html;
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
    protected function _paging($base_url = '', $cur_page = 0, $total_rows = 0, $per_page = 20, $num_links = 3, $uri_segment = 3) {
        $config['base_url'] = $base_url;
        $config['cur_page'] = $cur_page;
        $config['total_rows'] = $total_rows;
        $config['per_page'] = $per_page;
        $config['num_links'] = $num_links;
        $config['uri_segment'] = $uri_segment;
        $config['use_page_numbers'] = true;
        $config['page_query_string'] = true;
        $config['query_string_segment'] = 'page';

        $page_num = fn_getPages($total_rows , $per_page);
        $str_num1 = (!$total_rows) ? 0:($cur_page-1)*$per_page+1;
        $str_num2 = ($cur_page == $page_num) ? $total_rows : $cur_page*$per_page;
        if($total_rows > 0){
            $config['full_tag_open'] = '<li>'.$total_rows.'件中 '.$str_num1."〜".$str_num2.'件表示</li>';
        }else{
            $config['full_tag_open'] = '<li>全 0件中</li>';
        }
        $config['full_tag_close'] = '';
        $config['prev_link'] = '＜';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['next_link'] = '＞';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li><a href="javascript:void(0);" class="current" onClick="return false;">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['first_link'] = FALSE;
        $config['last_link'] = FALSE;

        $this->pagination->initialize($config);
        return  $this->pagination->create_links();
    }


    protected function _clientsInfo($key) {

        $this->load->model('Clients_model');
        return $this->Clients_model->getValue($key);

    }

    protected function _error_notfound($message = 'このURLは現在アクセスできません。') {
        $this->output->set_status_header('404');
        $this->view_data['error_message'] = $message;
        $this->frontviewlib->set_view_dir('error' , VIEW_TMPL_PATH);
        return $this->load->view('index' , $this->view_data);
    }

    protected function _error($message = 'エラーが発生し、処理が中断されました。大変に恐れ入りますが、再度操作をお試し下さい。') {
        $this->output->set_status_header('500');
        $this->view_data['error_message'] = $message;

        $this->frontviewlib->set_view_dir('error' , VIEW_TMPL_PATH);
        return $this->load->view('index' , $this->view_data);
    }

}