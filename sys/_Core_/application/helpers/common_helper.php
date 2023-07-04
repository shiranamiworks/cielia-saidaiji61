<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * common helper
 *
 * ログ書き出し
 *
 * @access	public
 * @param	$data ログに書き出すデータ
 * @param	$add_str 追記メッセージ
 * @return	datetime
 */
if ( ! function_exists( 'logd' ) ) {
  //log_messageのラッパー
  //log_messageが長い上に引数の順序が非常に気に入らないので
  //短縮形のラッパを作った（デフォルトログレベルはdebug）
  //ついでに引数に配列、オブジェクトを渡せるようにした。
  //ついでに呼出元ファイル名、行番号、メソッド名を追記を可能にした

  function logd($data , $add_str='', $level='error' , $show_filename=true)
  {
    //配列、オブジェクトは自動展開する
    if(is_array($data) || is_object($data)){
      $space = "\n";
      $message = print_r($data,true) . $space . $add_str ;

    }else{
      $space = ' ';
      $message = $data . $space . $add_str ;
    }
    if($show_filename){
      $dbg = debug_backtrace();
      //呼出元ファイル名、行番号、メソッド名を追記
      $fname = ( isset($dbg[0]['file'] ) ) ? 'FILE:' . $dbg[0]['file'] : '';
      $line = ( isset($dbg[0]['line'] ) ) ? ' , LINE:' . $dbg[0]['line'] : '';
      $func = ( isset($dbg[1]['function'] ) ) ? ' , FUNCTION:' . $dbg[1]['function'] : '';
      $message = '[' . $fname . $line . $func . ']' . $space . $message ;
    }
    log_message($level , $message);
  }
}

// ------------------------------------------------------------------------

/*
 * print_r拡張
 *
 * @access	public
 * @param	$data
 * @return	void
 */
if ( ! function_exists( 'pr' ) ) {

	function pr($data) {
		echo "<pre>";
		print_r($data);
		echo "</pre>";
	}
}

/*
 * システムディレクトリを先頭にしたパスを返す
 *
 * @access	public
 * @param   string
 * @return	string
 */
if ( ! function_exists( 'system_path' ) ) {

	function system_path() {
		return SYSTEM_DIR;
	}
}

/**
 * QUERY_STRING を $_GETへ格納
 *
 * @access	public
 * @return	void
 */
if ( ! function_exists( 'setGETparam' ) ) {
	function setGETparam() {
		parse_str($_SERVER['QUERY_STRING'],$_GET);
		return;
	}
}

// シリアライズしつつ base64 エンコードをする関数
if ( ! function_exists( 'serialize_base64_encode' ) ) {
	function serialize_base64_encode($array) {
		$data = serialize($array);
		$data = base64_encode($data);
		return $data;
	}
}

// base64 デコードしつつシリアライズされたデータを復元する関数
if ( ! function_exists( 'unserialize_base64_decode' ) ) {
	function unserialize_base64_decode($data) {
		$data = base64_decode($data);
		$array = unserialize($data);
		return $array;
	}
}

/**
 * facebook_id から プロフィール画像URLを返す
 *
 * @access  public
 * @return  string
 */
if ( ! function_exists( 'getFbProfileUrl' ) ) {
  function getFbProfileUrl($facebook_id) {
    return "https://graph.facebook.com/".$facebook_id."/picture?type=normal";
  }
}

