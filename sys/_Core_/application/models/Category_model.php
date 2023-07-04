<?php

class Category_model extends MY_Model {

	public function __construct() {
		parent::__construct();
		$this->_init();
	}

	/**
	 * 初期化関数
	 *
	 * @access	private
	 * @param
	 * @return
	 */
	private function _init() {
    //テーブル名は機能別で変わってくるため（お知らせ用カテゴリテーブル（info_category）、イベント用カテゴリテーブル(event_category)など）
    //カテゴリテーブル名の定数がセットされていればそれを利用する
    //if(defined('CATEGORY_TABLE_NAME')) {
    //  $this->_table = CATEGORY_TABLE_NAME;
    //}
	}

}
