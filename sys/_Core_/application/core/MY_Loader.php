<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class MY_Loader extends CI_Loader {
    function __construct(){
        parent::__construct();
    }

    /**
    * viewのパス上書き
    * @param string $path 追加するパス
    */
    function view_path_override($path){
        $this->_ci_view_paths = array($path => TRUE);
    }
    

    public function custom_view($view_basedir , $view_file , $vars = array(), $return = FALSE)
    {
        $file_exists = FALSE;
        if(substr($view_file , -4) != '.php') {
            $view_file = $view_file.'.php';
        }
        if(!empty($view_basedir)) $view_basedir .= '/';
        foreach ($this->_ci_view_paths as $_ci_view_file => $cascade){
            if (file_exists($_ci_view_file . $view_basedir .$view_file)) {
                $file_exists = TRUE;
                break;
            }
        }
        
        //指定したビューファイルがない場合は commonを参照する
        if(!empty($view_basedir) && $file_exists === FALSE) {
            $view_basedir = 'common/';
        }

        //viewにセットするデータをエスケープしておく
        $var_data = array();
        if(!empty($vars)) {
            $var_data = fn_esc($vars);
            $var_data['_no_escape'] = $vars;
            //エスケープしない変数は$_変数名（例：$a -> $_a）で取得できるようにしておく
            foreach($vars as $key => $value) {
                $var_data['_'.$key] = $value;
            }
        }

        return $this->view($view_basedir .$view_file, $var_data, $return);
    }

}
?>