// エラーを表示してログを残す
if ( ! function_exists( 'fn_show_error' ) ) {
	function fn_show_error($message , $error_code = "") {
		if(!empty($error_code)) $error_code = "/".$error_code;
        logd($message.$error_code);
		show_error($message.$error_code , 500 );
        exit;
	}
}
if ( ! function_exists( 'fn_show_error_xml' ) ) {
	function fn_show_error_xml($message , $errcode = "" , $header_status = "") {

        logd($message."/".$errcode);
        $dom = new DOMDocument();
        $dom->encoding = 'UTF-8';
        $dom->formatOutput = true;
        $top_level = $dom->appendChild($dom->createElement('data'));
        $error = $top_level->appendChild($dom->createElement('error'));
        $error->appendChild($dom->createTextNode($message));
        if($errcode)
        {
       		$errorcode = $top_level->appendChild($dom->createElement('errorcode'));
        	$errorcode->appendChild($dom->createTextNode($errcode));
        }

        if($header_status)
        {
        	header($header_status);
        }
        header("Content-Type: text/xml; charset=utf-8");
        echo $dom->saveXML();
        exit;
	}
}
if ( ! function_exists( 'fn_show_error_json' ) ) {
	function fn_show_error_json($message , $errcode = "" , $header_status = "") {
        logd($message."/".$errcode);

        $output = array(
        		"error" => $message
        	);
        if($errcode)
        {
        	$output["errcode"] = $errcode;
        }

        if($header_status)
        {
        	header($header_status);
        }
        header( 'Content-Type: text/javascript; charset=utf-8' );
		echo json_encode($output);

        exit;
	}
}

/*
 * 消費税の計算
 *
 * @access	public
 * @param	number $price
 * @return	number $tax
 */
if ( ! function_exists( 'calc_tax' ) ) {

	function calc_tax($price) {

		//切捨て
		//return floor($price*0.05);
		//↓
		//消費税込み表示となったため、ここは0を返す
		return 0;
	}
}

/*
 * ページ数や件数計算
 *
 * @access	public
 * @param	int $total 総データ数
 * @param	int $page  現在のページ数
 * @param	int $page_limit ページ表示数
 * @return	object
 */
if ( ! function_exists( 'calc_pageinfo' ) ) {

	function calc_pageinfo($total , $page , $page_limit) {
		$return = array();

		if(!is_num($page)) {
			$page = 1;
		}
		$page_num = 0;
		if($total > 0){
			$page_num = floor((intval($total)-1)/intval($page_limit)) + 1;
		}
		if($page > $page_num)
		{
			$page = $page_num;
		}


		$cnt01 = ($page -1)*$page_limit + 1;
		$cnt02 = $page*$page_limit;

		if($page_num == $page) {
			$cnt02 = $total;
		}

		$return = array(
			"page_num" => $page_num,
			"page" => $page,
			"cnt01" => $cnt01,
			"cnt02" => $cnt02
		);

		return $return;

	}
}


if ( ! function_exists( 'is_num' ) ) {
  function is_num($val) {
      if (preg_match("/^[0-9]+$/",$val)) {
          return TRUE;
      } else {
          return FALSE;
      }
  }
}

/*
 * ページャ
 * @access	public
 * @param	string $link_url  リンクURLベース ([page]]page数が入る、[param] : パラメータが入る)
 * @param	int $page  現在のページ数
 * @param	int $total 総データ数
 * @param	int $page_limit ページ表示数
 * @param	string $param  検索文字列など
 * @param	int $cur_pager  ページリンクをある一定数分だけ表示する（前後10ページ分なら10、全部出すなら0）
 * @return	string
 */
if ( ! function_exists( 'fn_pagination' ) ) {

	function fn_pagination($link_url , $page , $total , $page_limit , $param="" , $cut_pager = 10 , $prev_page_txt = "前へ" , $next_page_txt = "次へ"){

		$exchange_page_str = "[page]";
		$exchange_param_str = "[param]";

		//総ページ数を算出
		if(!is_numeric($page))
		{
			$page = 1;
		}
		$page_num = 0;
		if($total > 0)
		{
			$page_num = floor(($total-1)/$page_limit) + 1;
		}
		if($page >= $page_num)
		{
			$page = $page_num;
		}

		$link_url = str_replace($exchange_param_str , $param , $link_url);
		//link_url内にpage= というパラメータがあれば消す
		$link_url = preg_replace('/page=[0-9]{1,}&/' ,'' , $link_url);

		$output_txt = "";

		if($page_num > 0){

			//$output_txt .= "<div class=\"pager-wrapper\">";
			$output_txt .= "<p class=\"pager-info\">".$total." 件中 / ".(($page-1)*$page_limit+1)."～".(($page != $page_num)?$page*$page_limit:$total)."件を表示</p>";
			$output_txt .= "<div class=\"pager\">";

			if($page == 1){
				$output_txt .= "<span class=\"prev\">".$prev_page_txt."</span>";
			}else{
				$output_txt .= "<a href=\"".str_replace($exchange_page_str , ($page-1) , $link_url)."\">".$prev_page_txt."</a>";
			}

			if($cut_pager*2+1 < $page_num){
				$output_txt .= "<span class=\"omit\">.....</span>";
			}

			for($i=1;$i<=$page_num;$i++){

				if($cut_pager && ($page-$cut_pager) <= $i && ($page+$cut_pager) >= $i) {

					if($i == $page){
						$output_txt .= "<span class=\"current\">".$i."</span>";
					}else{
						$output_txt .= "<a href=\"".str_replace($exchange_page_str , $i, $link_url)."\">".$i."</a>";
					}
				}
			}

			if($cut_pager*2+1 < $page_num){
				$output_txt .= "<span class=\"omit\">.....</span>";
			}

			if($page == $page_num){
				$output_txt .= "<span class=\"next\">".$next_page_txt."</span>";
			}else{
				$output_txt .= "<a href=\"".str_replace($exchange_page_str , ($page+1) , $link_url)."\">".$next_page_txt."</a>";
			}

			$output_txt .= "</div>";
			//$output_txt .= "</div>";

		}

		return $output_txt;
	}
}

