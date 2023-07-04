<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
* CSVReader Class
*/
class Csvreader {

	var $fields;            /** columns names retrieved after parsing */
	var $separator = ',';    /** separator used to explode each line */
	var $enclosure = '"';    /** enclosure used to decorate each field */

	var $max_row_size = 4096;    /** maximum row size to be used for decoding */

	/* get_csv ------------------------------------------------------------------*/
	//@param $file=ファイル名
	//@param $table=テーブル名
	//@param $code=文字コード
	//@return array(array())
	function parse_file($file,$table,$code,$csv_code){
		if(file_exists($file) == FALSE){
			$data = 'Error: There is no file.';
		}else{
			$data_file = fopen($file,'r');
			$data = $this->feof_csv($data_file,$table,$code,$csv_code);
			fclose($data_file);
		}
		return $data;
	}

	/* feof_csv ------------------------------------------------------------------*/
	//@param $data_file
	//@param $table
	//@param $code
	//@param $deli
	//@return array()
	function feof_csv($data_file,$table,$code,$csv_code){

		$deli = $this->separator;

		if(feof($data_file) == FALSE){
			$data = array();
			while(($data_row = $this->fgetcsv_reg($data_file,null,$deli)) != FALSE){
				$table_count = count($data_row);
				for($i=0; $i<$table_count; $i++){
					//$data_row[$i] = $this->fn_sanitize($data_row[$i]);
					if(!empty($table[$i]))
					{
						$data_list[$table[$i]] = $this->convert_encoding($data_row[$i],$code,$csv_code);
					}
				}
				$data[] = $data_list;
			}
		}else{
			$data = 'Error: The file pointer doesn\'t reach the file terminal.';
		}
		return $data;
	}

	/* fgetcsv_reg ------------------------------------------------------------------*/
	//@param $handle
	//@param $length
	//@param $d
	//@param $e
	//@return array()
	/*
	function fgetcsv_reg (&$handle, $length = null, $d , $e = '"') {
		//$d = preg_quote($d);
		$e = preg_quote($e);
		$_line = "";
		$eof = false;
		while ($eof != true) {
			$_line .= (empty($length) ? fgets($handle) : fgets($handle, $length));
			$itemcnt = preg_match_all('/'.$e.'/', $_line, $dummy);
			if ($itemcnt % 2 == 0) $eof = true;
		}
		$_csv_line = preg_replace("/(?:\r\n|[\r\n])?$/", $d, trim($_line));
		$_csv_pattern = '/('.$e.'[^'.$e.']*(?:'.$e.$e.'[^'.$e.']*)*'.$e.'|[^'.$d.']*)'.$d.'/';
		preg_match_all($_csv_pattern, $_csv_line, $_csv_matches);
		$_csv_data = $_csv_matches[1];
		for($_csv_i=0;$_csv_i<count($_csv_data);$_csv_i++){
			$_csv_data[$_csv_i]=preg_replace('/^'.$e.'(.*)'.$e.'$/s','$1',$_csv_data[$_csv_i]);
			$_csv_data[$_csv_i]=str_replace($e.$e, $e, $_csv_data[$_csv_i]);
		}
		return empty($_line) ? false : $_csv_data;
	}
	*/
	function fgetcsv_reg (&$handle, $length = null, $d = ',', $e = '"') {
		$d = preg_quote($d);
		$e = preg_quote($e);
		$_line = "";
		$eof = false;
		while (($eof != true)and(!feof($handle))) {
		    $_line .= (empty($length) ? fgets($handle) : fgets($handle, $length));
		    $itemcnt = preg_match_all('/'.$e.'/', $_line, $dummy);
		    if ($itemcnt % 2 == 0) $eof = true;
		}
		$_csv_line = preg_replace('/(?:\\r\\n|[\\r\\n])?$/', $d, trim($_line));
		$_csv_pattern = '/('.$e.'[^'.$e.']*(?:'.$e.$e.'[^'.$e.']*)*'.$e.'|[^'.$d.']*)'.$d.'/';
		preg_match_all($_csv_pattern, $_csv_line, $_csv_matches);
		$_csv_data = $_csv_matches[1];
		for($_csv_i=0;$_csv_i<count($_csv_data);$_csv_i++){
		    $_csv_data[$_csv_i]=preg_replace('/^'.$e.'(.*)'.$e.'$/s','$1',$_csv_data[$_csv_i]);
		    $_csv_data[$_csv_i]=str_replace($e.$e, $e, $_csv_data[$_csv_i]);
		}
		return empty($_line) ? false : $_csv_data;
	}



