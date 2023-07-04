<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
======================================================================
Project Name    : Mishima T.CMS  
 
Copyright © <2016> Teruhiko Mishima All rights reserved.
 
This source code or any portion thereof must not be  
reproduced or used in any manner whatsoever.
本ソースコードを無断で転用・転載することを禁じます。
======================================================================
*/

//ini_set('display_errors',1);
class  Mediafile {

	var $CI = NULL;
  var $media_upload_dir;
  var $media_upload_path;

  var $client_upload_path;

  var $media_model_name = 'Media_model';

  private $prodimg_resize_setting = array(
    //一覧表示サムネイルサイズ
    'size_list_thumb' => array('addname' => 'prdlist' , 'width' => 220 , 'height' => 280),
    //関連製品表示サムネイルサイズ
    'size_related_thumb' => array('addname' => 'prdrel' , 'width' => 150 , 'height' => 191),
    //詳細写真切り替えサムネイルサイズ
    'size_slide_thumb' => array('addname' => 'prdslide' , 'width' => 100 , 'height' => 100),
    //通常サイズ
    'size_prod_m' => array('addname' => 'prdm' , 'width' => 460 , 'height' => 630),
    //拡大サイズ
    'size_prod_l' => array('addname' => 'prdl' , 'width' => 840)
  );


	/**
	* __construct
	*
	* @access public
	* @return void
	*/
	public function __construct($params = array()) {
		$this->CI =& get_instance();
    if(!empty($params['media_model_name'])) {
      $this->media_model_name = $params['media_model_name'];
    }

		$this->_set_config();
	}

	/**
	* _set_config
	*
	* @access protected
	* @return void
	*/
	protected function _set_config() {

		$this->CI->config->load('config_myapp' , true);

    $this->CI->load->model($this->media_model_name , 'Media_model');
		$this->CI->load->helper(array('string','url' , 'common'));

    //管理画面利用時のアップロードファイルの基本パス
    $this->media_upload_dir = $this->CI->config->item('media_upload_dir' , 'config_myapp') . CLIENT_ID; // /var/www/...
    $this->media_upload_path = $this->CI->config->item('media_upload_path' , 'config_myapp') . CLIENT_ID; // /uploads/cl_1/...

    //クライアント側のサイト表示時のアップロードファイルの基本パス
    $this->client_upload_path = $this->CI->config->item('client_upload_path' , 'config_myapp');
	}

  public function getUploadDir() {
    return $this->media_upload_dir;
  }

  public function getUploadPath($file_name = "") {
    if(!empty($file_name)) {
      return $this->media_upload_path."/".$file_name;
    }

    return $this->media_upload_path;
  }

  public function getClientUploadPath($file_name = "") {
    if(!empty($file_name)) {
      return $this->client_upload_path."/".$file_name;
    }
    
    return $this->client_upload_path;
  }


  /**
   *  アップロードされているファイルリスト取得
   *
   *  @param  number $page
   *  @param  number $num
   *  @param  string $filter(image:gif,jpg,pngファイルのみ、pdf:PDFファイルのみ）
   *  @param  string $keyword (検索キーワード)
   *  @return object
   */
  public function getFileList($page , $num=15 , $filter = "" , $keyword = "") {

    $medialist = array();
    $data_num = 0;

    if($this->media_upload_dir && is_dir($this->media_upload_dir)){

      $where = array();
      if($filter == 'image') {
        $where['type'] = 'image';
      } elseif ($filter == 'pdf') {
        $where['type'] = 'pdf';
      } elseif ($filter == 'word') {
        $where['type'] = 'word';
      }elseif ($filter == 'excel') {
        $where['type'] = 'excel';
      }elseif ($filter == 'file') {
        $where['type !='] = 'image';
      }

      //全データ件数取得
      $data_num = $this->CI->Media_model->getDataCnt($where , $keyword);

      //総ページ数取得
      $page_num = fn_getPages($data_num , $num);
      if($page > $page_num){ $page = $page_num; }

      //ファイルリスト取得
      $result = $this->CI->Media_model->getList($page , $num , $where , $keyword);

      if(!empty($result)) {
          foreach($result as $media) {
              $lfl = $this->media_upload_dir."/".$media["filename"];

              if(file_exists($lfl)){
                  //$filesize = ceil(filesize($lfl)/1024);
                  $medialist[] = array(
                      "id"=>$media["id"],
                      "type"=>$media["type"],
                      "upload_filename"=>$media["upload_filename"],
                      "filename"=>$media["filename"],
                      "path"=>$this->media_upload_path."/".$media["filename"],
                      //"ext" => $file_ext,
                      "description" => $media["description"],
                      //"size"=>$filesize
                  );
              }
          }
      }

        if(!empty($medialist)){
          return array('num' => $data_num , 'medialist' => $medialist);
        }
    }

    return array('num' =>0 , 'medialist'=>array());

  }