/**
 * エスケープする
 */
if ( ! function_exists( 'fn_esc' ) ) {
	function fn_esc($data , $charset = 'UTF-8') {
		if (is_array($data)) {
			return array_map('fn_esc', $data);
		}

    if(is_bool($data)) {
      return $data;
    }

		return htmlspecialchars($data,ENT_QUOTES,$charset);
	}
}

/**
 * http接続ならhttpsにリダイレクトする
 */
if ( ! function_exists( 'fn_force_ssl' ) ) {
	function fn_force_ssl()
	{

		$CI =& get_instance();
		if ($CI->config->item('SECURE_CONTROL') && $_SERVER['SERVER_PORT'] != 443)
        {
			$CI->config->config['base_url'] = str_replace('http://', 'https://', $CI->config->config['base_url']);
			redirect($CI->uri->uri_string());
        }
	}
}
/**
 * https接続ならhttpにリダイレクトする
 * @param string request_url ジャンプ先
 */
if ( ! function_exists( 'fn_force_nonssl' ) ) {
	function fn_force_nonssl($request_url)
	{
		$CI =& get_instance();
		if ($CI->config->item('SECURE_CONTROL') && $_SERVER['SERVER_PORT'] == 443)
        {
			//URL指定がある場合(CIを絡まないリンク)
			if($request_url)
			{
				header('Location: http://'.$CI->config->config['domain'].$request_url);
			}
			else
			{
				redirect($CI->uri->uri_string());
			}
        }
	}
}

/**
 * 指定したデータ内の指定したKEYの値をURLパラメータ形式に変換
 * @param array $vars
 * @param array $keys
 * @param boolean $top_str (返却する文字列の最初に？を付けるかどうか)
 * @return string
 */
function fn_urlprm($vars , $keys = array() , $top_str=true)
{
    $param = "";
    if(!empty($vars) && is_array($vars)) {
      foreach($vars as $key => $value) {
        if((!empty($keys) && in_array($key , $keys) === true) || (empty($keys))) {
          if(!$param) {
              if($top_str) {
                  $param .= "?";
              }
          } else {
            $param .= "&";
          }
          $param .= $key."=".fn_esc($value);
        }
      }
    }

    /*
    if(is_array($keys) && is_array($vars))
    {
        foreach($keys as $key)
        {
            if(isset($vars[$key]))
            {
                if(!$param)
                {
                    if($top_str)
                    {
                        $param .= "?";
                    }
                }
                else
                {
                    $param .= "&";
                }
                $param .= $key."=".fn_esc($vars[$key]);
            }
        }
    }
    */

    return $param;
}

/**
 * bytelen
 * http://zombiebook.seesaa.net/article/33192046.html
 *
 * @param mixed $data
 * @retrn int $length
 */
function bytelen($data)
{
    return strlen(bin2hex($data)) / 2;
}

/**
 *  日付取得
 *
 *  @param  void
 *  @return string
 */
function fn_get_date(){
    return Date("Y-m-d H:i:s");
}

