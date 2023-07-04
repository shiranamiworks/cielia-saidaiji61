<?php
/*
======================================================================
Project Name    : Mishima T.CMS  
 
Copyright © <2016> Teruhiko Mishima All rights reserved.
 
This source code or any portion thereof must not be  
reproduced or used in any manner whatsoever.
本ソースコードを無断で転用・転載することを禁じます。
======================================================================
*/

if (!defined('BASEPATH')) exit('No direct script access allowed');

class MY_Form_validation extends CI_Form_validation {

	/*
	 * 日付入力値チェック(YYYY-MM-DD形式かどうか)
	 */
	function valid_date_yymmdd($str) {

		if(preg_match('/^\d{4}-\d{2}-\d{2}/',$str)) {
			return TRUE;
		}else{
			$this->CI->form_validation->set_message('valid_date_yymmdd', '日付指定が正しくありません');
			return FALSE;
		}
	}
	/*
	 * 日付入力値チェック(YYYY-MM-DD HH:ii形式かどうか)
	 */
	function valid_date_yymmddhhii($str) {
		if(!preg_match('/^(\d\d\d\d)\/(\d\d)\/(\d\d) (\d\d):(\d\d):(\d\d)$/' , $str) && 
       !preg_match('/^(\d\d\d\d)\/(\d\d)\/(\d\d) (\d\d):(\d\d)$/' , $str) && 
       !preg_match('/^(\d\d\d\d)\-(\d\d)\-(\d\d) (\d\d):(\d\d):(\d\d)$/' , $str) && 
       !preg_match('/^(\d\d\d\d)\-(\d\d)\-(\d\d) (\d\d):(\d\d)$/' , $str))
    {
			$this->CI->form_validation->set_message('valid_date_yymmddhhii', '日付指定が正しくありません');
			return FALSE;
		} else {
			return TRUE;
		}
		
	}

	/*
	 * 2つの日付入力による期間チェック(日付1が日付2より過去の日付かをチェック)
	 */
	function valid_date_comparison($date1 , $date2_field) {
		$date2 = "";
		if(!empty($_POST[$date2_field])) {
			$date2 = $_POST[$date2_field];
			if(strtotime($date1) < strtotime($date2)) {
				return TRUE;
			}

			$this->CI->form_validation->set_message('valid_date_comparison', '日付の期間指定に問題があります');
			return FALSE;
		}
		return TRUE;
	}

	/*
	 * チェックボックスの選択数チェック
	 */
	function valid_checked_num($checked , $max_num) {
		if(is_array($checked) && !empty($checked)) {
			if(count($checked) > $max_num) {
				$this->CI->form_validation->set_message('valid_checkvalue_numeric', '選択数が制限よりもオーバーしています');
				return FALSE;
			}
		}
		
		return TRUE;
	}

	/**
	 * 全角 2バイト、半角1バイトで計算し、指定バイト数以下かどうかをチェック
	 */
	function valid_strbyte($str , $len)
	{
		$charset = $this->CI->config->item('charset');

		//1文字ずつ配列に
		$byte = 0;
		if($str)
		{
			//改行削除
			$str = preg_replace("/[\r\n]/", "", $str);

			while ($iLen = mb_strlen($str, $charset))
			{
				$tstr = mb_substr($str, 0, 1, $charset);
				//echo $tstr.":".$byte." / ";
				//半角か全角かを判定
				if (strlen($tstr) === mb_strlen($tstr, $charset)) {
					$tbyte = 1;
				}
				else
				{
					$tbyte = 2;
				}
				$byte += $tbyte;
				$str = mb_substr($str, 1, $iLen, $charset);
			}
		}


		if($byte <= $len)
		{
			return TRUE;
		}
		else
		{
			$this->CI->form_validation->set_message('valid_strbyte', '文字数がオーバーしています');
			return FALSE;
		}
	}

	/*
	 * 半角英数字記号
	 */
	function valid_alpha_numeric_symbol($str) {
		if(preg_match('/^[!-~]+$/',$str)) {
			return TRUE;
		}else{
			$this->CI->form_validation->set_message('valid_alpha_numeric_symbol', '半角英数字記号のみで入力して下さい');
			return FALSE;
		}
		
	}
}