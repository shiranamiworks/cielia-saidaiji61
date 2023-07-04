<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['torikago_table_css_classname'] = array(
	'1' => '', //分譲中
	'2' => 'keiyaku', //分譲済み
	'3' => 'jiki', //次期分譲
	'4' => 'keiyaku', //非分譲
	'5' => 'nolink public', //共有・その他,
	'99' => 'keiyaku' //データ登録なし
);
$config['torikago_table_output_tag'] = array(
	'1' => '<p>{type}</p><p class="price">{price}</p>', //分譲中
	'2' => '<p>{type}</p><p class="price">{status}</p>', //分譲済み
	'3' => '<p>{type}</p><p class="price">{status}</p>', //次期分譲
	'4' => '<p>{type}</p><p class="price">{status}</p>', //非分譲
	'5' => '<span>{type}</span>', //共有・その他
);
//シミュレーター起動ボタン
$config['simulation_btn_tag'] = '<p class="btn-simulation"><button type="button"><img src="../../sys/setting/img/icon_simu.png" alt="カンタンローン計算"></button></p>';
$config['furniture_btn_tag'] = '<p class="btn-furniture"><a target="_blank"><img src="../../sys/setting/img/icon_kagu.png" alt="家具シミュレーション"></a></p>';
//シミュレーター起動ボタンを表示するタイプ
$config['simulation_enable_type'] = array('1');
//リンクを無効にするタイプ（このタイプはリンク先の入力があってもリンクされなくなる）
$config['link_disable_type'] = array('2','4','5');

//この物件用の資金計画シミュレーターURL
$config['simulation_link_url'] = '/m/saidaiji61/simulator/index.html';
$config['simulation_link_window'] = 'popup'; //popup:別ウィンドウ立ち上げ、self:同一ウインドウで開く
$config['simulation_window_size'] = array('width'=>'860px','height'=>'900px');