/**
 *  年月日表示変換
 *
 *  @param  string $date
 *  @return string
 */
function fn_dateFormat($date,$format = "Y年m月d日") {
    return date($format,strtotime($date));
}

/**
 *  年月日時間表示変換2
 *
 *  @param  string $date
 *  @return string
 */
function fn_dateTimeFormat($datetime,$format = "Y年m月d日 H:i:s") {
    return date($format,strtotime($datetime));
}

function fn_weekday_jp( $date ){
 $weekday = array( '日', '月', '火', '水', '木', '金', '土' );
 return $weekday[date( 'w',strtotime( $date ) )];
}

/**
 * 日付の比較(日付が対象とする日付より先かどうか）
 *
 * @param datetime $date1
 * @param datetime $date2 //対象とする日付
 */
function fn_dateComparison($date1,$date2){

    if(strtotime($date1) > strtotime($date2)){
        return true;
    }else{
        return false;
    }
}

/**
 * 指定した日付から現在日までの経過日数を返す
 *
 * @param datetime $date1//指定日
 * @param datetime $date2 
 */
function fn_dayDiff($date1, $date2 = '') {
    
    if(empty($date2)) $date2 = date('Y-m-d');
    // 日付をUNIXタイムスタンプに変換
    $timestamp1 = strtotime($date1);
    $timestamp2 = strtotime($date2);

    // 何秒離れているかを計算
    $seconddiff = abs($timestamp2 - $timestamp1);

    // 日数に変換
    $daydiff = $seconddiff / (60 * 60 * 24);

    // 戻り値
    return $daydiff;
}

function fn_br2nl($data) {
  return preg_replace('/<br[[:space:]]*\/?[[:space:]]*>/i' ,'' , $data);
}

//チェックボックスタイプで登録されたデータを配列に復元
function fn_convertChkboxValue($data) {

  $delimiter = (defined('CATEGORY_DATA_DELIMITER') ? CATEGORY_DATA_DELIMITER : ';');
  $arr = explode($delimiter , $data);
  //最後の値をチェック
  $values=array_values($arr);
  $end_val = end($values);
  if(empty($end_val)) {
    array_pop($arr);
  }
  
  return $arr;
}

/**
 * Javascriptコードを除去する
 * @param string $text
 * @return string
 */
function fn_strip_jscode($data)
{
    $data = preg_replace('/( )(on|On|oN|ON)(.{3,16}\=)/','__\2\3',$data);
    $data = preg_replace('/(script)/','__\1',$data);
    $data = preg_replace('/(alert)(.{3,16}\=)/','__\1',$data);
    $data = preg_replace('/(location)(.{3,16}\=)/','__\1',$data);

    return $data;
}


/**
 *  現在のページをセット
 *
 *  @param  int $pg  ページ値
 *  @return int
 */
function fn_valid_page($pg){

    if(is_num($pg)){
        $page = $pg;
        if($page < 1){
            $page = 1;
        }
    }else{
        $page = 1;
    }
    return $page;
}

/**
 *  ページ数を計算
 *
 *  @param  int $cnt  総ページ数
 *  @param  int $num  1ページあたりの表示数
 *  @return int
 */
function fn_getPages($cnt,$num=10){

    if(is_numeric($cnt) && is_numeric($num))
    {
        if($cnt == 0) {
            return 0;
        }else{
          if($num === 0) return 0;
            $page_num = floor(($cnt-1)/$num) + 1;
            return $page_num;
        }
    }
    return 0;
}


/*
 * 外部サーバー上のファイルの有無確認
 * @access	public
 * @param	string $url
 * @return	boolean
 */
if ( ! function_exists( 'fn_url_exists' ) ) {
	function fn_url_exists($url) {
	    // Version 4.x supported
	    $handle   = curl_init($url);
	    if (false === $handle)
	    {
		return false;
	    }
	    curl_setopt($handle, CURLOPT_HEADER, false);
	    curl_setopt($handle, CURLOPT_FAILONERROR, true);  // this works
	    curl_setopt($handle, CURLOPT_HTTPHEADER, Array("User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.15) Gecko/20080623 Firefox/2.0.0.15") ); // request as if Firefox
	    curl_setopt($handle, CURLOPT_NOBODY, true);
	    curl_setopt($handle, CURLOPT_RETURNTRANSFER, false);
	    $connectable = curl_exec($handle);
	    curl_close($handle);
	    return $connectable;
	}
}