	/* convert_encoding ------------------------------------------------------------------*/
	//@param $data
	//@param $code
	//@return array
	function convert_encoding($data,$code1,$code2='auto'){
		return mb_convert_encoding($data,$code1,$code2);
	}

	/* fn_sanitize ------------------------------------------------------------------*/
	//@param $data
	//@return
	function fn_sanitize($data){
		if (is_array($data)) {
			return array_map('fn_sanitize', $data);
		}
		return htmlspecialchars($data,ENT_QUOTES);
	}

	/* create_csvfile_bylist------------------------------------------------------------------*/
	//@param $rs 項目内用(2次元配列を使用)
	//@param $filename 作成ファイル名
	function create_csvfile_bylistandhead($head,$arr,$filename,$code){
		header("Content-Disposition: attachment; filename=$filename");
		header("Content-Type: application/octet-stream");

		//項目内容出力
		$cnt=0;
		if(!is_null($head)){
			foreach($head as $value){
				if($cnt == 0){
					print("\"" . mb_convert_encoding($value,$code,"auto") . "\"");
					$cnt++;
				}else{
					print(",\"" . mb_convert_encoding($value,$code,"auto") . "\"");
					$cnt++;
				}
			}
			print("\n");
		}
		if(!is_null($arr)){
			foreach($arr as $rs_data){
				$cnt=0;
				foreach($rs_data as $key=>$value){
					if($cnt == 0){
						print("\"" . mb_convert_encoding($value,$code,"auto") . "\"");
						$cnt++;
					}elseif($key == $cnt){
						print(",\"" . mb_convert_encoding($value,$code,"auto") . "\"");
						$cnt++;
					}
				}
				print("\n");
			}
		}
	}

	/* csvheader書き出し------------------------------------------------------------------*/
	//@param $filename 作成ファイル名
	//@param $head ヘッダ
	function output_csvHeader($filename,$head,$charset) {
		header("Content-Disposition: attachment; filename=$filename");
		header("Content-Type: application/octet-stream");

		//項目内容出力
		$cnt=0;
		if(!is_null($head)){
			foreach($head as $value){
				if($cnt == 0){
					print("\"" . mb_convert_encoding($value,$charset,"auto") . "\"");
					$cnt++;
				}else{
					print(",\"" . mb_convert_encoding($value,$charset,"auto") . "\"");
					$cnt++;
				}
			}
			print("\n");
		}
	}

	/* キャッシュするように IE+SSL対策 */
	function output_csvHeader_Cache($filename,$head,$charset,$charset0="auto") {
		header("Cache-Control: public");
		header("Pragma: public");
		header("Content-Disposition: attachment; filename=$filename");
		header("Content-Type: application/octet-stream");

		//項目内容出力
		$cnt=0;
		if(!is_null($head)){
			foreach($head as $value){
				if($cnt == 0){
					print("\"" . mb_convert_encoding($value,$charset,$charset0) . "\"");
					$cnt++;
				}else{
					print(",\"" . mb_convert_encoding($value,$charset,$charset0) . "\"");
					$cnt++;
				}
			}
			print("\n");
		}
	}

	function output_csvBody($arr,$charset,$charset0="auto") {
		if(!is_null($arr)){
			$cnt=0;
			foreach($arr as $key=>$value){
				if($cnt == 0){
					print("\"" . mb_convert_encoding($value,$charset,$charset0) . "\"");
					$cnt++;
				}else{
					print(",\"" . mb_convert_encoding($value,$charset,$charset0) . "\"");
					$cnt++;
				}
			}
			print("\n");
		}
	}

	function get_csvBody($arr,$keys,$charset,$charset0) {
		$data = array();
		foreach($keys as $idx => $key){
			$data[$key] = mb_convert_encoding($arr[$idx], $charset, $charset0);
		}
		return $data;
	}
	
}

// END Pear loader Class

/* End of file Pear_loader.php */