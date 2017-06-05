<?php
/**
 * @package   	Egolt Framewrok
 * @link 		http://www.egolt.com
 * @copyright 	Copyright (C) Egolt - www.egolt.com
 * @author    	Soheil Novinfard
 * @license    	GNU/GPL 2
 */

// Check Joomla! Library and direct access
defined('_JEXEC') or die('Direct access denied!');

// Check Egolt Framework
defined('_EGOINC') or die('Egolt Framework not installed!');

class EGODateSolar
{
	var $_default_date;
	var $_default_format;
	var $weekstart;
	
	
	function __construct()
	{
		// parent::__construct();

		$this->_default_date	= date('Y-m-d h:i:s');
		$this->_default_format	= 'Y-m-d h:i:s';
		$this->weekstart = 0; // Saturday
	}
	
	public function getInfo()
	{
		$info = array();
		$info['name']	= 'solar';
		$info['title']	= 'Solar Hijri';
		$info['compat']	= '2.5,3';

		return $info;
	}
	
	public function show($date = null, $format  = null)
	{	
		if(!isset($format))
		{
			$format = $this->_default_format;
		}
		if(!isset($date))
		{
			$date = $this->_default_date;
		}

		$localdate =  $this->toLocal($date, $format);

		list($year, $month, $day) = explode('-', $localdate);
		
		$output = $format;
		
		$output = str_replace('Y', $year, $output);
		$output = str_replace('m', $month, $output);
		$output = str_replace('d', $day, $output);
		$output = str_replace('M', $this->getMonths($month,'short'), $output);
		$output = str_replace('F', $this->getMonths($month), $output);
		$output = str_replace('l', $this->getWeekday(date('N',strtotime($date))), $output);
		
		return $output;
	}
	
