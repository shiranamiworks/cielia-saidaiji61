<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
if(defined('ADMINTOOL_FLG')) {
  //管理者モードの場合
  $route['default_controller'] = 'home';
} else {
  //フロントビュー側
  $route['default_controller'] = 'blank/index';
}

$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

if(defined('ADMINTOOL_FLG')) {
  //管理者モードの場合
  $route['(:any)/media/(:num)'] = "$1/media/index/$2";
  $route['(:any)/media/(:num)/(:any)'] = "$1/media/index/$2/$3";
  $route['(:any)/lists/(:num)'] = "$1/lists/index/$2";
  $route['(:any)/lists/(:num)/(:any)'] = "$1/lists/index/$2/$3";
  $route['(:any)/edit/(:num)'] = "$1/edit/index/$2";
  $route['(:any)/edit/(:num)/(:any)'] = "$1/edit/index/$2/$3";

} else {

  $route['404_override'] = 'myerror/frontview404';

  //フロントビュー側
  $route['rooms/lists'] = "frontview/rooms/index";
}

//管理画面からのプレビュー表示の場合のため
$route['frontview/rooms/d/(:any)'] = "frontview/rooms/detail/$1";
