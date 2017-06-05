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

// Include Helper
require_once(JPATH_SITE . '/libraries/ego/date/islamic-helper.php');

class EGODateIslamic
{
	var $_default_date;
	var $_default_format;
	var $weekstart;
	var $ihelper;
	
	function __construct()
	{
		// parent::__construct();

		$this->_default_date	= date('Y-m-d h:i:s');
		$this->_default_format	= 'Y-m-d h:i:s';
		$this->weekstart = 0; // Saturday
		$this->ihelper = new uCal;
	}
	
	public function getInfo()
	{
		$info = array();
		$info['name']	= 'islamic';
		$info['title']	= 'Islamic Calendar(Hijri)';
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
		
		$output = $this->ihelper->date($format, strtotime($date));
		
		return $output;
	}
	
	public function toGre($date = null, $format  = null)
	{
		if (stripos($date, ':') !== 0) 
		{
			$tmp = explode(' ', $date);
			$date = $tmp[0];
		}
		list($j_y, $j_m, $j_d) = explode('-', $date);
		$res = $this->ihelper->u2g($j_d, $j_m, $j_y);

		return $res['year']. '-' .$res['month']. '-' .$res['day'];
	}
	
	function toLocal ($date = null, $format  = null)
	{
		if (stripos($date, ':') !== 0) 
		{
			$tmp = explode(' ', $date);
			$date = $tmp[0];
		}
		list($g_y, $g_m, $g_d) = explode('-', $date);
		$res = $this->ihelper->g2u($g_d, $g_m, $g_y);

		return $res['year']. '-' .$res['month']. '-' .$res['day'];
	}
	
	public function getMonths($select = null, $type = 'long') 
	{
		if(isset($select))
		{
			$select = (int) $select;
		}

		if($type == 'long')
		{
			$months = $this->ihelper->uF['en'];
		}
		elseif($type == 'short')
		{
			$months = $this->ihelper->uM['en'];
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
			$l = $this->ihelper->l['ar'];
			$week = array(
				1 => $l[1],
				2 => $l[2],
				3 => $l[3],
				4 => $l[4],
				5 => $l[5],
				6 => $l[6],
				7 => $l[0],
			);	
		}
		elseif($type == 'short')
		{
			$week = array(
				1 => EGOText::_('EGO_WEEK_2SH', array('d'=>'Mon', 'fa'=>'د', 'ar'=>'اث') ),
				2 => EGOText::_('EGO_WEEK_3SH', array('d'=>'Tue', 'fa'=>'س', 'ar'=>'ثل') ),
				3 => EGOText::_('EGO_WEEK_4SH', array('d'=>'Wed', 'fa'=>'چ', 'ar'=>'ار') ),
				4 => EGOText::_('EGO_WEEK_5SH', array('d'=>'Thu', 'fa'=>'پ', 'ar'=>'خم') ),
				5 => EGOText::_('EGO_WEEK_JOM', array('d'=>'Fri', 'fa'=>'ج', 'ar'=>'جم') ),
				6 => EGOText::_('EGO_WEEK_SH', array('d'=>'Sat', 'fa'=>'ش', 'ar'=>'سب') ),
				7 => EGOText::_('EGO_WEEK_1SH', array('d'=>'Sun', 'fa'=>'ی', 'ar'=>'اح') ),
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
			
		// Get each month duration
		$months = array();
		for($j=1;$j<=12;$j++)
		{
			$tyear = $year;
			$tmonth = $j;
			$months[$j] = 28;
			for($i=28;$i<=30;$i++)
			{
				$testday_g = $this->ihelper->u2g($i, $tmonth, $tyear);
				$testday_u = $this->ihelper->g2u($testday_g['day'], $testday_g['month'], $testday_g['year']);
				if($i != $testday_u['day']) break;
				$months[$j] = $i;
			}
		}
		
		if(!isset($month))
			return $months;
		else
			return $months[$month];			
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