	public function toGre($date = null, $format  = null)
	{
		if (stripos($date, ':') === 0) 
		{
			$tmp = explode(' ', $date);
			$date = $tmp[0];
		}
		list($j_y, $j_m, $j_d) = explode('-', $date);
		
		if ( ! function_exists('div'))
		{
			function div($a,$b) {
				return (int) ($a / $b);
			}
		}
		$g_days_in_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
		$j_days_in_month = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);
		$jy = $j_y-979;
		$jm = $j_m-1;
		$jd = $j_d-1;
		$j_day_no = 365*$jy + div($jy, 33)*8 + div($jy%33+3, 4);
		for ($i=0; $i < $jm; ++$i)
			$j_day_no += $j_days_in_month[$i];
		$j_day_no += $jd;
		$g_day_no = $j_day_no+79;
		$gy = 1600 + 400*div($g_day_no, 146097); /* 146097 = 365*400 + 400/4 - 400/100 + 400/400 */
		$g_day_no = $g_day_no % 146097;
		$leap = true;
		if ($g_day_no >= 36525) /* 36525 = 365*100 + 100/4 */
		{
			$g_day_no--;
			$gy += 100*div($g_day_no,  36524); /* 36524 = 365*100 + 100/4 - 100/100 */
			$g_day_no = $g_day_no % 36524;
			if ($g_day_no >= 365)
				$g_day_no++;
			else
				$leap = false;
		}
		$gy += 4*div($g_day_no, 1461); /* 1461 = 365*4 + 4/4 */
		$g_day_no %= 1461;
		if ($g_day_no >= 366) {
			$leap = false;
			$g_day_no--;
			$gy += div($g_day_no, 365);
			$g_day_no = $g_day_no % 365;
		}
		for ($i = 0; $g_day_no >= $g_days_in_month[$i] + ($i == 1 && $leap); $i++)
		$g_day_no -= $g_days_in_month[$i] + ($i == 1 && $leap);
		$gm = $i+1;
		$gd = $g_day_no+1;
		// return array($gy, $gm, $gd);
		return $gy. '-' .$gm. '-' . $gd;
	}
	
	function toLocal ($date = null, $format  = null)
	{
		if (stripos($date, ':') === 0) 
		{
			$tmp = explode(' ', $date);
			$date = $tmp[0];
		}
		list($g_y, $g_m, $g_d) = explode('-', $date);
		
		if ( ! function_exists('div'))
		{
			function div($a,$b) {
				return (int) ($a / $b);
			}
		}

		$g_days_in_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
		$j_days_in_month = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);

		$gy = $g_y-1600;
		$gm = $g_m-1;
		$gd = $g_d-1;

		$g_day_no = 365*$gy+div($gy+3,4)-div($gy+99,100)+div($gy+399,400);

		for ($i=0; $i < $gm; ++$i)
			$g_day_no += $g_days_in_month[$i];

		if ($gm>1 && (($gy%4==0 && $gy%100!=0) || ($gy%400==0)))
		/* leap and after Feb */
		$g_day_no++;
		$g_day_no += $gd;
		$j_day_no = $g_day_no-79;
		$j_np = div($j_day_no, 12053); /* 12053 = 365*33 + 32/4 */
		$j_day_no = $j_day_no % 12053;
		$jy = 979+33*$j_np+4*div($j_day_no,1461); /* 1461 = 365*4 + 4/4 */
		$j_day_no %= 1461;
		
		if ($j_day_no >= 366) {
			$jy += div($j_day_no-1, 365);
			$j_day_no = ($j_day_no-1)%365;
		}   
		for ($i = 0; $i < 11 && $j_day_no >= $j_days_in_month[$i]; ++$i)
			$j_day_no -= $j_days_in_month[$i];

		$jm = $i+1;
		$jd = $j_day_no+1;

		// return array($jy, $jm, $jd);
		return $jy. '-' .$jm. '-' . $jd;
	}
	
	public function getMonths($select = null, $type = 'long') 
	{
		if(isset($select))
		{
			$select = (int) $select;
		}

		if($type == 'long')
		{
			$months = array(
				1 => EGOText::_('EGO_FAR_LG', array('d' => 'Farvardin', 'fa-IR' => 'فروردین') ),
				2 => EGOText::_('EGO_ORD_LG', array('d' => 'Ordibehesht', 'fa-IR' => 'اردیبهشت') ),
				3 => EGOText::_('EGO_KHO_LG', array('d' => 'Khordad', 'fa-IR' => 'خرداد') ),
				4 => EGOText::_('EGO_TIR_LG', array('d' => 'Tir', 'fa-IR' => 'تیر') ),
				5 => EGOText::_('EGO_MOR_LG', array('d' => 'Mordad', 'fa-IR' => 'مرداد') ),
				6 => EGOText::_('EGO_SHA_LG', array('d' => 'Shahrivar', 'fa-IR' => 'شهریور') ),
				7 => EGOText::_('EGO_MEH_LG', array('d' => 'Mehr', 'fa-IR' => 'مهر') ),
				8 => EGOText::_('EGO_ABA_LG', array('d' => 'Aban', 'fa-IR' => 'آبان') ),
				9 => EGOText::_('EGO_AZA_LG', array('d' => 'Azar', 'fa-IR' => 'آذر') ),
				10 => EGOText::_('EGO_DEY_LG', array('d' => 'Dey', 'fa-IR' => 'دی') ),
				11 => EGOText::_('EGO_BAH_LG', array('d' => 'Bahman', 'fa-IR' => 'بهمن') ),
				12 => EGOText::_('EGO_ESF_LG', array('d' => 'Esfand', 'fa-IR' => 'اسفند') ),
			);
		}
		elseif($type == 'short')
		{
			$months = array(
				1 => EGOText::_('EGO_FAR', array('d' => 'Far', 'fa-IR' => 'فر') ),
				2 => EGOText::_('EGO_ORD', array('d' => 'Ord', 'fa-IR' => 'ار') ),
				3 => EGOText::_('EGO_KHO', array('d' => 'Kho', 'fa-IR' => 'خر') ),
				4 => EGOText::_('EGO_TIR', array('d' => 'Tir', 'fa-IR' => 'تی') ),
				5 => EGOText::_('EGO_MOR', array('d' => 'Mor', 'fa-IR' => 'مر') ),
				6 => EGOText::_('EGO_SHA', array('d' => 'Sha', 'fa-IR' => 'شه') ),
				7 => EGOText::_('EGO_MEH', array('d' => 'Meh', 'fa-IR' => 'مه') ),
				8 => EGOText::_('EGO_ABA', array('d' => 'Aba', 'fa-IR' => 'آب') ),
				9 => EGOText::_('EGO_AZA', array('d' => 'Aza', 'fa-IR' => 'آذ') ),
				10 => EGOText::_('EGO_DEY', array('d' => 'Dey', 'fa-IR' => 'دی') ),
				11 => EGOText::_('EGO_BAH', array('d' => 'Bah', 'fa-IR' => 'به') ),
				12 => EGOText::_('EGO_ESF', array('d' => 'Esf', 'fa-IR' => 'اس') ),
			);
		}
		
		if(!isset($select))
			return $months;
		else
			return $months[$select];		
	}
	
	public function getWeekday($select = null, $type = 'long') 
	{
		if(isset($select))
		{
			$select = (int) $select;
		}

		if($type == 'long')
		{
			$week = array(
				1 => EGOText::_('EGO_WEEK_2SH_LG', array('d' => '2 Shanbe', 'fa-IR' => 'دوشنبه') ),
				2 => EGOText::_('EGO_WEEK_3SH_LG', array('d' => '3 Shanbe', 'fa-IR' => 'سه شنبه') ),
				3 => EGOText::_('EGO_WEEK_4SH_LG', array('d' => '4 Shanbe', 'fa-IR' => 'چهارشنبه') ),
				4 => EGOText::_('EGO_WEEK_5SH_LG', array('d' => '5 Shanbe', 'fa-IR' => 'پنجشنبه') ),
				5 => EGOText::_('EGO_WEEK_JOM_LG', array('d' => 'Jomee', 'fa-IR' => 'جمعه') ),
				6 => EGOText::_('EGO_WEEK_SH_LG', array('d' => 'Shanbe', 'fa-IR' => 'شنبه') ),
				7 => EGOText::_('EGO_WEEK_1SH_LG', array('d' => '1 Shanbe', 'fa-IR' => 'یکشنبه') ),
			);	
		}
		elseif($type == 'short')
		{
			$week = array(
				1 => EGOText::_('EGO_WEEK_2SH', array('d' => 'DO', 'fa-IR' => 'د') ),
				2 => EGOText::_('EGO_WEEK_3SH', array('d' => 'SE', 'fa-IR' => 'س') ),
				3 => EGOText::_('EGO_WEEK_4SH', array('d' => 'CH', 'fa-IR' => 'چ') ),
				4 => EGOText::_('EGO_WEEK_5SH', array('d' => 'PA', 'fa-IR' => 'پ') ),
				5 => EGOText::_('EGO_WEEK_JOM', array('d' => 'JO', 'fa-IR' => 'ج') ),
				6 => EGOText::_('EGO_WEEK_SH', array('d' => 'SH', 'fa-IR' => 'ش') ),
				7 => EGOText::_('EGO_WEEK_1SH', array('d' => 'YE', 'fa-IR' => 'ی') ),
			);	
		}
		
		if(!isset($select))
			return $week;
		else
			return $week[$select];
	}

	public function getMonthsdur($year, $month = null) 
	{
		$year = (int) $year;
		if(isset($month))
			$month = (int) $month;
			
		$months = array(
			1 => 31,
			2 => 31,
			3 => 31,
			4 => 31,
			5 => 31,
			6 => 31,
			7 => 30,
			8 => 30,
			9 => 30,
			10 => 30,
			11 => 30,
		);
		if($this->isKbs($year))
			$months[12] = 30;
		else
			$months[12] = 29;
		
		if(!isset($month))
			return $months;
		else
			return $months[$month];			
	}
	
	public function isKbs($year) 
	{
		if($year%4 == 3)
			return true;
		else
			return false;
	}
	
	public function getDurs($year, $month = null)
	{
		if($month == '')
			$month = null;
		if(!isset($month))
		{
			$lastday		= $this->getMonthsdur($year, 12);
			$output['start']= $this->toGre($year.'-01-01', 'Y-m-d');
			$output['end']	= $this->toGre($year.'-12-'.$lastday, 'Y-m-d');
		}
		else
		{
			$lastday		= $this->getMonthsdur($year, $month);
			$output['start']= $this->toGre($year.'-'.$month.'-01', 'Y-m-d');
			$output['end']	= $this->toGre($year.'-'.$month.'-'.$lastday, 'Y-m-d');
		}	
		return $output;
	}
}