/**
 *  制限文字数に合わせて切り取り
 *
 *  @param  string $string
 *  @param  int $length
 *  @return string
 */
if ( ! function_exists( 'fn_cutstr' ) ) {
  function fn_cutstr($string , $length = 100){

    return mb_strimwidth($string, 0, $length, '…', 'UTF-8');
  }
}

/**
 *  制限文字数に合わせて切り取り(FAQ一覧用)
 *
 *  @param  string $string
 *  @param  int $length
 *  @return string
 */
if ( ! function_exists( 'fn_cutstr_faq' ) ) {
  function fn_cutstr_faq($string , $length = 100){
    return fn_cutstr($string , $length);
  }
}

/**
 *  公開・表示フラグ（output_flag）値を文字列化
 *
 *  @param  boolean $output
 *  @param  string $output_str
 *  @return string
 */
if ( ! function_exists( 'fn_output_flag_str' ) ) {
  function fn_output_flag_str($output , $output_str = '表示'){

    return ($output == 1 ? $output_str.'' : $output_str.'しない');
  }
}

/*
 * 文字コード判定
 */
function detect_encoding_ja( $str )
{
	$enc = @mb_detect_encoding( $str, 'ASCII,JIS,eucJP-win,SJIS-win,UTF-8' );

	switch ( $enc ) {
	case FALSE   :
	case 'ASCII' :
	case 'JIS'   :
	case 'UTF-8' : break;
	case 'eucJP-win' :
		// ここで eucJP-win を検出した場合、eucJP-win として判定
		if ( @mb_detect_encoding( $str, 'SJIS-win,UTF-8,eucJP-win' ) === 'eucJP-win' ) {
			break;
		}
		$_hint = "\xbf\xfd" . $str; // "\xbf\xfd" : EUC-JP "雀"

		// EUC-JP -> UTF-8 変換時にマッピングが変更される文字を削除( ≒ ≡ ∫ など)
		mb_regex_encoding( 'EUC-JP' );
		$_hint = mb_ereg_replace( "\xad(?:\xe2|\xf5|\xf6|\xf7|\xfa|\xfb|\xfc|\xf0|\xf1|\xf2)", '', $_hint );

		$_tmp  = mb_convert_encoding( $_hint, 'UTF-8', 'eucJP-win' );
		$_tmp2 = mb_convert_encoding( $_tmp,  'eucJP-win', 'UTF-8' );
		if ( $_tmp2 === $_hint ) {

			// 例外処理( EUC-JP 以外と認識する範囲 )
			if (
				// SJIS と重なる範囲(2バイト|3バイト|iモード絵文字|1バイト文字)
				! preg_match( '/^(?:'
					. '[\x8E\xE0-\xE9][\x80-\xFC]|\xEA[\x80-\xA4]|'
					. '\x8F[\xB0-\xEF][\xE0-\xEF][\x40-\x7F]|'
					. '\xF8[\x9F-\xFC]|\xF9[\x40-\x49\x50-\x52\x55-\x57\x5B-\x5E\x72-\x7E\x80-\xB0\xB1-\xFC]|'
					. '[\x00-\x7E]'
					. ')+$/', $str ) &&

				// UTF-8 と重なる範囲(全角英数字|漢字|1バイト文字)
				! preg_match( '/^(?:'
					. '\xEF\xBC[\xA1-\xBA]|[\x00-\x7E]|'
					. '[\xE4-\xE9][\x8E-\x8F\xA1-\xBF][\x8F\xA0-\xEF]|'
					. '[\x00-\x7E]'
					. ')+$/', $str )
			) {
				// 条件式の範囲に入らなかった場合は、eucJP-win として検出
				break;
			}
			// 例外処理2(一部の頻度の多そうな熟語は eucJP-win として判定)
			// (珈琲|琥珀|瑪瑙|癇癪|碼碯|耄碌|膀胱|蒟蒻|薔薇|蜻蛉)
			if ( mb_ereg( '^(?:'
				. '\xE0\xDD\xE0\xEA|\xE0\xE8\xE0\xE1|\xE0\xF5\xE0\xEF|\xE1\xF2\xE1\xFB|'
				. '\xE2\xFB\xE2\xF5|\xE6\xCE\xE2\xF1|\xE7\xAF\xE6\xF9|\xE8\xE7\xE8\xEA|'
				. '\xE9\xAC\xE9\xAF|\xE9\xF1\xE9\xD9|[\x00-\x7E]'
				. ')+$', $str )
			) {
				break;
			}
		}

	default :
		// ここで SJIS-win と判断された場合は、文字コードは SJIS-win として判定
		$enc = @mb_detect_encoding( $str, 'UTF-8,SJIS-win' );
		if ( $enc === 'SJIS-win' ) {
			break;
		}
		// デフォルトとして SJIS-win を設定
		$enc   = 'SJIS-win';

		$_hint = "\xe9\x9b\x80" . $str; // "\xe9\x9b\x80" : UTF-8 "雀"

		// 変換時にマッピングが変更される文字を調整
		mb_regex_encoding( 'UTF-8' );
		$_hint = mb_ereg_replace( "\xe3\x80\x9c", "\xef\xbd\x9e", $_hint );
		$_hint = mb_ereg_replace( "\xe2\x88\x92", "\xe3\x83\xbc", $_hint );
		$_hint = mb_ereg_replace( "\xe2\x80\x96", "\xe2\x88\xa5", $_hint );

		$_tmp  = mb_convert_encoding( $_hint, 'SJIS-win', 'UTF-8' );
		$_tmp2 = mb_convert_encoding( $_tmp,  'UTF-8', 'SJIS-win' );

		if ( $_tmp2 === $_hint ) {
			$enc = 'UTF-8';
		}
		// UTF-8 と SJIS 2文字が重なる範囲への対処(SJIS を優先)
		if ( preg_match( '/^(?:[\xE4-\xE9][\x80-\xBF][\x80-\x9F][\x00-\x7F])+/', $str ) ) {
			$enc = 'SJIS-win';
		}
	}
	return $enc;
}


