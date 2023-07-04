<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require(dirname(__FILE__).'/../../core/controller/Admin/Edit_Controller.php');

class Edit extends Edit_Controller {

  protected $edit_id = '';

	public function __construct(){
    //ソートNoを登録するか
    define('SET_SORT_NUM' , true);

		parent::__construct();

    if(!empty($_GET['edit_id'])) {
      $this->edit_id = fn_esc( $_GET['edit_id'] );
    }

		$this->_orig_setting();

    if(empty($_GET['edit_id'])) {
      unset( $this->editSetting['items']['Main']['item']['edit_id'] );
    }

    //クリック時リンク先の注釈コメント作成
    $category_list = $this->datalib->get_category_list();
    $this->config->load('config_torikago',true);
    $link_disable_type = $this->config->item('link_disable_type' , 'config_torikago');
    $link_disable_category_arr = array();
    if(!empty($link_disable_type)) {
      foreach($link_disable_type as $type_id) {
        if(!empty($category_list[$type_id])) {
          $link_disable_category_arr[] = '「'.$category_list[$type_id].'」';
        }
      }
    }
    if(!empty($link_disable_category_arr)) {
      $this->editSetting['items']['Main']['item']['link']['comment_btm'] = '表示タイプが'.implode('',$link_disable_category_arr).'の場合はリンクされません';
    }

    $this->view_data['adminpage_infotext'] = 'ここでは、物件データの登録・編集を行うことができます。';
	}

  protected function _init_index($id , $editData) {
    if(!empty($this->edit_id)) {
      //送信先URLにedit_urlが追記されるよう修正
      $this->view_data['submit_url'] = site_url(INDEX_CONTROLLER_PATH.'/'.$id).'?edit_id='.$this->edit_id;
    }
  }

  protected function _before_data_save($db_dataset) {
    if($db_dataset['data']['price'] === '') {
      //価格が空の場合は価格タイプは1に
      if(isset($db_dataset['data']['pricetype']) && $db_dataset['data']['pricetype'] == 2) {
        $db_dataset['data']['pricetype'] = 1;
      }
    }
    return $db_dataset;
  }

  protected function _before_render_complete_view() {
    $this->view_data['add_js'] = 'js/edit_comp';
  }