  /**
   * サムネイル表示用画像ファイルのパスを返す
   * @param object $file_name
   * @return string
   */
  public function get_thumbpath($file_name , $addname = '_thumb') {
    
    $filepath = realpath($this->getUploadDir()."/".$file_name);
    
    if(file_exists($filepath)) {
      $file_info = pathinfo($filepath);
      if(!empty($file_info['filename'])) {
        if(strtolower($file_info['extension']) == 'pdf') {
          return base_url().'img/icons/ico_pdf.png';
        } else if(strtolower($file_info['extension']) == 'doc' || strtolower($file_info['extension']) == 'docx') {
          return base_url().'img/icons/ico_word.png';
        } else if(strtolower($file_info['extension']) == 'xls' || strtolower($file_info['extension']) == 'xlsx') {
          return base_url().'img/icons/ico_excel.png';
        } else {
          $thumb_file_name = $file_info['filename'].$addname.".".$file_info['extension'];
          $thumb_filepath = realpath($this->getUploadDir()."/".$thumb_file_name);
          if(file_exists($thumb_filepath)) {
            return $this->getUploadPath()."/".$thumb_file_name;
          }
        }
      }

      return $this->getUploadPath()."/".$file_name;
    }

    return "";

  }


  /*
  public function get_file_extension($file_name) {

    $filepath = realpath($this->getUploadDir()."/".$file_name);
    die($filepath);
    if(file_exists($filepath)) {
      $file_info = pathinfo($filepath);
      if(isset($file_info["extension"])){
          return strtolower($file_info["extension"]);
      }
    }
    return "";
  }
  */


  /**
   * 一覧で表示させるデータ件数表示のhtml
   * @param number $cnt
   * @param number $page
   * @param number $page_num
   * @param number $output_num
   * @return string
   */
  public function html_media_list_pager_head($cnt , $page , $page_num , $output_num)
  {
      $str_num1 = (!$cnt)?0:($page-1)*$output_num+1;
      $str_num2 = ($page == $page_num) ? $cnt : $page*$output_num;
      if($cnt > 0) {
        return "全". $cnt."件中 <strong>".$str_num1."〜".$str_num2."件</strong>を表示しています";
      } else {
        return "全 0件";
      }
  }


  public function delete($id) {

    $result = $this->CI->Media_model->find($id);

    if(!empty($result)) {
      //削除処理
      $this->CI->Media_model->delete($id);

      return $this->delete_mediafile($result['filename']);
    }

    return false;
  }

  public function delete_mediafile($filename) {

    $filepath = realpath($this->getUploadDir()."/".$filename);
    if(file_exists($filepath)) {
      unlink($filepath);
        
      //thumbファイル
      $file_info = pathinfo($filepath);
      if(isset($file_info["extension"])){
        $file_ext = $file_info["extension"];
      }
      if(!empty($file_ext)) {
        $filename_thumb = str_replace(".".$file_ext , "_thumb".".".$file_ext , $filename);
        $filepath_thumb = realpath($this->getUploadDir()."/".$filename_thumb);
        if(file_exists($filepath_thumb)) {
          unlink($filepath_thumb);
        }
        //middleサイズ
        $filename_smp = str_replace(".".$file_ext , "_smp".".".$file_ext , $filename);
        $filepath_smp = realpath($this->getUploadDir()."/".$filename_smp);
        if(file_exists($filepath_smp)) {
          unlink($filepath_smp);
        }
        //product系の画像があればそれも削除
        foreach($this->prodimg_resize_setting as $setting) {
          $prod_resize_filename = str_replace(".".$file_ext , '_'.$setting['addname'].".".$file_ext , $filename);
          $filepath_prod = realpath($this->getUploadDir()."/resize/".$setting['addname']."/".$prod_resize_filename);
          if(file_exists($filepath_prod)) {
            unlink($filepath_prod);
          }
        }

      }

      return true;
    }

    return false;

  }


  public function product_image_resize($filename) {

    $filepath = realpath($this->getUploadDir()."/".$filename);
    if(!file_exists($filepath)) {
      return false;
    }

    try{

      $this->CI->load->library('image_lib');
      $img_property =  $this->CI->image_lib->get_image_properties($filepath , true);
      $file_info = pathinfo($filepath);
      if(isset($file_info["extension"])){
        $file_ext = $file_info["extension"];
      }
      if(!empty($img_property['width']) && !empty($file_ext)) {

        foreach($this->prodimg_resize_setting as $setting) {
          $prod_resize_filename = str_replace(".".$file_ext , "_".$setting['addname'].".".$file_ext , $filename);
          $config = array(
            'source_image'  => $filepath,
            'new_image'     => $this->getUploadDir()."/resize/".$setting['addname']."/".$prod_resize_filename,
            'width'         => $setting['width'],
            'height'        => !empty($setting['height']) ? $setting['height'] : '',
            'maintain_ratio' => true,
            'master_dim'    => 'auto' //http://www.ci-guide.info/practical/library/image_lib/
          );
          $this->CI->image_lib->initialize($config);
          $this->CI->image_lib->resize();
          $this->CI->image_lib->clear();
        }

        return true;
      }

    } catch (Exception $e) {
    }

    return false;
  }


}