/**
 * ベースURLをセキュア化した(https)アドレスを返す
 */
if ( ! function_exists( 'fn_url_secure' ) )
{
	function fn_url_secure($path="")
	{

		$CI =& get_instance();
		$CI->load->helper('url');
		$base_url = site_url($path);
		if($CI->config->item('SECURE_CONTROL'))
		{
			return str_replace("http","https",$base_url);
		}

		return $base_url;
	}
}


if ( ! function_exists( 'fn_val' ) )
{
  function fn_val($val)
  {
    return isset($val) ? $val : '';
  }
}


/**
 * 整数値かチェック
 */
if ( ! function_exists( 'fn_check_int' ) )
{
	function fn_check_int($val)
	{
		return ctype_digit($val);
	}
}

/**
 * 管理画面のステータスUPDATEのajax処理で送信するtoken値
 */
if ( ! function_exists( 'fn_ajaxpost_token_encode' ) )
{
	function fn_ajaxpost_token_encode($val)
	{
		//9999を追加
		$val = intval($val)+9999;
		//16進数化
		return base_convert ($val , 10 , 16);
	}
}
if ( ! function_exists( 'fn_ajaxpost_token_decode' ) )
{
	function fn_ajaxpost_token_decode($val)
	{
		//10進数化
		$val = base_convert ($val , 16 , 10);
		//9999を引く
		$val = intval($val)-9999;

		return $val;
	}
}

/*
 * 半角/全角スペースのみの場合にエラーとするチェック関数
 */
function fn_check_space_only($str)
{
	if( mb_ereg_match("^(\s|　)+$", $str) ){
		return false;
	}else{
		return true;
	}
}

function implode_assoc($inner_glue,$outer_glue,$array,$skip_empty=false)
{
  $output=array();
  foreach ($array as $key => $item) {
    if (!$skip_empty || $item) {
      $output[] = $key. $inner_glue. $item;
    }
  }
  return implode($outer_glue, $output);
};

/* End of file common_helper.php */