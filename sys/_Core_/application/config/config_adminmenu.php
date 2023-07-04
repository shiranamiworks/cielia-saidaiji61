<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/*
|--------------------------------------------------------------------------
| MyApplication Configration
|--------------------------------------------------------------------------
|
*/

$config['menu_list'] = array(

    //Menu Main
    /*
    "home" => array(
      "menu_id" => "Home",
      "label"     => "ホーム画面",
      "link"    => 'home',
      "submenu"   =>array(
        )
    ),
    */

    "rooms" => array(
        "menu_id"   => "Rooms",
        "terms"     => "manage_rooms",
        "label"     => "鳥かご管理",
        "link"      => 'rooms/lists',
        "comment"   => "鳥かごに表示する物件の管理・編集を行います",
        "submenu"   =>array(

            array(
                "class" => "list",
                "label" => "物件一覧",
                "link"  => 'rooms/lists',
                "icon"  => "list"
            ),

            /*
            array(
                "class" => "edit",
                "label" => "物件編集",
                "link"  => 'rooms/edit',
                "icon"  => "data"
            )
            */
        )
    ),

    "logout" => array(
        "menu_id"   => "Logout",
        "label"     => "ログアウト",
        "link"      => 'logout',
        "comment"   => "管理画面からログアウトします",
    )
);



/* End of file config_myapp.php */
/* Location: ./application/config/config_myapp.php */