  private function _orig_setting() {

    $this->editSetting = [
      'add_view' =>[
        'js' => 'js/edit',
        'css' => 'css/edit',
      ],
      'tabs' => [
        ['id' => 'Main' , 'name' => '基本項目']
      ],
      'items' => [
        'Main' => [
          'item' => [

            'category' => [
              'meta'    => false,
              'label'   => '表示タイプ',
              'type'    => 'select',
              'data'    => 'category_list',
              'attr'    => [],
              'require' => false,
              'validation' => 'is_natural_no_zero',
            ],

            'title'    => [
              'meta'    => false,
              'label'   => '部屋番号',
              'type'    => 'input',
              'attr'    => [
                'maxlength' => 40,
                'class'=>'form-control',
                'style'=>'width:200px'
              ],
              'require' => false,
              'validation' => 'trim|max_length[40]',
              'label_comment' => '',
              'comment_top'  => '',
              'comment_btm' => ''
            ],

              'breadth'    => [
                  'meta'    => false,
                  'label'   => '面積',
                  'type'    => 'input',
                  'attr'    => [
                      'maxlength' => 7,
                      'class'=>'form-control inline-block',
                      'style'=>'width:200px',
                      'placeholder' => '例：9999.99'
                  ],
                  'require' => false,
                  'validation' => 'trim|decimal|max_length[7]',
                  'label_comment' => '',
                  'comment_top'  => '',
                  'comment_btm' => '小数点2桁まで入力ください。',
                  'suffix' => '㎡'
              ],
              'ldk'    => [
                  'meta'    => false,
                  'label'   => 'LDK',
                  'type'    => 'input',
                  'attr'    => [
                      'class'=>'form-control',
                      'style'=>'width:200px',
                      'placeholder' => '例：3LDK'
                  ],
                  'require' => false,
                  'validation' => 'trim|regex_match[/^[a-zA-Z0-9!-~]+/]',
                  'label_comment' => '',
                  'comment_top'  => '',
                  'comment_btm' => ''
              ],
              'orientation'    => [
                  'meta'    => false,
                  'label'   => '方位',
                  'type'    => 'input',
                  'attr'    => [
                      'maxlength' => 40,
                      'class'=>'form-control',
                      'style'=>'width:200px',
                      'placeholder' => '例：南向き'
                  ],
                  'require' => false,
                  'validation' => 'trim|max_length[40]',
                  'label_comment' => '',
                  'comment_top'  => '',
                  'comment_btm' => ''
              ],
              'free_tag'    => [
                  'meta'    => false,
                  'label'   => 'フリー',
                  'type'    => 'input',
                  'attr'    => [
                      'maxlength' => 100,
                      'class'=>'form-control',
                      'style'=>'width:200px',
                      'placeholder' => '例：ルーフバルコニー,角部屋'
                  ],
                  'require' => false,
                  'validation' => 'trim|max_length[100]',
                  'label_comment' => '',
                  'comment_top'  => '',
                  'comment_btm' => 'カンマ区切りで複数入力可能です。'
              ],

            'distribution_start_date' => [
              'meta'    => false,
              'label'   => '掲載開始日時',
              'type'    => 'hidden',
              'hidden_val'    => '2018-01-01 12:00:00',
              'attr'    => [],
              'require' => true,
              'validation' => 'required|valid_date_yymmddhhii',
            ],

            'output_flag' => [
              'meta'    => false,
              'label'   => '表示フラグ',
              'type'    => 'hidden',
              'hidden_val'    => '1',
              'attr'    => [],
              'require' => true,
              'validation' => 'required',
            ],

            /*
            'distribution_end_date' => [
              'meta'    => false,
              'label'   => '掲載終了日時',
              'type'    => 'date_end',
              'attr'    => [
                'class'=>'form-control',
                'style'=>'width:250px'
              ],
              'require' => false,
              'validation' => 'valid_date_yymmddhhii'
            ],

            'category' => [
              'meta'    => false,
              'label'   => 'カテゴリ',
              'type'    => 'hidden',
              'hidden_val'    => '1',
              'attr'    => [],
              'require' => false,
              'validation' => 'required|is_natural_no_zero',
            ],
            */

            'type_str'    => [
              'meta'    => false,
              'label'   => 'タイプ名',
              'type'    => 'textarea',
              'attr'    => [
                'rows' => 2,
                'cols' => 50
              ],
              'require' => false,
              'validation' => 'trim',
              'comment_btm' => ''
            ],

            'status_str'    => [
              'meta'    => false,
              'label'   => 'ステータス',
              'type'    => 'textarea',
              'attr'    => [
                'rows' => 2,
                'cols' => 50
              ],
              'require' => false,
              'validation' => 'trim',
              'comment_btm' => 'ここの入力値は「分譲済み」「次期分譲」「非分譲」の場合に表示されます'
            ],

            'price'    => [
              'meta'    => false,
              'label'   => '価格',
              'type'    => 'input',
              'attr'    => [
                'maxlength' => 10,
                'class'=>'form-control',
                'style'=>'width:100px'
              ],
              'require' => false,
              'validation' => 'trim|numeric|max_length[10]',
              'label_comment' => '整数で入力して下さい',
              'comment_top'  => '',
              'comment_btm' => '価格タイプが「万円台」の場合は、十万・一万の位の値は0にしてください。'
            ],
            'pricetype' => [
              'meta'    => false,
              'label'   => '価格タイプ',
              'type'    => 'radio',
              'data'    => [
                '1' => '万円',
                '2' => '万円台'
              ],
              'attr'    => [],
              'default' => 1,
              'require' => false,
              'validation' => 'required|numeric'
            ],

            'link'    => [
              'meta'    => false,
              'label'   => '家具シミュレーション',
              'type'    => 'input',
              'attr'    => [
                'maxlength' => 200,
                'class'=>'form-control',
                'style'=>'width:600px'
              ],
              'require' => false,
              'validation' => 'trim|max_length[200]',
              'label_comment' => '',
              'comment_top'  => '',
              'comment_btm' => ''
            ],

            'edit_id' => [
              'meta'    => false,
              'label'   => '編集用ID',
              'type'    => 'hidden',
              'hidden_val'    => $this->edit_id,
              'attr'    => [],
              'require' => false,
              'validation' => 'required',
            ],

            /*
            'chkbox1' => [
              'meta'    => false,
              'label'   => 'チェックボックス',
              'type'    => 'checkbox',
              'data'    => [
                '1' => 'value1',
                '2' => 'value2',
                '3' => 'value3'
              ],
              'attr'    => [],
              'require' => true,
              'validation' => 'required'
            ],

            'radio1' => [
              'meta'    => false,
              'label'   => 'ラジオボタン',
              'type'    => 'radio',
              'data'    => [
                '1' => 'radio1',
                '2' => 'radio2'
              ],
              'attr'    => [],
              'require' => true,
              'validation' => 'required|numeric'
            ],

            'textarea1' => [
              'meta'    => false,
              'label'   => 'テキストエリア',
              'type'    => 'textarea',
              'attr'    => [
                'rows' => 5,
                'cols' => 80
              ],
              'require' => true,
              'validation' => 'required',
              'label_comment' => 'ダミー',
              'comment_top'  => 'ダミーtop',
              'comment_btm' => 'ダミーダミーbtm'
            ],

            //meta
            'message'    => [
              'meta'    => true,
              'label'   => '(META)メッセージ',
              'type'    => 'input',
              'attr'    => [
                'maxlength' => 255,
                'class'=>'form-control'
              ],
              'require' => true,
              'validation' => 'trim|required|max_length[255]',
              'label_comment' => 'ダミーダミーダミーダミーダミーダミーダミー',
              'comment_top'  => 'ダミーダミーダミーダミーダミーダミーダミーtop',
              'comment_btm' => 'ダミーダミーダミーダミーダミーダミーダミーbtm'
            ],

            //meta
            'select1' => [
              'meta'    => true,
              'label'   => '(META)セレクトボックス',
              'type'    => 'select',
              'data'    => [
                '1' => 'value1',
                '2' => 'value2',
                '3' => 'value3'
              ],
              'attr'    => [],
              'require' => true,
              'validation' => 'required|numeric',
            ],
            */
            'base_image' => [
              'meta'    => false,
              'label'   => '基本タイプ',
              'type'    => 'image',
              'attr'    => [],
              'require' => false,
              'validation' => 'max_length[255]'
            ],
              'menu1_image' => [
                  'meta'    => false,
                  'label'   => 'メニュー1',
                  'type'    => 'image',
                  'attr'    => [],
                  'require' => false,
                  'validation' => 'max_length[255]'
              ],
              'menu2_image' => [
                  'meta'    => false,
                  'label'   => 'メニュー2',
                  'type'    => 'image',
                  'attr'    => [],
                  'require' => false,
                  'validation' => 'max_length[255]'
              ],
              'menu3_image' => [
                  'meta'    => false,
                  'label'   => 'メニュー3',
                  'type'    => 'image',
                  'attr'    => [],
                  'require' => false,
                  'validation' => 'max_length[255]'
              ],
              'menu4_image' => [
                  'meta'    => false,
                  'label'   => 'メニュー4',
                  'type'    => 'image',
                  'attr'    => [],
                  'require' => false,
                  'validation' => 'max_length[255]'
              ],
              //meta
                  /*
            //meta
            'chkbox2' => [
              'meta'    => true,
              'label'   => '(META)チェックボックス',
              'type'    => 'checkbox',
              'data'    => [
                '1' => 'meta_chkvalue1',
                '2' => 'meta_chkvalue2',
                '3' => 'meta_chkvalue3',
                '4' => 'meta_chkvalue1',
                '5' => 'meta_chkvalue2',
                '6' => 'meta_chkvalue3',
                '7' => 'meta_chkvalue1',
                '8' => 'meta_chkvalue2',
                '9' => 'meta_chkvalue3'
              ],
              'attr'    => [],
              'require' => true,
              'validation' => 'required'
            ],

            //meta
            'radio2' => [
              'meta'    => true,
              'label'   => '(META)ラジオボタン',
              'type'    => 'radio',
              'data'    => [
                '1' => 'meta_radio1',
                '2' => 'meta_radio2'
              ],
              'attr'    => [],
              'require' => true,
              'validation' => 'required|numeric'
            ],

            //meta
            'textarea2' => [
              'meta'    => true,
              'label'   => '(META)テキストエリア',
              'type'    => 'textarea',
              'attr'    => [
                'rows' => 8,
                'cols' => 50
              ],
              'require' => true,
              'validation' => 'required',
              'label_comment' => 'ダミーダミーダミー',
              'comment_top'  => 'ダミーダミーダミーtop',
              'comment_btm' => 'ダミーダミーダミーbtm'
            ],

            //meta
            'meta_content' => [
              'meta'    => true,
              'label'   => '(META)本文',
              'type'    => 'editor',
              'attr'    => [
                'class' => "col-md-12",
                'rows'  => "10"
              ],
              'require' => true,
              'validation' => 'required'
            ],
            
            */
          ]
        ],
      ]
    ];

    //カテゴリデータをセット($this->editSetting中の data : category_list の値と置き換えされる)
    $this->category_list = $this->datalib->get_category_list();

  }

}
