<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/*
|--------------------------------------------------------------------------
| MyApplication Configration
|--------------------------------------------------------------------------
|
*/

$config['admin_title'] = '鳥かごCMS管理画面';
$config['admin_mainpage'] = 'rooms/lists';
$config['client_db_basename'] = '';
$config['client_upload_path'] = '/m/saidaiji61/uploads';

$config['media_upload_upper_dir'] = dirname(__FILE__).'/../../../../uploads';
$config['media_upload_path'] = '/m/saidaiji61/uploads/cl_';
$config['media_upload_dir'] = dirname(__FILE__).'/../../../../uploads/cl_';
$config['media_upload_maxsize'] = 5000;

$config['tmpl_data_path'] = 'tpl\data\\';


$config['torikago_template_path'] = dirname(__FILE__).'/../../../setting/inc/';



//メール送信元
$config['EMAIL_FROM'] = "pylomania@gmail.com";
$config['EMAIL_FROM_NAME'] = "ESO";

//データテーブルリスト
//データテーブル名とディレクトリ名は必ず合わせる
$config['data_table_list'] = array(
  'rooms'
);

//データ名
//KEYは必ずディレクトリ名に合わせる
$config['data_name'] = array(
  'rooms'   => '物件',
  'accounts' => 'アカウント',
  'setting' => '設定'
);

define('NUM_LIST' , 10);
define('CATEGORY_DATA_DELIMITER' , ';');

/** TABLE設定 */
define('TABLE_BASENAME_DATA' , 'data');
define('TABLE_BASENAME_CATEGORY' , 'category');
define('TABLE_BASENAME_MEDIA' , 'media');
define('TABLE_BASENAME_METADATA' , 'metadata');

/* End of file config_myapp.php */
/* Location: ./application/config/config_myapp.php */