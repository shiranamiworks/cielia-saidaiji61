<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/*
|--------------------------------------------------------------------------
| MyApplication Configration
|--------------------------------------------------------------------------
|
*/

$config['url_setting'] = array(
  'rooms_list'     => '/rooms/', // rooms/(:num) -> rooms/list/index/(:num)
  'rooms_detail'   => '/rooms/d/[id]/', // rooms/d/(:any) -> rooms/detail/index/(:any)

);